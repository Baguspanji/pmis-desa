<?php

use Livewire\Volt\Component;
use App\Models\TaskTarget;
use App\Models\Task;
use Illuminate\Validation\Rule;

new class extends Component {
    public $taskId = null;
    public $targetId = null;
    public $target_value = '';
    public $achieved_value = '';
    public $target_date = '';
    public $notes = '';

    public $isEdit = false;
    public $showModal = false;

    protected $listeners = ['open-target-form' => 'openModal'];

    public function openModal($taskId, $targetId = null)
    {
        $this->resetForm();
        $this->taskId = $taskId;

        if ($targetId) {
            $this->isEdit = true;
            $this->targetId = $targetId;
            $this->loadTarget($targetId);
        } else {
            $this->isEdit = false;
        }

        $this->showModal = true;
    }

    public function loadTarget($targetId)
    {
        $target = TaskTarget::findOrFail($targetId);

        $this->target_value = $target->target_value;
        $this->achieved_value = $target->achieved_value ?? '';
        $this->target_date = $target->target_date?->format('Y-m-d') ?? '';
        $this->notes = $target->notes ?? '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->targetId = null;
        $this->target_value = '';
        $this->achieved_value = '';
        $this->target_date = '';
        $this->notes = '';
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'target_value' => ['required', 'numeric', 'min:0'],
            'achieved_value' => ['nullable', 'numeric', 'min:0'],
            'target_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];

        $validated = $this->validate($rules);

        try {
            if ($this->isEdit) {
                $target = TaskTarget::findOrFail($this->targetId);

                $target->update([
                    'target_value' => $validated['target_value'],
                    'achieved_value' => $validated['achieved_value'] ?? 0,
                    'target_date' => $validated['target_date'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'content' => 'Target tugas berhasil diperbarui.',
                ]);
            } else {
                TaskTarget::create([
                    'task_id' => $this->taskId,
                    'target_value' => $validated['target_value'],
                    'achieved_value' => $validated['achieved_value'] ?? 0,
                    'target_date' => $validated['target_date'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'content' => 'Target tugas berhasil ditambahkan.',
                ]);
            }

            $this->closeModal();
            $this->dispatch('target-saved');
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => $e->getMessage(),
            ]);
        }
    }

    public function deleteTarget()
    {
        if (!$this->targetId) {
            return;
        }

        try {
            $target = TaskTarget::findOrFail($this->targetId);
            $target->delete();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Target tugas berhasil dihapus.',
            ]);

            $this->closeModal();
            $this->dispatch('target-saved');
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
    <flux:modal name="target-form" wire:model.self="showModal" class="min-w-[600px]">
        <form wire:submit.prevent="save">
            <div>
                <flux:heading size="lg">
                    {{ $isEdit ? 'Edit Target Tugas' : 'Tambah Target Tugas' }}
                </flux:heading>
            </div>

            <div class="my-4 space-y-4">
                <!-- Target Value -->
                <div>
                    <flux:field>
                        <flux:label>Nilai Target <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model.live="target_value" type="number" step="0.01" min="0"
                            placeholder="Masukkan nilai target" required />
                        @error('target_value')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                        <div class="text-xs text-gray-400 mt-1">
                            Target: {{ number_format((float) $target_value, 2, ',', '.') }}
                        </div>
                    </flux:field>
                </div>

                <!-- Achieved Value -->
                <div>
                    <flux:field>
                        <flux:label>Nilai Tercapai</flux:label>
                        <flux:input wire:model.live="achieved_value" type="number" step="0.01" min="0"
                            placeholder="Masukkan nilai yang sudah tercapai" />
                        @error('achieved_value')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                        <div class="text-xs text-gray-400 mt-1">
                            Tercapai: {{ number_format((float) ($achieved_value ?: 0), 2, ',', '.') }}
                        </div>
                        @if($target_value > 0 && $achieved_value > 0)
                            <div class="text-xs mt-1 {{ ($achieved_value / $target_value * 100) >= 100 ? 'text-green-600' : 'text-blue-600' }}">
                                Progress: {{ number_format(($achieved_value / $target_value * 100), 2) }}%
                            </div>
                        @endif
                    </flux:field>
                </div>

                <!-- Target Date -->
                <div>
                    <flux:field>
                        <flux:label>Tanggal Target <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="target_date" type="date" required />
                        @error('target_date')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Notes -->
                <div>
                    <flux:field>
                        <flux:label>Catatan</flux:label>
                        <flux:textarea wire:model="notes" placeholder="Masukkan catatan tambahan (opsional)"
                            rows="3" />
                        @error('notes')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>
            </div>

            <div class="flex gap-2">
                @if($isEdit)
                    <flux:button variant="danger" wire:click="deleteTarget" type="button"
                        wire:confirm="Apakah Anda yakin ingin menghapus target ini?">
                        Hapus
                    </flux:button>
                @endif
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
