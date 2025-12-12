<?php

use Livewire\Volt\Component;
use App\Models\Complaint;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

new #[Title('Pengaduan Masyarakat')] class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public array $complaintsData = [];
    public $complaintsPagination = [
        'current_page' => 1,
        'last_page' => 1,
        'next_page_url' => null,
        'prev_page_url' => null,
    ];

    public array $statuses = [];
    public $complaintIdToDelete = null;
    public $complaintIdToRespond = null;
    public $showResponseModal = false;
    public $adminResponse = '';
    public $responseStatus = '';

    protected $listeners = [
        'confirm-delete-complaint' => 'performDelete',
    ];

    public function mount()
    {
        $this->statuses = [(object) ['name' => 'Pending', 'value' => 'pending', 'color' => 'gray'], (object) ['name' => 'Diproses', 'value' => 'in_progress', 'color' => 'blue'], (object) ['name' => 'Selesai', 'value' => 'resolved', 'color' => 'green'], (object) ['name' => 'Ditolak', 'value' => 'rejected', 'color' => 'red']];

        $this->fetchData();
    }

    public function fetchData()
    {
        $query = Complaint::with('respondedBy');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $paginated = $query->latest()->paginate(10);

        $this->complaintsData = $paginated->items();
        $this->complaintsPagination = [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'next_page_url' => $paginated->nextPageUrl(),
            'prev_page_url' => $paginated->previousPageUrl(),
        ];
    }

    public function openResponseModal($complaintId)
    {
        $this->complaintIdToRespond = $complaintId;
        $complaint = Complaint::find($complaintId);

        $this->adminResponse = $complaint->admin_response ?? '';
        $this->responseStatus = $complaint->status;
        $this->showResponseModal = true;
    }

    public function saveResponse()
    {
        $this->validate(
            [
                'adminResponse' => 'required|string|min:10',
                'responseStatus' => 'required|in:pending,in_progress,resolved,rejected',
            ],
            [
                'adminResponse.required' => 'Tanggapan wajib diisi',
                'adminResponse.min' => 'Tanggapan minimal 10 karakter',
                'responseStatus.required' => 'Status wajib dipilih',
            ],
        );

        try {
            $complaint = Complaint::findOrFail($this->complaintIdToRespond);

            $complaint->update([
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
        $this->complaintIdToRespond = null;
        $this->adminResponse = '';
        $this->responseStatus = '';
        $this->resetValidation();
    }

    public function delete($complaintId)
    {
        $this->complaintIdToDelete = $complaintId;
        $complaint = Complaint::find($complaintId);

        $this->dispatch('show-confirm', [
            'type' => 'warning',
            'title' => 'Konfirmasi Hapus',
            'content' => 'Apakah Anda yakin ingin menghapus pengaduan dari "' . $complaint->name . '"? Tindakan ini tidak dapat dibatalkan.',
            'callback' => 'confirm-delete-complaint',
        ]);
    }

    public function performDelete()
    {
        try {
            Complaint::findOrFail($this->complaintIdToDelete)->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Pengaduan berhasil dihapus.',
            ]);

            $this->fetchData();
            $this->complaintIdToDelete = null;
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
            'pending' => 'Pending',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            default => $status,
        };
    }
}; ?>

