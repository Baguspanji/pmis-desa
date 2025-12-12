<?php

use Livewire\Volt\Component;
use App\Models\News;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

new #[Title('Berita & Acara')] class extends Component {
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';

    public array $newsData = [];
    public $newsPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $categories = [];
    public array $statuses = [];
    public $newsIdToDelete = null;

    protected $listeners = [
        'news-saved' => 'fetchData',
        'confirm-delete-news' => 'performDelete',
    ];

    public function mount()
    {
        $this->categories = [
            (object) ['name' => 'Kesehatan', 'value' => 'Kesehatan'],
            (object) ['name' => 'Wisata', 'value' => 'Wisata'],
            (object) ['name' => 'Pariwisata', 'value' => 'Pariwisata'],
            (object) ['name' => 'Pendidikan', 'value' => 'Pendidikan'],
            (object) ['name' => 'Ekonomi', 'value' => 'Ekonomi'],
            (object) ['name' => 'Sosial', 'value' => 'Sosial'],
            (object) ['name' => 'Budaya', 'value' => 'Budaya'],
            (object) ['name' => 'Pembangunan', 'value' => 'Pembangunan'],
        ];

        $this->statuses = [
            (object) ['name' => 'Dipublikasikan', 'value' => 'published'],
            (object) ['name' => 'Draft', 'value' => 'draft'],
        ];

        $this->fetchData();
    }

    public function fetchData()
    {
        $query = News::with('creator');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                    ->orWhere('category', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->statusFilter) {
            if ($this->statusFilter === 'published') {
                $query->where('is_published', true);
            } elseif ($this->statusFilter === 'draft') {
                $query->where('is_published', false);
            }
        }

        $paginated = $query->latest()->paginate(10);

        $this->newsData = $paginated->items();
        $this->newsPagination = [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
        ];
    }

    public function create()
    {
        $this->dispatch('open-news-form');
    }

    public function edit($newsId)
    {
        $this->dispatch('open-news-form', newsId: $newsId);
    }

    public function delete($newsId)
    {
        $this->newsIdToDelete = $newsId;
        $news = News::find($newsId);

        $this->dispatch('show-confirm', [
            'type' => 'warning',
            'title' => 'Konfirmasi Hapus',
            'content' => 'Apakah Anda yakin ingin menghapus berita "' . $news->title . '"? Tindakan ini tidak dapat dibatalkan.',
            'callback' => 'confirm-delete-news',
        ]);
    }

    public function performDelete()
    {
        try {
            $news = News::findOrFail($this->newsIdToDelete);

            // Delete image if exists
            if ($news->image && \Storage::exists('public/' . $news->image)) {
                \Storage::delete('public/' . $news->image);
            }

            $news->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Berita berhasil dihapus.',
            ]);

            $this->fetchData();
            $this->newsIdToDelete = null;
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function togglePublish($newsId)
    {
        try {
            $news = News::findOrFail($newsId);
            $news->is_published = !$news->is_published;

            if ($news->is_published && !$news->published_at) {
                $news->published_at = now();
            }

            $news->save();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => $news->is_published ? 'Berita berhasil dipublikasikan.' : 'Berita berhasil disembunyikan.',
            ]);

            $this->fetchData();
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function toggleFeatured($newsId)
    {
        try {
            $news = News::findOrFail($newsId);
            $news->is_featured = !$news->is_featured;
            $news->save();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => $news->is_featured ? 'Berita ditandai sebagai unggulan.' : 'Berita dihapus dari unggulan.',
            ]);

            $this->fetchData();
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }
}; ?>

