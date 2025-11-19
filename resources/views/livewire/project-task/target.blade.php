<?php

use Livewire\Volt\Component;
use App\Models\Task;
use App\Models\TaskTarget;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $programId;
    public $taskId;
    public $task;
    public $targets = [];

    protected $listeners = ['target-saved' => 'loadTask'];

    public function mount($id, $taskId)
    {
        $this->programId = $id;
        $this->taskId = $taskId;
        $this->loadTask();
    }

    public function loadTask()
    {
        $this->task = Task::with(['program', 'assignedUser', 'targets.logbooks'])->findOrFail($this->taskId);
        $this->targets = $this->task->targets;
    }

    public function createNew()
    {
        $this->dispatch('open-target-form', taskId: $this->taskId);
    }

    public function edit($targetId)
    {
        $this->dispatch('open-target-form', taskId: $this->taskId, targetId: $targetId);
    }

    public function deleteTarget($targetId)
    {
        if (!$targetId) {
            return;
        }

        try {
            $target = TaskTarget::findOrFail($targetId);
            $target->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Target tugas berhasil dihapus.',
            ]);

            $this->loadTask();
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
    <x-app-header-page title="Target Tugas: {{ $task->task_name }}"
        description="Kelola target dan pencapaian untuk tugas ini." :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Program', 'url' => route('projects')],
            ['label' => 'Tugas', 'url' => route('projects.tasks', $programId)],
            ['label' => 'Target'],
        ]">
        <x-slot:actions>
            <flux:button href="{{ route('projects.tasks', $programId) }}" variant="ghost" icon="arrow-left">
                Kembali
            </flux:button>
        </x-slot:actions>
    </x-app-header-page>

    <div class="mt-6 space-y-6">
        <!-- Task Information Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold mb-4">Informasi Tugas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Nama Tugas</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $task->task_name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Program</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $task->program?->program_name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">PIC</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $task->assignedUser?->full_name ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Status</label>
                    <div class="mt-1">
                        @if ($task->status === 'not_started')
                            <flux:badge color="gray">Belum Dimulai</flux:badge>
                        @elseif ($task->status === 'in_progress')
                            <flux:badge color="blue">Sedang Berjalan</flux:badge>
                        @elseif ($task->status === 'completed')
                            <flux:badge color="green">Selesai</flux:badge>
                        @elseif ($task->status === 'on_hold')
                            <flux:badge color="yellow">Ditunda</flux:badge>
                        @else
                            <flux:badge color="red">Dibatalkan</flux:badge>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Targets List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-2 mb-4">
                <h3 class="text-lg font-semibold">Daftar Target ({{ count($targets) }})</h3>
                <flux:button size="sm" wire:click="createNew" variant="primary" icon="plus">
                    Tambah Target Baru
                </flux:button>
            </div>

            @if (count($targets) > 0)
                <div class="space-y-4">
                    @foreach ($targets as $target)
                        @php
                            $progressPercentage =
                                $target->target_value > 0 ? ($target->achieved_value / $target->target_value) * 100 : 0;
                            $logbookCount = $target->logbooks()->count();
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="text-base font-semibold text-gray-900">
                                            {{ $target->target_name }}
                                        </h4>
                                        @if ($logbookCount > 0)
                                            <flux:badge color="blue" size="sm">
                                                {{ $logbookCount }} - Logbook
                                            </flux:badge>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-500">Target Date:</span>
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ $target->target_date?->format('d/m/Y') ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <flux:button icon="square-pen" size="xs" wire:click="edit({{ $target->id }})"
                                        variant="ghost" />
                                    <flux:button icon="trash" size="xs" variant="danger"
                                        wire:click="deleteTarget({{ $target->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus target ini?" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                <div class="bg-blue-50 rounded-lg p-3">
                                    <label class="text-xs font-medium text-blue-900">Nilai Target</label>
                                    <p class="mt-1 text-xl font-bold text-blue-900">
                                        {{ number_format($target->target_value, 2, ',', '.') }}
                                        {{ $target->target_unit }}
                                    </p>
                                </div>
                                <div class="bg-green-50 rounded-lg p-3">
                                    <label class="text-xs font-medium text-green-900">Nilai Tercapai</label>
                                    <p class="mt-1 text-xl font-bold text-green-900">
                                        {{ number_format($target->achieved_value, 2, ',', '.') }}
                                        {{ $target->target_unit }}
                                    </p>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-3">
                                    <label class="text-xs font-medium text-purple-900">Progress</label>
                                    <p class="mt-1 text-xl font-bold text-purple-900">
                                        {{ number_format($progressPercentage, 2) }}%
                                    </p>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-3">
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all duration-300 {{ $progressPercentage >= 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                                        style="width: {{ min($progressPercentage, 100) }}%">
                                    </div>
                                </div>
                            </div>

                            @if ($target->notes)
                                <div class="mt-2">
                                    <label class="text-xs font-medium text-gray-700">Catatan:</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $target->notes }}</p>
                                </div>
                            @endif

                            <!-- Logbook Section -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div x-data="{ showLogbooks: false }">
                                    <div class="flex justify-between items-center mb-2">
                                        <button @click="showLogbooks = !showLogbooks"
                                            class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                            <flux:icon x-show="!showLogbooks" name="chevron-right" class="w-4 h-4" />
                                            <flux:icon x-show="showLogbooks" name="chevron-down" class="w-4 h-4" />
                                            <span>Logbook ({{ $logbookCount }})</span>
                                        </button>
                                    </div>
                                    <div x-show="showLogbooks" x-collapse>
                                        <div class="mt-3">
                                            @livewire('project-task.logbook-list', ['targetId' => $target->id])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <flux:icon name="check-circle" class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                    <p class="text-sm text-gray-500">Belum ada target yang ditambahkan.</p>
                    <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Target Baru" untuk memulai.</p>
                </div>
            @endif
        </div>

        <!-- Help Text -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <flux:icon name="information-circle" class="w-5 h-5 text-blue-600 mt-0.5 mr-2" />
                <div class="text-sm text-blue-900">
                    <p class="font-semibold mb-2">Panduan Penggunaan:</p>
                    <div class="space-y-3">
                        <div>
                            <p class="font-medium mb-1">Target:</p>
                            <ul class="list-disc list-inside space-y-1 ml-2">
                                <li>Setiap tugas dapat memiliki beberapa target dengan tanggal pencapaian yang berbeda
                                </li>
                                <li>Nilai target dan nilai tercapai mendukung angka desimal (gunakan titik sebagai
                                    pemisah desimal)</li>
                                <li>Progress dihitung otomatis: (Nilai Tercapai / Nilai Target) × 100%</li>
                                <li>Tambahkan catatan untuk memberikan konteks atau informasi tambahan pada setiap
                                    target</li>
                            </ul>
                        </div>
                        <div>
                            <p class="font-medium mb-1">Logbook:</p>
                            <ul class="list-disc list-inside space-y-1 ml-2">
                                <li>Setiap target dapat memiliki beberapa logbook untuk melacak kemajuan harian</li>
                                <li>Logbook mencatat aktivitas, pencapaian, dan kendala yang dihadapi</li>
                                <li>Klik ikon chevron pada bagian Logbook untuk melihat detail catatan</li>
                                <li>Logbook membantu dokumentasi proses pencapaian target secara detail</li>
                            </ul>
                        </div>
                        <div>
                            <p class="font-medium mb-1">Tips:</p>
                            <ul class="list-disc list-inside space-y-1 ml-2">
                                <li>Gunakan tombol <span class="font-medium">Edit</span> untuk mengubah data target yang
                                    sudah ada</li>
                                <li>Perhatikan badge logbook untuk mengetahui jumlah catatan yang sudah dibuat</li>
                                <li>Progress bar akan berubah hijau saat mencapai 100%</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Target Form Modal -->
    @livewire('project-task.target-form')

    <!-- Include Logbook Form Modal -->
    @livewire('project-task.logbook-form')
</div>
