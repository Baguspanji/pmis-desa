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
        $this->statuses = [
            ['name' => 'Direncanakan', 'value' => 'planned'],
            ['name' => 'Sedang Berjalan', 'value' => 'in_progress'],
            ['name' => 'Selesai', 'value' => 'completed'],
            ['name' => 'Ditunda', 'value' => 'on_hold'],
            ['name' => 'Dibatalkan', 'value' => 'cancelled'],
        ];

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
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'pic_user_id' => ['nullable', 'exists:users,id'],
            'total_budget' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:planned,in_progress,completed,on_hold,cancelled'],
        ];

        $validated = $this->validate($rules);

        try {
            if ($this->isEdit) {
                $project = Program::findOrFail($this->projectId);

                $updateData = [
                    'program_name' => $validated['program_name'],
                    'program_description' => $validated['program_description'] ?? null,
                    'location' => $validated['location'] ?? null,
                    'start_date' => $validated['start_date'] ?? null,
                    'end_date' => $validated['end_date'] ?? null,
                    'pic_user_id' => $validated['pic_user_id'] ?? null,
                    'total_budget' => $validated['total_budget'] ?? null,
                    'status' => $validated['status'],
                ];

                $project->update($updateData);

                session()->flash('message', 'Program berhasil diperbarui.');
            } else {
                Program::create([
                    'program_name' => $validated['program_name'],
                    'program_description' => $validated['program_description'] ?? null,
                    'location' => $validated['location'] ?? null,
                    'start_date' => $validated['start_date'] ?? null,
                    'end_date' => $validated['end_date'] ?? null,
                    'pic_user_id' => $validated['pic_user_id'] ?? null,
                    'total_budget' => $validated['total_budget'] ?? null,
                    'status' => $validated['status'],
                    'created_by' => auth()->id(),
                ]);

                session()->flash('message', 'Program berhasil ditambahkan.');
            }

            $this->closeModal();
            $this->dispatch('project-saved');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
                        <flux:input wire:model="program_name" type="text" placeholder="Masukkan nama program" required />
                        @error('program_name')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Program Description -->
                <div>
                    <flux:field>
                        <flux:label>Deskripsi Program</flux:label>
                        <flux:textarea wire:model="program_description" placeholder="Masukkan deskripsi program" rows="3" />
                        @error('program_description')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Location -->
                <div>
                    <flux:field>
                        <flux:label>Lokasi</flux:label>
                        <flux:input wire:model="location" type="text" placeholder="Masukkan lokasi program" />
                        @error('location')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>Tanggal Mulai</flux:label>
                            <flux:input wire:model="start_date" type="date" />
                            @error('start_date')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Tanggal Selesai</flux:label>
                            <flux:input wire:model="end_date" type="date" />
                            @error('end_date')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- PIC -->
                <div>
                    <flux:field>
                        <flux:label>Penanggung Jawab (PIC)</flux:label>
                        <flux:select wire:model="pic_user_id" placeholder="Pilih penanggung jawab">
                            <option value="">-- Pilih PIC --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user['id'] }}">{{ $user['full_name'] }}</option>
                            @endforeach
                        </flux:select>
                        @error('pic_user_id')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Total Budget -->
                <div>
                    <flux:field>
                        <flux:label>Total Anggaran</flux:label>
                        <flux:input wire:model.live="total_budget" type="number" step="0.01" min="0" placeholder="Masukkan total anggaran" />
                        @error('total_budget')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                        <div class="text-xs text-gray-400">
                            Rp {{ number_format((float)$total_budget, 2, ',', '.') }}
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
                        @error('status')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
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
