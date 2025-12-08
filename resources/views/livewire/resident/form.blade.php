<?php

use Livewire\Volt\Component;
use App\Models\Resident;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $residentId = null;
    public $nik = '';
    public $kk_number = '';
    public $full_name = '';
    public $birth_place = '';
    public $birth_date = '';
    public $gender = 'male';
    public $address = '';
    public $rt = '';
    public $rw = '';
    public $dusun = '';
    public $religion = '';
    public $marital_status = '';
    public $occupation = '';
    public $education = '';
    public $phone = '';
    public $is_active = true;
    public $is_head = false;

    public $isEdit = false;
    public $showModal = false;

    public array $genders = [];
    public array $religions = [];
    public array $maritalStatuses = [];
    public array $educationLevels = [];

    protected $listeners = ['open-resident-form' => 'openModal'];

    public function mount()
    {
        $this->genders = [['name' => 'Laki-laki', 'value' => 'male'], ['name' => 'Perempuan', 'value' => 'female']];

        $this->religions = [['name' => 'Islam', 'value' => 'Islam'], ['name' => 'Kristen', 'value' => 'Kristen'], ['name' => 'Katolik', 'value' => 'Katolik'], ['name' => 'Hindu', 'value' => 'Hindu'], ['name' => 'Buddha', 'value' => 'Buddha'], ['name' => 'Konghucu', 'value' => 'Konghucu']];

        $this->maritalStatuses = [['name' => 'Belum Kawin', 'value' => 'Belum Kawin'], ['name' => 'Kawin', 'value' => 'Kawin'], ['name' => 'Cerai Hidup', 'value' => 'Cerai Hidup'], ['name' => 'Cerai Mati', 'value' => 'Cerai Mati']];

        $this->educationLevels = [['name' => 'Tidak/Belum Sekolah', 'value' => 'Tidak/Belum Sekolah'], ['name' => 'Belum Tamat SD/Sederajat', 'value' => 'Belum Tamat SD/Sederajat'], ['name' => 'Tamat SD/Sederajat', 'value' => 'Tamat SD/Sederajat'], ['name' => 'SLTP/Sederajat', 'value' => 'SLTP/Sederajat'], ['name' => 'SLTA/Sederajat', 'value' => 'SLTA/Sederajat'], ['name' => 'Diploma I/II', 'value' => 'Diploma I/II'], ['name' => 'Akademi/Diploma III/S.Muda', 'value' => 'Akademi/Diploma III/S.Muda'], ['name' => 'Diploma IV/Strata I', 'value' => 'Diploma IV/Strata I'], ['name' => 'Strata II', 'value' => 'Strata II'], ['name' => 'Strata III', 'value' => 'Strata III']];
    }

    public function openModal($residentId = null)
    {
        $this->resetForm();

        if ($residentId) {
            $this->isEdit = true;
            $this->residentId = $residentId;
            $this->loadResident($residentId);
        } else {
            $this->isEdit = false;
        }

        $this->showModal = true;
    }

    public function loadResident($residentId)
    {
        $resident = Resident::findOrFail($residentId);

        $this->nik = $resident->nik;
        $this->kk_number = $resident->kk_number ?? '';
        $this->full_name = $resident->full_name;
        $this->birth_place = $resident->birth_place ?? '';
        $this->birth_date = $resident->birth_date ? $resident->birth_date->format('Y-m-d') : '';
        $this->gender = $resident->gender;
        $this->address = $resident->address ?? '';
        $this->rt = $resident->rt ?? '';
        $this->rw = $resident->rw ?? '';
        $this->dusun = $resident->dusun ?? '';
        $this->religion = $resident->religion ?? '';
        $this->marital_status = $resident->marital_status ?? '';
        $this->occupation = $resident->occupation ?? '';
        $this->education = $resident->education ?? '';
        $this->phone = $resident->phone ?? '';
        $this->is_active = $resident->is_active;
        $this->is_head = $resident->is_head;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->residentId = null;
        $this->nik = '';
        $this->kk_number = '';
        $this->full_name = '';
        $this->birth_place = '';
        $this->birth_date = '';
        $this->gender = 'male';
        $this->address = '';
        $this->rt = '';
        $this->rw = '';

        if (Auth::user()->role == 'kasun') {
            $this->dusun = Auth::user()->dusun;
        } else {
            $this->dusun = '';
        }

        $this->religion = '';
        $this->marital_status = '';
        $this->occupation = '';
        $this->education = '';
        $this->phone = '';
        $this->is_active = true;
        $this->is_head = false;
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'nik' => ['required', 'string', 'size:16', Rule::unique('residents', 'nik')->ignore($this->residentId)],
            'kk_number' => ['nullable', 'string', 'size:16'],
            'full_name' => ['required', 'string', 'max:255'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['required', 'in:male,female'],
            'address' => ['nullable', 'string'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'dusun' => ['nullable', 'string', 'max:50'],
            'religion' => ['nullable', 'string', 'max:50'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
            'is_head' => ['boolean'],
        ];

        $validated = $this->validate($rules);

        try {
            if ($this->isEdit) {
                $resident = Resident::findOrFail($this->residentId);

                $resident->update([
                    'nik' => $validated['nik'],
                    'kk_number' => $validated['kk_number'] ?? null,
                    'full_name' => $validated['full_name'],
                    'birth_place' => $validated['birth_place'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'gender' => $validated['gender'],
                    'address' => $validated['address'] ?? null,
                    'rt' => $validated['rt'] ?? null,
                    'rw' => $validated['rw'] ?? null,
                    'dusun' => $validated['dusun'] ?? null,
                    'religion' => $validated['religion'] ?? null,
                    'marital_status' => $validated['marital_status'] ?? null,
                    'occupation' => $validated['occupation'] ?? null,
                    'education' => $validated['education'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'is_active' => $this->is_active,
                    'is_head' => $this->is_head,
                ]);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'content' => 'Data warga berhasil diperbarui.',
                ]);
            } else {
                Resident::create([
                    'nik' => $validated['nik'],
                    'kk_number' => $validated['kk_number'] ?? null,
                    'full_name' => $validated['full_name'],
                    'birth_place' => $validated['birth_place'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'gender' => $validated['gender'],
                    'address' => $validated['address'] ?? null,
                    'rt' => $validated['rt'] ?? null,
                    'rw' => $validated['rw'] ?? null,
                    'dusun' => $validated['dusun'] ?? null,
                    'religion' => $validated['religion'] ?? null,
                    'marital_status' => $validated['marital_status'] ?? null,
                    'occupation' => $validated['occupation'] ?? null,
                    'education' => $validated['education'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'is_active' => $this->is_active,
                    'is_head' => $this->is_head,
                ]);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'content' => 'Data warga berhasil ditambahkan.',
                ]);
            }

            $this->closeModal();
            $this->dispatch('resident-saved');
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
    <flux:modal name="resident-form" wire:model.self="showModal" class="min-w-[800px] max-h-[90vh] overflow-y-auto">
        <form wire:submit.prevent="save">
            <div>
                <flux:heading size="lg">
                    {{ $isEdit ? 'Edit Data Warga' : 'Tambah Data Warga' }}
                </flux:heading>
            </div>

            <div class="my-4 space-y-4">
                <!-- NIK & KK Number -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>NIK (16 digit) <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model="nik" type="text" placeholder="Masukkan NIK" maxlength="16"
                                required />
                            @error('nik')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>No. KK (16 digit)</flux:label>
                            <flux:input wire:model="kk_number" type="text" placeholder="Masukkan No. KK"
                                maxlength="16" />
                            @error('kk_number')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Is Head -->
                <div class="flex flex-row justify-between">
                    <flux:checkbox wire:model="is_head" label="Kepala Keluarga" />
                    <flux:description>
                        (Tandai jika warga ini adalah kepala keluarga)
                    </flux:description>
                </div>

                <!-- Full Name -->
                <div>
                    <flux:field>
                        <flux:label>Nama Lengkap <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model="full_name" type="text" placeholder="Masukkan nama lengkap"
                            required />
                        @error('full_name')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Birth Place & Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>Tempat Lahir</flux:label>
                            <flux:input wire:model="birth_place" type="text" placeholder="Masukkan tempat lahir" />
                            @error('birth_place')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Tanggal Lahir</flux:label>
                            <flux:input wire:model="birth_date" type="date" />
                            @error('birth_date')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Gender -->
                <div>
                    <flux:field>
                        <flux:label>Jenis Kelamin <span class="text-red-500">*</span></flux:label>
                        <flux:select wire:model="gender" required>
                            @foreach ($genders as $gender)
                                <option value="{{ $gender['value'] }}">{{ $gender['name'] }}</option>
                            @endforeach
                        </flux:select>
                        @error('gender')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Address -->
                <div>
                    <flux:field>
                        <flux:label>Alamat</flux:label>
                        <flux:textarea wire:model="address" placeholder="Masukkan alamat lengkap" rows="2" />
                        @error('address')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- RT, RW, Dusun -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>RT</flux:label>
                            <flux:input wire:model="rt" type="text" placeholder="Contoh: 001" maxlength="10" />
                            @error('rt')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>RW</flux:label>
                            <flux:input wire:model="rw" type="text" placeholder="Contoh: 001" maxlength="10" />
                            @error('rw')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                    <div>
                        @can('kasun')
                            <flux:field>
                                <flux:label>Dusun</flux:label>
                                <flux:input wire:model="dusun" type="text" placeholder="Nama Dusun" disabled/>
                                @error('dusun')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </flux:field>
                        @else
                            <flux:field>
                                <flux:label>Dusun</flux:label>
                                <flux:input wire:model="dusun" type="text" placeholder="Nama Dusun" />
                                @error('dusun')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </flux:field>
                        @endcan
                    </div>
                </div>

                <!-- Religion & Marital Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>Agama</flux:label>
                            <flux:select wire:model="religion" placeholder="Pilih agama">
                                @foreach ($religions as $religion)
                                    <option value="{{ $religion['value'] }}">{{ $religion['name'] }}</option>
                                @endforeach
                            </flux:select>
                            @error('religion')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Status Perkawinan</flux:label>
                            <flux:select wire:model="marital_status" placeholder="Pilih status">
                                @foreach ($maritalStatuses as $status)
                                    <option value="{{ $status['value'] }}">{{ $status['name'] }}</option>
                                @endforeach
                            </flux:select>
                            @error('marital_status')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                </div>

                <!-- Occupation & Education -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>Pekerjaan</flux:label>
                            <flux:input wire:model="occupation" type="text" placeholder="Masukkan pekerjaan" />
                            @error('occupation')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Pendidikan</flux:label>
                            <flux:select wire:model="education" placeholder="Pilih pendidikan">
                                @foreach ($educationLevels as $level)
                                    <option value="{{ $level['value'] }}">{{ $level['name'] }}</option>
                                @endforeach
                            </flux:select>
                            @error('education')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    </div>
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

                <!-- Is Active -->
                <div class="flex flex-row justify-between">
                    <flux:checkbox wire:model="is_active" label="Status Aktif" />
                    <flux:description>
                        (Nonaktifkan untuk menandai warga tidak aktif/pindah)
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