<div>
    <!-- Header Page -->
    <x-app-header-page title="Berita & Acara" description="Kelola berita dan acara desa Anda di sini." :breadcrumbs="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Berita & Acara']]">
        <x-slot:actions>
            <flux:button wire:click="create" variant="primary">
                Tambah Berita
            </flux:button>
        </x-slot:actions>
    </x-app-header-page>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row items-center justify-between mt-6 mb-4 gap-2">
        <div class="w-full md:w-1/4">
            <flux:select wire:model="categoryFilter" placeholder="Filter berdasarkan kategori" wire:change="fetchData">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->value }}">{{ $category->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full md:w-1/4">
            <flux:select wire:model="statusFilter" placeholder="Filter berdasarkan status" wire:change="fetchData">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full md:flex-1">
            <flux:input wire:model.debounce.500ms="search" type="text" placeholder="Cari berita..."
                wire:keyup="fetchData" />
        </div>
    </div>

    <!-- Table Data -->
    <x-table-container>
        <x-slot:columns>
            <x-table.column>Judul</x-table.column>
            <x-table.column>Kategori</x-table.column>
            <x-table.column>Status</x-table.column>
            <x-table.column>Publikasi</x-table.column>
            <x-table.column>Pembuat</x-table.column>
            <x-table.column align="center">Aksi</x-table.column>
        </x-slot:columns>

        <x-slot:rows>
            @forelse ($newsData as $news)
                <x-table.row>
                    <x-table.cell>
                        <div class="flex items-start gap-3">
                            @if ($news->image)
                                <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}"
                                    class="w-12 h-12 object-cover rounded">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-900 line-clamp-2">{{ $news->title }}</span>
                                    @if ($news->is_featured)
                                        <svg class="w-4 h-4 text-yellow-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 line-clamp-1">
                                    {{ $news->excerpt ?? Str::limit(strip_tags($news->content), 80) }}
                                </p>
                            </div>
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        @if ($news->category)
                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-md">
                                {{ $news->category }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex flex-col gap-1">
                            @if ($news->is_published)
                                <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-md w-fit">
                                    Dipublikasikan
                                </span>
                            @else
                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-md w-fit">
                                    Draft
                                </span>
                            @endif
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        @if ($news->published_at)
                            <div class="flex flex-col">
                                <span class="text-sm">{{ $news->published_at->format('d M Y') }}</span>
                                <span class="text-xs text-gray-500">{{ $news->published_at->format('H:i') }}</span>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">Belum dipublikasi</span>
                        @endif
                    </x-table.cell>
                    <x-table.cell>
                        @if ($news->creator)
                            <div class="flex flex-col">
                                <span class="text-sm font-medium">{{ $news->creator->full_name }}</span>
                                <span class="text-xs text-gray-500">{{ $news->creator->email }}</span>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </x-table.cell>
                    <x-table.cell align="center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Toggle Publish -->
                            <flux:button
                                :icon="$news->is_published ? 'eye-slash' : 'eye'"
                                size="xs"
                                variant="{{ $news->is_published ? 'ghost' : 'filled' }}"
                                wire:click="togglePublish({{ $news->id }})"
                                title="{{ $news->is_published ? 'Sembunyikan' : 'Publikasikan' }}" />

                            <!-- Toggle Featured -->
                            <flux:button
                                :icon="$news->is_featured ? 'star-solid' : 'star'"
                                size="xs"
                                variant="{{ $news->is_featured ? 'filled' : 'ghost' }}"
                                wire:click="toggleFeatured({{ $news->id }})"
                                title="{{ $news->is_featured ? 'Hapus dari unggulan' : 'Tandai unggulan' }}" />

                            <!-- Edit -->
                            <flux:button
                                icon="square-pen"
                                size="xs"
                                wire:click="edit({{ $news->id }})"
                                title="Edit" />

                            <!-- Delete -->
                            <flux:button
                                icon="trash"
                                size="xs"
                                variant="danger"
                                wire:click="delete({{ $news->id }})"
                                title="Hapus" />
                        </div>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="6" align="center">Tidak ada data berita</x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:rows>
    </x-table-container>

    <!-- Pagination -->
    <div class="mt-4">
        <x-simple-pagination :pagination="$newsPagination" />
    </div>

    <!-- Include News Form Modal -->
    @livewire('news.form')
</div>
