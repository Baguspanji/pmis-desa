<?php

use Livewire\Volt\Component;
use App\Models\News;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $showModal = false;
    public $isEdit = false;
    public $newsId = null;

    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $content = '';
    public $image = null;
    public $existingImage = null;
    public $category = '';
    public $is_featured = false;
    public $is_published = false;
    public $published_at = null;

    public array $categories = [];

    protected $listeners = [
        'open-news-form' => 'openModal',
    ];

    public function mount()
    {
        $this->categories = [
            'Kesehatan',
            'Wisata',
            'Pariwisata',
            'Pendidikan',
            'Ekonomi',
            'Sosial',
            'Budaya',
            'Pembangunan',
        ];
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:news,slug,' . ($this->newsId ?? 'NULL'),
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => $this->isEdit ? 'nullable|image|max:2048' : 'nullable|image|max:2048',
            'category' => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Judul wajib diisi',
            'title.max' => 'Judul maksimal 255 karakter',
            'slug.required' => 'Slug wajib diisi',
            'slug.unique' => 'Slug sudah digunakan',
            'excerpt.max' => 'Ringkasan maksimal 500 karakter',
            'content.required' => 'Konten wajib diisi',
            'image.image' => 'File harus berupa gambar',
            'image.max' => 'Ukuran gambar maksimal 2MB',
            'category.max' => 'Kategori maksimal 100 karakter',
            'published_at.date' => 'Tanggal publikasi harus berupa tanggal yang valid',
        ];
    }

    public function openModal($newsId = null)
    {
        $this->resetValidation();
        $this->reset(['title', 'slug', 'excerpt', 'content', 'image', 'existingImage', 'category', 'is_featured', 'is_published', 'published_at']);

        if ($newsId) {
            $this->isEdit = true;
            $this->newsId = $newsId;
            $this->loadNews();
        } else {
            $this->isEdit = false;
            $this->newsId = null;
            $this->published_at = now()->format('Y-m-d\TH:i');
        }

        $this->showModal = true;
    }

    public function loadNews()
    {
        $news = News::findOrFail($this->newsId);

        $this->title = $news->title;
        $this->slug = $news->slug;
        $this->excerpt = $news->excerpt;
        $this->content = $news->content;
        $this->existingImage = $news->image;
        $this->category = $news->category;
        $this->is_featured = $news->is_featured;
        $this->is_published = $news->is_published;
        $this->published_at = $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : null;
    }

    public function updatedTitle($value)
    {
        if (!$this->isEdit || empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'title' => $this->title,
                'slug' => $this->slug,
                'excerpt' => $this->excerpt,
                'content' => $this->content,
                'category' => $this->category,
                'is_featured' => $this->is_featured,
                'is_published' => $this->is_published,
                'published_at' => $this->published_at ? \Carbon\Carbon::parse($this->published_at) : null,
            ];

            // Handle image upload
            if ($this->image) {
                // Delete old image if exists
                if ($this->isEdit && $this->existingImage && \Storage::exists('public/' . $this->existingImage)) {
                    \Storage::delete('public/' . $this->existingImage);
                }

                $data['image'] = $this->image->store('news', 'public');
            }

            if ($this->isEdit) {
                $news = News::findOrFail($this->newsId);
                $news->update($data);
                $message = 'Berita berhasil diperbarui.';
            } else {
                $data['created_by'] = auth()->id();
                News::create($data);
                $message = 'Berita berhasil ditambahkan.';
            }

            $this->dispatch('news-saved');
            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => $message,
            ]);

            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function removeImage()
    {
        if ($this->isEdit && $this->existingImage) {
            if (\Storage::exists('public/' . $this->existingImage)) {
                \Storage::delete('public/' . $this->existingImage);
            }

            $news = News::findOrFail($this->newsId);
            $news->update(['image' => null]);

            $this->existingImage = null;

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Gambar berhasil dihapus.',
            ]);
        }

        $this->image = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['isEdit', 'newsId', 'title', 'slug', 'excerpt', 'content', 'image', 'existingImage', 'category', 'is_featured', 'is_published', 'published_at']);
        $this->resetValidation();
    }
}; ?>

