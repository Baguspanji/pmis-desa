<?php

use Livewire\Volt\Component;
use App\Models\Complaint;

new class extends Component {
    public $name = '';
    public $email = '';
    public $phone = '';
    public $message = '';
    public $showModal = false;

    protected $listeners = ['open-complaint-form' => 'openModal'];

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->modal('complaint-form')->show();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modal('complaint-form')->close();
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->message = '';
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'message' => ['required', 'string', 'min:10'],
        ];

        $validated = $this->validate($rules);

        try {
            Complaint::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'message' => $validated['message'],
                'status' => 'pending',
            ]);

            $this->dispatch('show-alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'content' => 'Pengaduan Anda telah berhasil dikirim. Kami akan segera menanggapi.',
            ]);

            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'title' => 'Terjadi Kesalahan',
                'content' => 'Gagal mengirim pengaduan. Silakan coba lagi.',
            ]);
        }
    }
}; ?>

<div>
    <flux:modal name="complaint-form" wire:model.self="showModal" class="md:max-w-sm md:ml-auto md:mr-6 md:mt-auto md:mb-14">
        <form wire:submit.prevent="save">
            <div>
                <flux:heading size="lg">
                    Form Pengaduan Masyarakat
                </flux:heading>
                <flux:text variant="subtle" class="mt-2">
                    Sampaikan keluhan atau pengaduan Anda kepada kami. Kami akan meresponnya dengan segera.
                </flux:text>
            </div>

            <div class="my-4 space-y-4">
                <!-- Name -->
                <div>
                    <flux:field>
                        <flux:label>Nama Lengkap <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="name" type="text" placeholder="Masukkan nama lengkap Anda" required />
                        @error('name')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Email -->
                <div>
                    <flux:field>
                        <flux:label>Email</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="Masukkan email Anda (opsional)" />
                        @error('email')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Phone -->
                <div>
                    <flux:field>
                        <flux:label>Nomor Telepon</flux:label>
                        <flux:input wire:model="phone" type="text" placeholder="Masukkan nomor telepon Anda (opsional)" />
                        @error('phone')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Message -->
                <div>
                    <flux:field>
                        <flux:label>Pesan Pengaduan <span class="text-red-500">*</span></flux:label>
                        <flux:textarea
                            wire:model="message"
                            placeholder="Jelaskan pengaduan Anda dengan detail (minimal 10 karakter)"
                            rows="5"
                            required
                        />
                        <flux:description>
                            Minimal 10 karakter. Jelaskan pengaduan Anda dengan jelas.
                        </flux:description>
                        @error('message')
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
                    Kirim Pengaduan
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
