<?php

use Livewire\Volt\Component;
use App\Models\Task;
use App\Models\Program;
use App\Models\User;
use Illuminate\Validation\Rule;

new class extends Component {
    public $programId = null;

    public $taskId = null;
    public $task_name = '';
    public $task_description = '';
    public $parent_task_id = '';
    public $assigned_user_id = '';
    public $status = 'not_started';
    public $progress_type = 'percentage';
    public $priority = 'medium';
    public $start_date = '';
    public $end_date = '';
    public $estimated_budget = '';

    public $isEdit = false;
    public $showModal = false;

    public array $statuses = [];
    public array $priorities = [];
    public array $progressTypes = [];
    public array $programs = [];
    public array $users = [];
    public array $parentTasks = [];

    protected $listeners = ['open-task-form' => 'openModal'];

    public function mount($programId = null)
    {
        $this->programId = $programId;

        $this->statuses = [['name' => 'Belum Dimulai', 'value' => 'not_started'], ['name' => 'Sedang Berjalan', 'value' => 'in_progress'], ['name' => 'Selesai', 'value' => 'completed'], ['name' => 'Ditunda', 'value' => 'on_hold'], ['name' => 'Dibatalkan', 'value' => 'cancelled']];

        $this->priorities = [['name' => 'Rendah', 'value' => 'low'], ['name' => 'Sedang', 'value' => 'medium'], ['name' => 'Tinggi', 'value' => 'high']];

        $this->progressTypes = [['name' => 'Persentase', 'value' => 'percentage'], ['name' => 'Status', 'value' => 'status']];

        // Load programs for dropdown
        $this->programs = Program::all()->toArray();

        // Load users for PIC dropdown
        $this->users = User::where('is_active', true)
            ->whereIn('role', ['staff', 'kasun'])
            ->get()
            ->toArray();

        // Load parent tasks (only top-level tasks)
        $this->parentTasks = Task::whereNull('parent_task_id')->where('program_id', $this->programId)->get()->toArray();
    }

    public function openModal($taskId = null)
    {
        $this->resetForm();

        if ($taskId) {
            $this->isEdit = true;
            $this->taskId = $taskId;
            $this->loadTask($taskId);
        } else {
            $this->isEdit = false;
        }

        $this->showModal = true;
    }

    public function loadTask($taskId)
    {
        $task = Task::findOrFail($taskId);

        $this->task_name = $task->task_name;
        $this->task_description = $task->task_description ?? '';
        $this->parent_task_id = $task->parent_task_id ?? '';
        $this->assigned_user_id = $task->assigned_user_id ?? '';
        $this->status = $task->status;
        $this->progress_type = $task->progress_type ?? 'percentage';
        $this->priority = $task->priority ?? 'medium';
        $this->start_date = $task->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $task->end_date?->format('Y-m-d') ?? '';
        $this->estimated_budget = $task->estimated_budget ?? '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->taskId = null;
        $this->task_name = '';
        $this->task_description = '';
        $this->parent_task_id = '';
        $this->assigned_user_id = '';
        $this->status = 'not_started';
        $this->progress_type = 'percentage';
        $this->priority = 'medium';
        $this->start_date = '';
        $this->end_date = '';
        $this->estimated_budget = '';
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'task_name' => ['required', 'string', 'max:255'],
            'task_description' => ['nullable', 'string'],
            'parent_task_id' => ['nullable', 'exists:tasks,id'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:not_started,in_progress,completed,on_hold,cancelled'],
            'progress_type' => ['nullable', 'in:percentage,status'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'estimated_budget' => ['nullable', 'numeric', 'min:0'],
        ];

        $messages = [
            'task_name.required' => 'Nama tugas wajib diisi.',
            'task_name.max' => 'Nama tugas maksimal :max karakter.',
            'parent_task_id.exists' => 'Tugas induk tidak valid.',
            'assigned_user_id.exists' => 'Pengguna yang ditugaskan tidak valid.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status tidak valid.',
            'progress_type.in' => 'Tipe progress tidak valid.',
            'priority.in' => 'Prioritas tidak valid.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'estimated_budget.numeric' => 'Estimasi anggaran harus berupa angka.',
            'estimated_budget.min' => 'Estimasi anggaran minimal :min.',
        ];

        $attributes = [
            'task_name' => 'Nama Tugas',
            'task_description' => 'Deskripsi Tugas',
            'parent_task_id' => 'Tugas Induk',
            'assigned_user_id' => 'Pengguna yang Ditugaskan',
            'status' => 'Status',
            'progress_type' => 'Tipe Progress',
            'priority' => 'Prioritas',
            'start_date' => 'Tanggal Mulai',
            'end_date' => 'Tanggal Selesai',
            'estimated_budget' => 'Estimasi Anggaran',
        ];

        $validated = $this->validate($rules, $messages, $attributes);

        try {
            if ($this->isEdit) {
                $task = Task::findOrFail($this->taskId);

                $updateData = [
                    'task_name' => $validated['task_name'],
                    'task_description' => $validated['task_description'] ?? null,
                    'parent_task_id' => $validated['parent_task_id'] != '' ? $validated['parent_task_id'] : null,
                    'assigned_user_id' => $validated['assigned_user_id'] != '' ? $validated['assigned_user_id'] : null,
                    'status' => $validated['status'],
                    'progress_type' => $validated['progress_type'] ?? 'percentage',
                    'priority' => $validated['priority'] ?? 'medium',
                    'start_date' => $validated['start_date'] ?? null,
                    'end_date' => $validated['end_date'] ?? null,
                    'estimated_budget' => floatval($validated['estimated_budget'] ?? 0),
                ];

                $task->update($updateData);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'content' => 'Tugas berhasil diperbarui.',
                ]);
            } else {
                if (in_array(Auth::user()->role, ['staff', 'kasun']) && ($validated['assigned_user_id'] == '' || $validated['assigned_user_id'] == null)) {
                    $validated['assigned_user_id'] = Auth::user()->id;
                }

                Task::create([
                    'task_name' => $validated['task_name'],
                    'task_description' => $validated['task_description'] ?? null,
                    'program_id' => $this->programId,
                    'parent_task_id' => $validated['parent_task_id'] != '' ? $validated['parent_task_id'] : null,
                    'assigned_user_id' => $validated['assigned_user_id'] != '' ? $validated['assigned_user_id'] : null,
                    'status' => $validated['status'],
                    'progress_type' => $validated['progress_type'] ?? 'percentage',
                    'priority' => $validated['priority'] ?? 'medium',
                    'start_date' => $validated['start_date'] ?? null,
                    'end_date' => $validated['end_date'] ?? null,
                    'estimated_budget' => floatval($validated['estimated_budget'] ?? 0),
                ]);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'content' => 'Tugas berhasil ditambahkan.',
                ]);
            }

            $this->closeModal();
            $this->dispatch('task-saved');
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
    <flux:modal name="task-form" wire:model.self="showModal" class="min-w-[700px] max-h-[90vh] overflow-y-auto">
        <form wire:submit.prevent="save">
            <div>
                <flux:heading size="lg">
                    {{ $isEdit ? 'Edit Tugas' : 'Tambah Tugas' }}
                </flux:heading>
            </div>

            <div class="my-4 space-y-4">
                <!-- Task Name -->
                <div>
                    <flux:field>
                        <flux:label>Nama Tugas <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="task_name" type="text" placeholder="Masukkan nama tugas" />
                        <flux:error name="task_name" />
                    </flux:field>
                </div>

                <!-- Task Description -->
                <div>
                    <flux:field>
                        <flux:label>Deskripsi Tugas</flux:label>
                        <flux:textarea wire:model="task_description" placeholder="Masukkan deskripsi tugas"
                            rows="3" />
                        <flux:error name="task_description" />
                    </flux:field>
                </div>

                <!-- Parent Task -->
                <div>
                    <flux:field>
                        <flux:label>Tugas Induk (Opsional)</flux:label>
                        <flux:select wire:model="parent_task_id" placeholder="Pilih tugas induk">
                            <option value="">-- Tidak Ada (Tugas Utama) --</option>
                            @foreach ($parentTasks as $parentTask)
                                @if (!$isEdit || $parentTask['id'] != $taskId)
                                    <option value="{{ $parentTask['id'] }}">{{ $parentTask['task_name'] }}</option>
                                @endif
                            @endforeach
                        </flux:select>
                        <flux:error name="parent_task_id" />
                    </flux:field>
                </div>

                <!-- Assigned User -->
                @if (!in_array(Auth::user()->role, ['staff', 'kasun']))
                    <div>
                        <flux:field>
                            <flux:label>Ditugaskan Kepada</flux:label>
                            <flux:select wire:model="assigned_user_id" placeholder="Pilih pengguna">
                                <option value="">-- Pilih Pengguna --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['full_name'] }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="assigned_user_id" />
                        </flux:field>
                    </div>
                @endif

                <!-- Priority and Progress Type -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>Prioritas</flux:label>
                            <flux:select wire:model="priority" placeholder="Pilih prioritas">
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority['value'] }}">{{ $priority['name'] }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="priority" />
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Tipe Progress</flux:label>
                            <flux:select wire:model="progress_type" placeholder="Pilih tipe progress">
                                @foreach ($progressTypes as $progressType)
                                    <option value="{{ $progressType['value'] }}">{{ $progressType['name'] }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="progress_type" />
                        </flux:field>
                    </div>
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

                <!-- Estimated Budget -->
                <div>
                    <flux:field>
                        <flux:label>Estimasi Anggaran</flux:label>
                        <flux:input wire:model.live="estimated_budget" type="number" step="0.01" min="0"
                            placeholder="Masukkan estimasi anggaran" />
                        <flux:error name="estimated_budget" />
                        <div class="text-xs text-gray-400">
                            Rp {{ number_format((float) $estimated_budget, 2, ',', '.') }}
                        </div>
                    </flux:field>
                </div>

                <!-- Status -->
                <div>
                    <flux:field>
                        <flux:label>Status <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model="status" placeholder="Pilih status">
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