<div>
    <flux:modal name="news-form-modal" class="lg:min-w-[900px] max-h-[90vh] overflow-y-auto" wire:model.self="showModal"
        wire:close="closeModal">
        <form wire:submit="save">
            <flux:heading size="lg">{{ $isEdit ? 'Edit Berita' : 'Tambah Berita' }}</flux:heading>

            <div class="space-y-4 mt-6">
                <!-- Title -->
                <div>
                    <flux:field>
                        <flux:label>Judul <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model.live="title" type="text" placeholder="Masukkan judul berita" />
                        <flux:error name="title" />
                    </flux:field>
                </div>

                <!-- Slug -->
                <div>
                    <flux:field>
                        <flux:label>Slug <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="slug" type="text" placeholder="slug-berita" />
                        <flux:description>
                            URL-friendly version dari judul. Akan otomatis dibuat dari judul.
                        </flux:description>
                        <flux:error name="slug" />
                    </flux:field>
                </div>

                <!-- Category -->
                <div>
                    <flux:field>
                        <flux:label>Kategori</flux:label>
                        <flux:select wire:model="category" placeholder="Pilih kategori">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="category" />
                    </flux:field>
                </div>

                <!-- Excerpt -->
                <div>
                    <flux:field>
                        <flux:label>Ringkasan</flux:label>
                        <flux:textarea wire:model="excerpt" rows="3"
                            placeholder="Masukkan ringkasan singkat berita (opsional)" />
                        <flux:description>
                            Ringkasan singkat yang akan ditampilkan di daftar berita. Maks 500 karakter.
                        </flux:description>
                        <flux:error name="excerpt" />
                    </flux:field>
                </div>

                <!-- Content -->
                <div>
                    <flux:field>
                        <flux:label>Konten <span class="text-red-500">*</span></flux:label>
                        <flux:textarea wire:model="content" rows="10"
                            placeholder="Masukkan konten lengkap berita" />
                        <flux:description>
                            Konten lengkap berita yang akan ditampilkan.
                        </flux:description>
                        <flux:error name="content" />
                    </flux:field>
                </div>

                <!-- Image Upload -->
                <div>
                    <flux:field>
                        <flux:label>Gambar Utama</flux:label>

                        @if ($existingImage && !$image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $existingImage) }}" alt="Existing Image"
                                    class="w-48 h-32 object-cover rounded">
                                <flux:button type="button" size="sm" variant="danger" class="mt-2"
                                    wire:click="removeImage">
                                    Hapus Gambar
                                </flux:button>
                            </div>
                        @endif

                        @if ($image)
                            <div class="mb-2">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                    class="w-48 h-32 object-cover rounded">
                                <flux:button type="button" size="sm" variant="ghost" class="mt-2"
                                    wire:click="removeImage">
                                    Batalkan
                                </flux:button>
                            </div>
                        @endif

                        <flux:input type="file" wire:model="image" accept="image/*" />
                        <flux:description>
                            Format: JPG, PNG, GIF. Maksimal 2MB.
                        </flux:description>
                        <flux:error name="image" />

                        @if ($image)
                            <div wire:loading wire:target="image" class="text-sm text-blue-600 mt-1">
                                Mengupload...
                            </div>
                        @endif
                    </flux:field>
                </div>

                <!-- Published At -->
                <div>
                    <flux:field>
                        <flux:label>Tanggal & Waktu Publikasi</flux:label>
                        <flux:input wire:model="published_at" type="datetime-local" />
                        <flux:description>
                            Tentukan kapan berita ini akan dipublikasikan.
                        </flux:description>
                        <flux:error name="published_at" />
                    </flux:field>
                </div>

                <!-- Checkboxes -->
                <div class="space-y-3">
                    <div>
                        <flux:checkbox wire:model="is_published" label="Publikasikan Sekarang" />
                        <flux:description>
                            Centang untuk mempublikasikan berita ini. Jika tidak, berita akan disimpan sebagai draft.
                        </flux:description>
                    </div>

                    <div>
                        <flux:checkbox wire:model="is_featured" label="Tandai sebagai Unggulan" />
                        <flux:description>
                            Berita unggulan akan ditampilkan di banner utama halaman berita.
                        </flux:description>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="closeModal" type="button">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $isEdit ? 'Perbarui' : 'Simpan' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
