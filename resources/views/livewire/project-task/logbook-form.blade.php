<?php

use Livewire\Volt\Component;
use App\Models\TaskLogbook;
use App\Models\Task;
use App\Models\TaskTarget;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\LogbookCreated;

new class extends Component {
    use WithFileUploads;

    public $isOpen = false;
    public $logbookId = null;
    public $taskId;
    public $taskTargetId;
    public $title = '';
    public $description = '';
    public $log_date;
    public $log_type = 'progress_update';
    public $progress_value = 0;
    public $status = 'draft';
    public $location = '';
    public $activity_date;
    public $attachments = [];

    protected $listeners = ['open-logbook-form' => 'openForm'];

    protected function rules()
    {
        return [
            'taskId' => 'required|exists:tasks,id',
            'taskTargetId' => 'required|exists:task_targets,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'log_date' => 'required|date',
            'log_type' => 'required|in:progress_update,issue,milestone,meeting,field_visit,other',
            'progress_value' => 'required|numeric|min:0',
            'status' => 'required|in:draft,submitted,verified',
            'location' => 'nullable|string|max:255',
            'activity_date' => 'required|date',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ];
    }

    protected $messages = [
        'taskId.required' => 'Task ID harus diisi',
        'taskTargetId.required' => 'Target ID harus diisi',
        'title.required' => 'Judul logbook harus diisi',
        'log_date.required' => 'Tanggal log harus diisi',
        'log_type.required' => 'Tipe log harus dipilih',
        'progress_value.required' => 'Nilai progress harus diisi',
        'progress_value.numeric' => 'Nilai progress harus berupa angka',
        'status.required' => 'Status harus dipilih',
        'activity_date.required' => 'Tanggal aktivitas harus diisi',
        'attachments.*.max' => 'Ukuran file maksimal 10MB',
    ];

    public function openForm($taskId, $taskTargetId, $logbookId = null)
    {
        $this->resetForm();
        $this->taskId = $taskId;
        $this->taskTargetId = $taskTargetId;
        $this->logbookId = $logbookId;

        if ($logbookId) {
            $logbook = TaskLogbook::findOrFail($logbookId);
            $this->title = $logbook->title;
            $this->description = $logbook->description;
            $this->log_date = $logbook->log_date;
            $this->log_type = $logbook->log_type;
            $this->progress_value = $logbook->progress_value;
            $this->status = $logbook->status;
            $this->location = $logbook->location;
            $this->activity_date = $logbook->activity_date?->format('Y-m-d');
        } else {
            $this->log_date = now()->format('Y-m-d');
            $this->activity_date = now()->format('Y-m-d');
        }

        $this->isOpen = true;
    }

    public function resetForm()
    {
        $this->reset(['logbookId', 'taskId', 'taskTargetId', 'title', 'description', 'log_date', 'log_type', 'progress_value', 'status', 'location', 'activity_date', 'attachments']);
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'task_id' => $this->taskId,
                'task_target_id' => $this->taskTargetId,
                'title' => $this->title,
                'description' => $this->description,
                'log_date' => $this->log_date,
                'log_type' => $this->log_type,
                'progress_value' => $this->progress_value,
                'status' => $this->status,
                'location' => $this->location,
                'activity_date' => $this->activity_date,
                'created_by' => Auth::id(),
            ];

            if ($this->logbookId) {
                $logbook = TaskLogbook::findOrFail($this->logbookId);
                $logbook->update($data);
                $message = 'Logbook berhasil diperbarui.';
            } else {
                $logbook = TaskLogbook::create($data);
                $message = 'Logbook berhasil ditambahkan.';

                // Kirim notifikasi ke supervisor/assigned user
                $task = Task::find($this->taskId);
                if ($task->assignedUser) {
                    $task->assignedUser->notify(new LogbookCreated($logbook));
                }
            }

            // Handle file attachments if any
            if (!empty($this->attachments)) {
                foreach ($this->attachments as $file) {
                    $path = $file->store('logbook-attachments', 'public');
                    \App\Models\Attachment::create([
                        'task_logbook_id' => $logbook->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            // Update achieved value in TaskTarget if status is verified
            if ($this->status === 'verified') {
                $target = TaskTarget::findOrFail($this->taskTargetId);
                $target->achieved_value = $target->achieved_value + $this->progress_value;
                $target->save();
            }

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => $message,
            ]);

            $this->dispatch('logbook-saved');
            $this->closeModal();
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
    <flux:modal name="logbook-form" wire:model.self="isOpen" class="min-w-[600px]">
        <form wire:submit.prevent="save">
            <div>
                <flux:heading size="lg">
                    {{ $logbookId ? 'Edit Logbook' : 'Tambah Logbook Baru' }}
                </flux:heading>
            </div>

            <div class="my-4 space-y-4">
                <!-- Title -->
                <div>
                    <flux:field>
                        <flux:label>Judul Logbook <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="title" type="text" placeholder="Masukkan judul logbook" required />
                        @error('title')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Description -->
                <div>
                    <flux:field>
                        <flux:label>Deskripsi</flux:label>
                        <flux:textarea wire:model="description" placeholder="Deskripsi detail aktivitas..."
                            rows="4" />
                        @error('description')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Log Type and Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>Tipe Log <span class="text-red-500">*</span></flux:label>
                            <flux:select wire:model="log_type" placeholder="Pilih tipe log" required>
                                <option value="progress_update">Progress Update</option>
                                <option value="issue">Issue</option>
                                <option value="milestone">Milestone</option>
                                <option value="meeting">Meeting</option>
                                <option value="field_visit">Field Visit</option>
                                <option value="other">Other</option>
                            </flux:select>
                            @error('log_type')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Status <span class="text-red-500">*</span></flux:label>
                            <flux:select wire:model="status" placeholder="Pilih status" required>
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted</option>
                                <option value="verified">Verified</option>
                            </flux:select>
                            @error('status')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Progress Value -->
                <div>
                    <flux:field>
                        <flux:label>Nilai Progress <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="progress_value" type="number" step="0.01" min="0"
                            placeholder="0.00" required />
                        @error('progress_value')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>Tanggal Log <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model="log_date" type="date" required />
                            @error('log_date')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Tanggal Aktivitas <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model="activity_date" type="date" required />
                            @error('activity_date')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <flux:field>
                        <flux:label>Lokasi</flux:label>
                        <flux:input wire:model="location" type="text" placeholder="Lokasi aktivitas" />
                        @error('location')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Attachments -->
                <div>
                    <flux:field>
                        <flux:label>Lampiran</flux:label>
                        <input type="file" wire:model="attachments" multiple
                            class="block w-full px-2 py-1 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                            accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                        <flux:description>
                            Maksimal 10MB per file. Format: gambar, PDF, Word, Excel
                        </flux:description>
                        @error('attachments.*')
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
                    {{ $logbookId ? 'Perbarui' : 'Simpan' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
