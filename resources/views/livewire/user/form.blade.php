<?php

use Livewire\Volt\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

new class extends Component {
    public $userId = null;
    public $full_name = '';
    public $username = '';
    public $email = '';
    public $password = '';
    public $role = '';
    public $phone = '';
    public $is_active = true;

    public $isEdit = false;
    public $showModal = false;

    public array $roles = [];

    protected $listeners = ['open-user-form' => 'openModal'];

    public function mount()
    {
        $this->roles = [['name' => 'Admin', 'value' => 'admin'], ['name' => 'Operator', 'value' => 'operator'], ['name' => 'Kepala Desa', 'value' => 'kepala_desa'], ['name' => 'Staff', 'value' => 'staff']];
    }

    public function openModal($userId = null)
    {
        $this->resetForm();

        if ($userId) {
            $this->isEdit = true;
            $this->userId = $userId;
            $this->loadUser($userId);
        } else {
            $this->isEdit = false;
        }

        $this->showModal = true;
    }

    public function loadUser($userId)
    {
        $user = User::findOrFail($userId);

        $this->full_name = $user->full_name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->phone = $user->phone ?? '';
        $this->is_active = $user->is_active;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->full_name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->role = '';
        $this->phone = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->userId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'role' => ['required', 'in:admin,operator,kepala_desa,staff'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ];

        if ($this->isEdit) {
            // Password is optional for edit
            if ($this->password) {
                $rules['password'] = ['required', 'string', 'min:8'];
            }
        } else {
            // Password is required for create
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        $validated = $this->validate($rules);

        try {
            if ($this->isEdit) {
                $user = User::findOrFail($this->userId);

                $updateData = [
                    'full_name' => $validated['full_name'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'role' => $validated['role'],
                    'phone' => $validated['phone'] ?? null,
                    'is_active' => $this->is_active,
                ];

                // Only update password if provided
                if ($this->password) {
                    $updateData['password'] = Hash::make($this->password);
                }

                $user->update($updateData);

                session()->flash('message', 'Pengguna berhasil diperbarui.');
            } else {
                User::create([
                    'full_name' => $validated['full_name'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => $validated['role'],
                    'phone' => $validated['phone'] ?? null,
                    'is_active' => $this->is_active,
                ]);

                session()->flash('message', 'Pengguna berhasil ditambahkan.');
            }

            $this->closeModal();
            $this->dispatch('user-saved');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}; ?>

<div>
    <flux:modal name="user-form" wire:model.self="showModal" class="min-w-[600px]">
        <form wire:submit.prevent="save">
            <div>
                <flux:heading size="lg">
                    {{ $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                </flux:heading>
            </div>

            <div class="my-4 space-y-4">
                <!-- Full Name -->
                <div>
                    <flux:field>
                        <flux:label>Nama Lengkap <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="full_name" type="text" placeholder="Masukkan nama lengkap" required />
                        @error('full_name')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Username -->
                <div>
                    <flux:field>
                        <flux:label>Username <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="username" type="text" placeholder="Masukkan username" required />
                        @error('username')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Email -->
                <div>
                    <flux:field>
                        <flux:label>Email <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="email" type="email" placeholder="Masukkan email" required />
                        @error('email')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Phone -->
                <div>
                    <flux:field>
                        <flux:label>Nomor Telepon</flux:label>
                        <flux:input wire:model="phone" type="text" placeholder="Masukkan nomor telepon" />
                        @error('phone')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Role -->
                <div>
                    <flux:field>
                        <flux:label>Peran <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model="role" placeholder="Pilih peran" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role['value'] }}">{{ $role['name'] }}</option>
                            @endforeach
                        </flux:select>
                        @error('role')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Password -->
                <div>
                    <flux:field>
                        <flux:label>
                            Password
                            @if ($isEdit)
                                <span class="text-gray-500 text-xs ml-1">(Kosongkan jika tidak ingin mengubah)</span>
                            @else
                                <span class="text-red-500">*</span>
                            @endif
                        </flux:label>
                        <flux:input wire:model="password" type="password" placeholder="Masukkan password"
                            :required="!$isEdit" />
                        @error('password')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Is Active -->
                <div class="flex flex-row justify-between">
                    <flux:checkbox wire:model="is_active" label="Aktif" />
                    <flux:description>
                        (Nonaktifkan untuk mencegah pengguna login ke sistem)
                    </flux:description>
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
