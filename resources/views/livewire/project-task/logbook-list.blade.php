<?php

use Livewire\Volt\Component;
use App\Models\TaskLogbook;
use App\Models\TaskTarget;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $taskTargetId;
    public $logbooks = [];
    public $target;

    protected $listeners = ['logbook-saved' => 'loadLogbooks'];

    public function mount($targetId)
    {
        $this->taskTargetId = $targetId;
        $this->loadLogbooks();
    }

    public function loadLogbooks()
    {
        $this->target = TaskTarget::with(['task'])->findOrFail($this->taskTargetId);
        $this->logbooks = TaskLogbook::with(['creator', 'verifier', 'attachments'])
            ->where('task_target_id', $this->taskTargetId)
            ->orderBy('log_date', 'desc')
            ->get();
    }

    public function createNew()
    {
        $this->dispatch('open-logbook-form', taskId: $this->target->task_id, taskTargetId: $this->taskTargetId);
    }

    public function edit($logbookId)
    {
        $this->dispatch('open-logbook-form', taskId: $this->target->task_id, taskTargetId: $this->taskTargetId, logbookId: $logbookId);
    }

    public function deleteLogbook($logbookId)
    {
        if (!$logbookId) {
            return;
        }

        try {
            $logbook = TaskLogbook::findOrFail($logbookId);

            // If logbook was verified, decrease achieved value
            if ($logbook->status === 'verified') {
                $target = TaskTarget::findOrFail($this->taskTargetId);
                $target->achieved_value = max(0, $target->achieved_value - $logbook->progress_value);
                $target->save();
            }

            // Delete attachments from storage
            foreach ($logbook->attachments as $attachment) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }

            $logbook->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Logbook berhasil dihapus.',
            ]);

            $this->loadLogbooks();
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function verifyLogbook($logbookId)
    {
        try {
            $logbook = TaskLogbook::findOrFail($logbookId);

            if ($logbook->status === 'verified') {
                $this->dispatch('show-alert', [
                    'type' => 'warning',
                    'title' => 'Peringatan',
                    'content' => 'Logbook sudah terverifikasi.',
                ]);
                return;
            }

            $oldStatus = $logbook->status;
            $logbook->status = 'verified';
            $logbook->verified_by = Auth::id();
            $logbook->verified_at = now();
            $logbook->save();

            // Update achieved value if not previously verified
            if ($oldStatus !== 'verified') {
                $target = TaskTarget::findOrFail($this->taskTargetId);
                $target->achieved_value = $target->achieved_value + $logbook->progress_value;
                $target->save();
            }

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Logbook berhasil diverifikasi.',
            ]);

            $this->dispatch('target-saved');
            $this->loadLogbooks();
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function getLogTypeLabel($type)
    {
        return match ($type) {
            'progress' => 'Progress',
            'milestone' => 'Milestone',
            'issue' => 'Issue',
            'note' => 'Catatan',
            default => $type,
        };
    }

    public function getLogTypeBadgeColor($type)
    {
        return match ($type) {
            'progress' => 'blue',
            'milestone' => 'green',
            'issue' => 'red',
            'note' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusBadgeColor($status)
    {
        return match ($status) {
            'draft' => 'gray',
            'submitted' => 'yellow',
            'verified' => 'green',
            default => 'gray',
        };
    }

    public function getStatusLabel($status)
    {
        return match ($status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'verified' => 'Verified',
            default => $status,
        };
    }
}; ?>

<div>
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-4 gap-2">
        <h3 class="text-lg font-semibold">Logbook Target ({{ count($logbooks) }})</h3>
        <flux:button size="sm" wire:click="createNew" variant="primary" icon="plus">
            Tambah Logbook
        </flux:button>
    </div>

    @if (count($logbooks) > 0)
        <div class="space-y-4">
            @foreach ($logbooks as $logbook)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h4 class="text-base font-semibold text-gray-900">
                                    {{ $logbook->title }}
                                </h4>
                                <flux:badge color="{{ $this->getLogTypeBadgeColor($logbook->log_type) }}"
                                    size="sm">
                                    {{ $this->getLogTypeLabel($logbook->log_type) }}
                                </flux:badge>
                                <flux:badge color="{{ $this->getStatusBadgeColor($logbook->status) }}" size="sm">
                                    {{ $this->getStatusLabel($logbook->status) }}
                                </flux:badge>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span>
                                    <flux:icon name="calendar" class="w-3 h-3 inline" />
                                    {{ $logbook->activity_date?->format('d M Y') }}
                                </span>
                                @if ($logbook->location)
                                    <span>
                                        <flux:icon name="map-pin" class="w-3 h-3 inline" />
                                        {{ $logbook->location }}
                                    </span>
                                @endif
                                <span>
                                    <flux:icon name="user" class="w-3 h-3 inline" />
                                    {{ $logbook->creator?->full_name ?? 'Unknown' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            @if ($logbook->status !== 'verified')
                                <flux:button icon="check-circle" size="xs"
                                    wire:click="verifyLogbook({{ $logbook->id }})" variant="ghost"
                                    title="Verifikasi" />
                                <flux:button icon="square-pen" size="xs" wire:click="edit({{ $logbook->id }})"
                                    variant="ghost" title="Edit" />
                            @endif
                            <flux:button icon="trash" size="xs" variant="danger"
                                wire:click="deleteLogbook({{ $logbook->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus logbook ini?" title="Hapus" />
                        </div>
                    </div>

                    <!-- Description -->
                    @if ($logbook->description)
                        <p class="text-sm text-gray-700 mb-3">{{ $logbook->description }}</p>
                    @endif

                    <!-- Progress Value -->
                    <div class="bg-blue-50 rounded-lg px-3 py-1.5 mb-3">
                        <label class="text-xs font-medium text-blue-900">Nilai Progress</label>
                        <p class="mt-1 text-lg font-bold text-blue-900">
                            {{ number_format($logbook->progress_value, 2, ',', '.') }} {{ $target->target_unit }}
                        </p>
                    </div>

                    <!-- Verified Info -->
                    @if ($logbook->status === 'verified' && $logbook->verifier)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                            <div class="flex items-center gap-2 text-xs text-green-900">
                                <flux:icon name="check-circle" class="w-4 h-4" />
                                <span>
                                    Diverifikasi oleh <strong>{{ $logbook->verifier->full_name }}</strong>
                                    pada {{ $logbook->verified_at?->format('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Attachments -->
                    @if ($logbook->attachments->count() > 0)
                        <div class="border-t pt-3">
                            <label class="text-xs font-medium text-gray-700 mb-2 block">Lampiran:</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($logbook->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                        class="flex items-center gap-1 px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700">
                                        <flux:icon name="paper-clip" class="w-3 h-3" />
                                        {{ Str::limit($attachment->file_name, 20) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <flux:icon name="document-text" class="w-12 h-12 text-gray-400 mx-auto mb-2" />
            <p class="text-sm text-gray-500">Belum ada logbook untuk target ini.</p>
            <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Logbook" untuk memulai.</p>
        </div>
    @endif
</div>
