<?php

use Livewire\Volt\Component;
use App\Models\Suggestion;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

new #[Title('Kritik dan Saran')] class extends Component {
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';

    public array $suggestionsData = [];
    public $suggestionsPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $categories = [];
    public array $statuses = [];
    public $suggestionIdToDelete = null;
    public $suggestionIdToRespond = null;
    public $showResponseModal = false;
    public $adminResponse = '';
    public $responseStatus = '';

    protected $listeners = [
        'confirm-delete-suggestion' => 'performDelete',
        'suggestion-submitted' => 'fetchData',
    ];

    public function mount()
    {
        $this->categories = [(object) ['name' => 'Kritik', 'value' => 'criticism', 'color' => 'yellow'], (object) ['name' => 'Saran', 'value' => 'suggestion', 'color' => 'blue']];

        $this->statuses = [(object) ['name' => 'Draft', 'value' => 'draft', 'color' => 'purple'], (object) ['name' => 'Pending', 'value' => 'pending', 'color' => 'gray'], (object) ['name' => 'Diproses', 'value' => 'in_progress', 'color' => 'blue'], (object) ['name' => 'Selesai', 'value' => 'resolved', 'color' => 'green'], (object) ['name' => 'Ditolak', 'value' => 'rejected', 'color' => 'red']];

        $this->fetchData();
    }

    public function fetchData()
    {
        $query = Suggestion::with('respondedBy');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $paginated = $query->latest()->paginate(10);

        $this->suggestionsData = $paginated->items();
        $this->suggestionsPagination = [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
        ];
    }

    public function openResponseModal($suggestionId)
    {
        $this->suggestionIdToRespond = $suggestionId;
        $suggestion = Suggestion::find($suggestionId);

        $this->adminResponse = $suggestion->admin_response ?? '';
        $this->responseStatus = $suggestion->status;
        $this->showResponseModal = true;
    }

    public function saveResponse()
    {
        $rules = [
            'adminResponse' => 'required|min:10',
            'responseStatus' => 'required',
        ];

        // when status is draft, no need to validate adminResponse
        if ($this->responseStatus === 'draft') {
            $rules['adminResponse'] = 'nullable';
        }

        $this->validate($rules, [
            'adminResponse.required' => 'Tanggapan wajib diisi',
            'adminResponse.min' => 'Tanggapan minimal 10 karakter',
            'responseStatus.required' => 'Status wajib dipilih',
        ]);

        try {
            $suggestion = Suggestion::findOrFail($this->suggestionIdToRespond);

            $suggestion->update([
                'admin_response' => $this->adminResponse,
                'status' => $this->responseStatus,
                'responded_by' => auth()->id(),
                'responded_at' => now(),
            ]);

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Tanggapan berhasil disimpan.',
            ]);

            $this->closeResponseModal();
            $this->fetchData();
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function closeResponseModal()
    {
        $this->showResponseModal = false;
        $this->suggestionIdToRespond = null;
        $this->adminResponse = '';
        $this->responseStatus = '';
        $this->resetValidation();
    }

    public function delete($suggestionId)
    {
        $this->suggestionIdToDelete = $suggestionId;
        $suggestion = Suggestion::find($suggestionId);

        $this->dispatch('show-confirm', [
            'type' => 'warning',
            'title' => 'Konfirmasi Hapus',
            'content' => 'Apakah Anda yakin ingin menghapus kritik/saran dari "' . $suggestion->name . '"? Tindakan ini tidak dapat dibatalkan.',
            'callback' => 'confirm-delete-suggestion',
        ]);
    }

    public function performDelete()
    {
        try {
            Suggestion::findOrFail($this->suggestionIdToDelete)->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Kritik/Saran berhasil dihapus.',
            ]);

            $this->fetchData();
            $this->suggestionIdToDelete = null;
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function getStatusBadgeColor($status)
    {
        return match ($status) {
            'draft' => 'purple',
            'pending' => 'gray',
            'in_progress' => 'blue',
            'resolved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function getStatusLabel($status)
    {
        return match ($status) {
            'draft' => 'Draft',
            'pending' => 'Pending',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            default => $status,
        };
    }

    public function getCategoryLabel($category)
    {
        return match ($category) {
            'criticism' => 'Kritik',
            'suggestion' => 'Saran',
            default => $category,
        };
    }

    public function getCategoryColor($category)
    {
        return match ($category) {
            'criticism' => 'yellow',
            'suggestion' => 'blue',
            default => 'gray',
        };
    }
}; ?>

<div>
    <!-- Header Page -->
    <x-app-header-page title="Kritik dan Saran" description="Kelola kritik dan saran dari masyarakat di sini."
        :breadcrumbs="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Kritik dan Saran']]">
    </x-app-header-page>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-6 mb-4">
        @php
            $totalSuggestions = Suggestion::count();
            $criticismCount = Suggestion::where('category', 'criticism')->count();
            $suggestionCount = Suggestion::where('category', 'suggestion')->count();
            $draftSuggestions = Suggestion::where('status', 'draft')->count();
            $pendingSuggestions = Suggestion::where('status', 'pending')->count();
            $resolvedSuggestions = Suggestion::where('status', 'resolved')->count();
        @endphp

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalSuggestions }}</p>
                </div>
                <div class="bg-gray-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600">Kritik</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $criticismCount }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600">Saran</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $suggestionCount }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600">Draft</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $draftSuggestions }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingSuggestions }}</p>
                </div>
                <div class="bg-gray-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600">Selesai</p>
                    <p class="text-2xl font-bold text-green-900">{{ $resolvedSuggestions }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row items-center justify-between mb-4 gap-2">
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
            <flux:input wire:model.debounce.500ms="search" type="text" placeholder="Cari kritik dan saran..."
                wire:keyup="fetchData" />
        </div>
    </div>

    <!-- Table Data -->
    <x-table-container>
        <x-slot:columns>
            <x-table.column>Pengirim</x-table.column>
            <x-table.column>Kategori</x-table.column>
            <x-table.column>Pesan</x-table.column>
            <x-table.column>Status</x-table.column>
            <x-table.column>Tanggal</x-table.column>
            <x-table.column>Ditanggapi Oleh</x-table.column>
            <x-table.column align="center">Aksi</x-table.column>
        </x-slot:columns>

        <x-slot:rows>
            @forelse ($suggestionsData as $suggestion)
                <x-table.row>
                    <x-table.cell>
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900">{{ $suggestion->name ?: 'Anonim' }}</span>
                            @if ($suggestion->email)
                                <span class="text-xs text-gray-500">{{ $suggestion->email }}</span>
                            @endif
                            @if ($suggestion->phone)
                                <span class="text-xs text-gray-500">{{ $suggestion->phone }}</span>
                            @endif
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        <span
                            class="text-xs px-2 py-1 rounded-md
                            {{ $suggestion->category === 'criticism' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $suggestion->category === 'suggestion' ? 'bg-blue-100 text-blue-800' : '' }}">
                            {{ $this->getCategoryLabel($suggestion->category) }}
                        </span>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="max-w-md">
                            <p class="text-sm text-gray-900 line-clamp-2">
                                {{ Str::limit($suggestion->message, 100) }}
                            </p>
                            @if ($suggestion->admin_response)
                                <div class="mt-2 p-2 bg-blue-50 rounded">
                                    <p class="text-xs font-semibold text-blue-900 mb-1">Tanggapan Admin:</p>
                                    <p class="text-xs text-blue-800 line-clamp-2">
                                        {{ Str::limit($suggestion->admin_response, 80) }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        <span
                            class="text-xs px-2 py-1 rounded-md
                            {{ $suggestion->status === 'draft' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $suggestion->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $suggestion->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $suggestion->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $suggestion->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $this->getStatusLabel($suggestion->status) }}
                        </span>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex flex-col">
                            <span class="text-sm">{{ $suggestion->created_at->format('d M Y') }}</span>
                            <span class="text-xs text-gray-500">{{ $suggestion->created_at->format('H:i') }}</span>
                            @if ($suggestion->responded_at)
                                <span class="text-xs text-green-600 mt-1">
                                    Ditanggapi: {{ $suggestion->responded_at->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        @if ($suggestion->respondedBy)
                            <div class="flex flex-col">
                                <span class="text-sm font-medium">{{ $suggestion->respondedBy->full_name }}</span>
                                <span class="text-xs text-gray-500">{{ $suggestion->respondedBy->email }}</span>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">Belum ditanggapi</span>
                        @endif
                    </x-table.cell>
                    <x-table.cell align="center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Respond -->
                            <flux:button icon="chat-bubble-left-right" size="xs" variant="filled"
                                wire:click="openResponseModal({{ $suggestion->id }})" title="Tanggapi" />

                            <!-- Delete -->
                            <flux:button icon="trash" size="xs" variant="danger"
                                wire:click="delete({{ $suggestion->id }})" title="Hapus" />
                        </div>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="7" align="center">Tidak ada data kritik dan saran</x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:rows>
    </x-table-container>

    <!-- Pagination -->
    <div class="mt-4">
        <x-simple-pagination :pagination="$suggestionsPagination" />
    </div>

    <!-- Response Modal -->
    <flux:modal name="suggestion-response-modal" class="lg:min-w-[700px] max-h-[90vh] overflow-y-auto"
        wire:model.self="showResponseModal" wire:close="closeResponseModal">
        <form wire:submit="saveResponse">
            <flux:heading size="lg">Tanggapi Kritik/Saran</flux:heading>

            @if ($suggestionIdToRespond)
                @php
                    $suggestion = Suggestion::find($suggestionIdToRespond);
                @endphp

                @if ($suggestion)
                    <!-- Suggestion Details -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <div class="space-y-2">
                            <div>
                                <span class="text-xs font-semibold text-gray-600">Pengirim:</span>
                                <span class="text-sm font-medium">{{ $suggestion->name ?: 'Anonim' }}</span>
                            </div>
                            @if ($suggestion->email || $suggestion->phone)
                                <div>
                                    <span class="text-xs font-semibold text-gray-600">Kontak:</span>
                                    <span class="text-sm">
                                        {{ $suggestion->email ?? '' }}
                                        {{ $suggestion->email && $suggestion->phone ? ' | ' : '' }}
                                        {{ $suggestion->phone ?? '' }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <span class="text-xs font-semibold text-gray-600">Kategori:</span>
                                <span
                                    class="text-xs px-2 py-1 rounded-md ml-2
                                    {{ $suggestion->category === 'criticism' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $suggestion->category === 'suggestion' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ $this->getCategoryLabel($suggestion->category) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-600">Tanggal:</span>
                                <span class="text-sm">{{ $suggestion->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-600">Pesan:</span>
                                <p class="text-sm mt-1 whitespace-pre-wrap">{{ $suggestion->message }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="space-y-4 mt-6">
                <!-- Status -->
                <div>
                    <flux:field>
                        <flux:label>Status <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model.live="responseStatus" placeholder="Pilih status">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}">{{ $status->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="responseStatus" />
                    </flux:field>
                </div>

                <!-- Admin Response -->
                <div>
                    <flux:field>
                        <flux:label>Tanggapan
                            @if ($responseStatus !== 'draft')
                                <span class="text-red-500">*</span>
                            @endif
                        </flux:label>
                        <flux:textarea wire:model="adminResponse" rows="6"
                            placeholder="Masukkan tanggapan Anda terhadap kritik/saran ini..." />
                        <flux:description>
                            Minimal 10 karakter. Berikan tanggapan yang jelas dan membantu.
                        </flux:description>
                        <flux:error name="adminResponse" />
                    </flux:field>
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <flux:spacer />
                <flux:button variant="ghost" wire:click="closeResponseModal" type="button">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Simpan Tanggapan
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
