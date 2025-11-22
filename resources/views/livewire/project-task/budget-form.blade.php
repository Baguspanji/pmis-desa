<?php

use Livewire\Volt\Component;
use App\Models\BudgetRealization;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $taskId = null;
    public $budgetId = null;
    public $amount = '';
    public $description = '';
    public $category = '';
    public $transaction_type = 'expense';
    public $transaction_date = '';

    public $isEdit = false;
    public $showModal = false;

    protected $listeners = ['open-budget-form' => 'openModal'];

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function openModal($taskId, $budgetId = null)
    {
        $this->resetForm();
        $this->taskId = $taskId;

        if ($budgetId) {
            $this->isEdit = true;
            $this->budgetId = $budgetId;
            $this->loadBudget($budgetId);
        } else {
            $this->isEdit = false;
            $this->transaction_date = now()->format('Y-m-d');
        }

        $this->showModal = true;
    }

    public function loadBudget($budgetId)
    {
        $budget = BudgetRealization::findOrFail($budgetId);

        $this->amount = $budget->amount;
        $this->description = $budget->description ?? '';
        $this->category = $budget->category ?? '';
        $this->transaction_type = $budget->transaction_type;
        $this->transaction_date = $budget->transaction_date?->format('Y-m-d') ?? '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->budgetId = null;
        $this->amount = '';
        $this->description = '';
        $this->category = '';
        $this->transaction_type = 'expense';
        $this->transaction_date = now()->format('Y-m-d');
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'transaction_type' => ['required', 'in:income,expense'],
            'transaction_date' => ['required', 'date'],
        ];

        $validated = $this->validate($rules);

        try {
            if ($this->isEdit) {
                $budget = BudgetRealization::findOrFail($this->budgetId);

                $budget->update([
                    'amount' => $validated['amount'],
                    'description' => $validated['description'] ?? null,
                    'category' => $validated['category'],
                    'transaction_type' => $validated['transaction_type'],
                    'transaction_date' => $validated['transaction_date'],
                ]);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'content' => 'Realisasi anggaran berhasil diperbarui.',
                ]);
            } else {
                BudgetRealization::create([
                    'task_id' => $this->taskId,
                    'amount' => $validated['amount'],
                    'description' => $validated['description'] ?? null,
                    'category' => $validated['category'],
                    'transaction_type' => $validated['transaction_type'],
                    'transaction_date' => $validated['transaction_date'],
                    'created_by' => Auth::id(),
                ]);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'content' => 'Realisasi anggaran berhasil ditambahkan.',
                ]);
            }

            $this->closeModal();
            $this->dispatch('budget-saved');
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
    <flux:modal name="budget-form" wire:model.self="showModal" class="lg:min-w-[600px]">
        <form wire:submit.prevent="save">
            <div>
                <flux:heading size="lg">
                    {{ $isEdit ? 'Edit Realisasi Anggaran' : 'Tambah Realisasi Anggaran' }}
                </flux:heading>
            </div>

            <div class="my-4 space-y-4">
                <!-- Transaction Type -->
                {{-- <div>
                    <flux:field>
                        <flux:label>Tipe Transaksi <span class="text-red-500">*</span></flux:label>
                        <flux:radio.group wire:model.live="transaction_type" variant="segmented">
                            <flux:radio value="expense" label="Pengeluaran" />
                            <flux:radio value="income" label="Pemasukan" />
                        </flux:radio.group>
                        @error('transaction_type')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div> --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Amount -->
                    <div>
                        <flux:field>
                            <flux:label>Jumlah (Rp) <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model.live="amount" type="number" step="0.01" min="0"
                                placeholder="Masukkan jumlah" required />
                            @error('amount')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                            <div class="text-xs text-gray-400 mt-1">
                                Jumlah: Rp {{ number_format((float) $amount, 0, ',', '.') }}
                            </div>
                        </flux:field>
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <flux:field>
                            <flux:label>Tanggal Transaksi <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model="transaction_date" type="date" required />
                            @error('transaction_date')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Category -->
                <div>
                    <flux:field>
                        <flux:label>Kategori <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model="category" placeholder="Pilih kategori" required>
                            <flux:select.option value="Bahan Material">Bahan Material</flux:select.option>
                            <flux:select.option value="Alat dan Perlengkapan">Alat dan Perlengkapan</flux:select.option>
                            <flux:select.option value="Tenaga Kerja">Tenaga Kerja</flux:select.option>
                            <flux:select.option value="Transportasi">Transportasi</flux:select.option>
                            <flux:select.option value="Konsumsi">Konsumsi</flux:select.option>
                            <flux:select.option value="Pelatihan">Pelatihan</flux:select.option>
                            <flux:select.option value="Administrasi">Administrasi</flux:select.option>
                            <flux:select.option value="Sewa">Sewa</flux:select.option>
                            <flux:select.option value="Utilitas">Utilitas</flux:select.option>
                            <flux:select.option value="Dana Bantuan">Dana Bantuan</flux:select.option>
                            <flux:select.option value="Sumbangan">Sumbangan</flux:select.option>
                            <flux:select.option value="Lain-lain">Lain-lain</flux:select.option>
                        </flux:select>
                        @error('category')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Description -->
                <div>
                    <flux:field>
                        <flux:label>Deskripsi</flux:label>
                        <flux:textarea wire:model="description" rows="3" maxlength="255"
                            placeholder="Masukkan deskripsi transaksi (opsional)" />
                        @error('description')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                        <div class="text-xs text-gray-400 mt-1 text-right">
                            {{ strlen($description ?? '') }}/255 karakter
                        </div>
                    </flux:field>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="closeModal">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $isEdit ? 'Update' : 'Simpan' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