<div>
    <!-- Header Page -->
    <x-app-header-page title="Pengaduan Masyarakat" description="Kelola pengaduan dari masyarakat di sini."
        :breadcrumbs="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Pengaduan']]">
    </x-app-header-page>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 mb-4">
        @php
            $totalComplaints = Complaint::count();
            $pendingComplaints = Complaint::where('status', 'pending')->count();
            $inProgressComplaints = Complaint::where('status', 'in_progress')->count();
            $resolvedComplaints = Complaint::where('status', 'resolved')->count();
        @endphp

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalComplaints }}</p>
                </div>
                <div class="bg-gray-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingComplaints }}</p>
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
                    <p class="text-sm text-blue-600">Diproses</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $inProgressComplaints }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600">Selesai</p>
                    <p class="text-2xl font-bold text-green-900">{{ $resolvedComplaints }}</p>
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
        <div class="w-full md:w-1/3">
            <flux:select wire:model="statusFilter" placeholder="Filter berdasarkan status" wire:change="fetchData">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full md:flex-1">
            <flux:input wire:model.debounce.500ms="search" type="text" placeholder="Cari pengaduan..."
                wire:keyup="fetchData" />
        </div>
    </div>

    <!-- Table Data -->
    <x-table-container>
        <x-slot:columns>
            <x-table.column>Pelapor</x-table.column>
            <x-table.column>Pesan</x-table.column>
            <x-table.column>Status</x-table.column>
            <x-table.column>Tanggal</x-table.column>
            <x-table.column>Ditanggapi Oleh</x-table.column>
            <x-table.column align="center">Aksi</x-table.column>
        </x-slot:columns>

        <x-slot:rows>
            @forelse ($complaintsData as $complaint)
                <x-table.row>
                    <x-table.cell>
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900">{{ $complaint->name }}</span>
                            @if ($complaint->email)
                                <span class="text-xs text-gray-500">{{ $complaint->email }}</span>
                            @endif
                            @if ($complaint->phone)
                                <span class="text-xs text-gray-500">{{ $complaint->phone }}</span>
                            @endif
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="max-w-md">
                            <p class="text-sm text-gray-900 line-clamp-2">
                                {{ Str::limit($complaint->message, 100) }}
                            </p>
                            @if ($complaint->admin_response)
                                <div class="mt-2 p-2 bg-blue-50 rounded">
                                    <p class="text-xs font-semibold text-blue-900 mb-1">Tanggapan Admin:</p>
                                    <p class="text-xs text-blue-800 line-clamp-2">
                                        {{ Str::limit($complaint->admin_response, 80) }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        <span
                            class="text-xs px-2 py-1 rounded-md
                            {{ $complaint->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $complaint->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $complaint->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $complaint->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $this->getStatusLabel($complaint->status) }}
                        </span>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="flex flex-col">
                            <span class="text-sm">{{ $complaint->created_at->format('d M Y') }}</span>
                            <span class="text-xs text-gray-500">{{ $complaint->created_at->format('H:i') }}</span>
                            @if ($complaint->responded_at)
                                <span class="text-xs text-green-600 mt-1">
                                    Ditanggapi: {{ $complaint->responded_at->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        @if ($complaint->respondedBy)
                            <div class="flex flex-col">
                                <span class="text-sm font-medium">{{ $complaint->respondedBy->full_name }}</span>
                                <span class="text-xs text-gray-500">{{ $complaint->respondedBy->email }}</span>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">Belum ditanggapi</span>
                        @endif
                    </x-table.cell>
                    <x-table.cell align="center">
                        <div class="flex items-center justify-center gap-1">
                            <!-- Respond -->
                            <flux:button icon="chat-bubble-left-right" size="xs" variant="filled"
                                wire:click="openResponseModal({{ $complaint->id }})" title="Tanggapi" />

                            <!-- Delete -->
                            <flux:button icon="trash" size="xs" variant="danger"
                                wire:click="delete({{ $complaint->id }})" title="Hapus" />
                        </div>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.row>
                    <x-table.cell colspan="6" align="center">Tidak ada data pengaduan</x-table.cell>
                </x-table.row>
            @endforelse
        </x-slot:rows>
    </x-table-container>

    <!-- Pagination -->
    <div class="mt-4">
        <x-simple-pagination :pagination="$complaintsPagination" />
    </div>

    <!-- Response Modal -->
    <flux:modal name="complaint-response-modal" class="lg:min-w-[700px] max-h-[90vh] overflow-y-auto"
        wire:model.self="showResponseModal" wire:close="closeResponseModal">
        <form wire:submit="saveResponse">
            <flux:heading size="lg">Tanggapi Pengaduan</flux:heading>

            @if ($complaintIdToRespond)
                @php
                    $complaint = Complaint::find($complaintIdToRespond);
                @endphp

                @if ($complaint)
                    <!-- Complaint Details -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <div class="space-y-2">
                            <div>
                                <span class="text-xs font-semibold text-gray-600">Pelapor:</span>
                                <span class="text-sm font-medium">{{ $complaint->name }}</span>
                            </div>
                            @if ($complaint->email || $complaint->phone)
                                <div>
                                    <span class="text-xs font-semibold text-gray-600">Kontak:</span>
                                    <span class="text-sm">
                                        {{ $complaint->email ?? '' }}
                                        {{ $complaint->email && $complaint->phone ? ' | ' : '' }}
                                        {{ $complaint->phone ?? '' }}
                                    </span>
                                </div>
                            @endif
                            <div>
                                <span class="text-xs font-semibold text-gray-600">Tanggal:</span>
                                <span class="text-sm">{{ $complaint->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-600">Pesan Pengaduan:</span>
                                <p class="text-sm mt-1 whitespace-pre-wrap">{{ $complaint->message }}</p>
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
                        <flux:select wire:model="responseStatus" placeholder="Pilih status">
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
                        <flux:label>Tanggapan <span class="text-red-500">*</span></flux:label>
                        <flux:textarea wire:model="adminResponse" rows="6"
                            placeholder="Masukkan tanggapan Anda terhadap pengaduan ini..." />
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
