<?php

use Livewire\Volt\Component;
use App\Models\Program;
use App\Models\User;
use Illuminate\Validation\Rule;

new class extends Component {
    public $projectId = null;
    public $program_name = '';
    public $program_description = '';
    public $location = '';
    public $start_date = '';
    public $end_date = '';
    public $pic_user_id = '';
    public $total_budget = '';
    public $status = 'planned';

    public $isEdit = false;
    public $showModal = false;

    public array $statuses = [];
    public array $users = [];

    protected $listeners = ['open-project-form' => 'openModal'];

    public function mount()
    {
        $this->statuses = [['name' => 'Direncanakan', 'value' => 'planned'], ['name' => 'Sedang Berjalan', 'value' => 'in_progress'], ['name' => 'Selesai', 'value' => 'completed'], ['name' => 'Ditunda', 'value' => 'on_hold'], ['name' => 'Dibatalkan', 'value' => 'cancelled']];

        // Load users for PIC dropdown
        $this->users = User::where('is_active', true)->where('role', 'staff')->get()->toArray();
    }

    public function openModal($projectId = null)
    {
        $this->resetForm();

        if ($projectId) {
            $this->isEdit = true;
            $this->projectId = $projectId;
            $this->loadProject($projectId);
        } else {
            $this->isEdit = false;
        }

        $this->showModal = true;
    }

    public function loadProject($projectId)
    {
        $project = Program::findOrFail($projectId);

        $this->program_name = $project->program_name;
        $this->program_description = $project->program_description ?? '';
        $this->location = $project->location ?? '';
        $this->start_date = $project->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $project->end_date?->format('Y-m-d') ?? '';
        $this->pic_user_id = $project->pic_user_id ?? '';
        $this->total_budget = $project->total_budget ?? '';
        $this->status = $project->status;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->projectId = null;
        $this->program_name = '';
        $this->program_description = '';
        $this->location = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->pic_user_id = '';
        $this->total_budget = '';
        $this->status = 'planned';
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'program_name' => ['required', 'string', 'max:255'],
            'program_description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'pic_user_id' => ['nullable', 'exists:users,id'],
            'total_budget' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:planned,in_progress,completed,on_hold,cancelled'],
        ];

        $messages = [
            'program_name.required' => 'Nama program wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'pic_user_id.exists' => 'Penanggung jawab yang dipilih tidak valid.',
            'total_budget.numeric' => 'Total anggaran harus berupa angka.',
            'total_budget.min' => 'Total anggaran tidak boleh negatif.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];

        $attributes = [
            'program_name' => 'Nama Program',
            'program_description' => 'Deskripsi Program',
            'location' => 'Lokasi',
            'start_date' => 'Tanggal Mulai',
            'end_date' => 'Tanggal Selesai',
            'pic_user_id' => 'Penanggung Jawab',
            'total_budget' => 'Total Anggaran',
            'status' => 'Status',
        ];

        $validated = $this->validate($rules, $messages, $attributes);

        try {
            if ($this->isEdit) {
                $project = Program::findOrFail($this->projectId);

                $updateData = [
                    'program_name' => $validated['program_name'],
                    'program_description' => $validated['program_description'] ?? null,
                    'location' => $validated['location'] ?? null,
                    'start_date' => $validated['start_date'] ?? null,
                    'end_date' => $validated['end_date'] ?? null,
                    'pic_user_id' => $validated['pic_user_id'] != '' ? $validated['pic_user_id'] : null,
                    'total_budget' => floatval($validated['total_budget'] ?? 0),
                    'status' => $validated['status'],
                ];

                $project->update($updateData);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'content' => 'Program berhasil diperbarui.',
                ]);
            } else {
                if (Auth::user()->role === 'staff' && ($validated['pic_user_id'] == '' || $validated['pic_user_id'] == null)) {
                    $validated['pic_user_id'] = Auth::id();
                }

                Program::create([
                    'program_name' => $validated['program_name'],
                    'program_description' => $validated['program_description'] ?? null,
                    'location' => $validated['location'] ?? null,
                    'start_date' => $validated['start_date'] ?? null,
                    'end_date' => $validated['end_date'] ?? null,
                    'pic_user_id' => $validated['pic_user_id'] != '' ? $validated['pic_user_id'] : null,
                    'total_budget' => floatval($validated['total_budget'] ?? 0),
                    'status' => $validated['status'],
                    'created_by' => auth()->id(),
                ]);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'content' => 'Program berhasil ditambahkan.',
                ]);
            }

            $this->closeModal();
            $this->dispatch('project-saved');
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
    <flux:modal name="project-form" wire:model.self="showModal" class="min-w-[600px]">
        <form wire:submit.prevent="save">
            <div>
                <flux:heading size="lg">
                    {{ $isEdit ? 'Edit Program' : 'Tambah Program' }}
                </flux:heading>
            </div>

            <div class="my-4 space-y-4">
                <!-- Program Name -->
                <div>
                    <flux:field>
                        <flux:label>Nama Program <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="program_name" type="text" placeholder="Masukkan nama program" />
                        <flux:error name="program_name" />
                    </flux:field>
                </div>

                <!-- Program Description -->
                <div>
                    <flux:field>
                        <flux:label>Deskripsi Program</flux:label>
                        <flux:textarea wire:model="program_description" placeholder="Masukkan deskripsi program"
                            rows="3" />
                        <flux:error name="program_description" />
                    </flux:field>
                </div>

                <!-- Location -->
                <div>
                    <flux:field>
                        <flux:label>Lokasi</flux:label>
                        <flux:input wire:model="location" type="text" placeholder="Masukkan lokasi program" />
                        <flux:error name="location" />
                    </flux:field>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>Tanggal Mulai <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model="start_date" type="date" />
                            <flux:error name="start_date" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Tanggal Selesai <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model="end_date" type="date" />
                            <flux:error name="end_date" />
                        </flux:field>
                    </div>
                </div>

                <!-- PIC -->
                @if (Auth::user()->role !== 'staff')
                    <div>
                        <flux:field>
                            <flux:label>Pelaksanan Kegiatan (PK)</flux:label>
                            <flux:select wire:model="pic_user_id" placeholder="Pilih penanggung jawab">
                                <option value="">-- Pilih PK --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['full_name'] }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="pic_user_id" />
                        </flux:field>
                    </div>
                @endif

                <!-- Total Budget -->
                <div>
                    <flux:field>
                        <flux:label>Total Anggaran</flux:label>
                        <flux:input wire:model.live="total_budget" type="number" step="0.01" min="0"
                            placeholder="Masukkan total anggaran" />
                        <flux:error name="total_budget" />
                        <div class="text-xs text-gray-400">
                            Rp {{ number_format((float) $total_budget, 2, ',', '.') }}
                        </div>
                    </flux:field>
                </div>

                <!-- Status -->
                <div>
                    <flux:field>
                        <flux:label>Status <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model="status" placeholder="Pilih status" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status['value'] }}">{{ $status['name'] }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="status" />
                    </flux:field>
                </div>
            </div>

            <div class="flex gap-2">
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
