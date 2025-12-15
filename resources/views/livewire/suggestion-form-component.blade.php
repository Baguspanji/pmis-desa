<!-- Form Section -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-8 lg:mb-0">
    <h2 class="text-xl font-bold text-[#035270] mb-4">Kirim Kritik/Saran</h2>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded-lg mb-4 text-sm" role="alert">
            <span class="block">{{ session('message') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-4">
        <!-- Name -->
        <div>
            <flux:field>
                <flux:label>Nama</flux:label>
                <flux:input wire:model="name" type="text" placeholder="Masukkan nama Anda" />
                @error('name')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>
        </div>

        <!-- Email -->
        <div>
            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input wire:model="email" type="email" placeholder="email@example.com" />
                @error('email')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>
        </div>

        <!-- Phone -->
        <div>
            <flux:field>
                <flux:label>Nomor Telepon</flux:label>
                <flux:input wire:model="phone" type="text" placeholder="08xx xxxx xxxx" />
                @error('phone')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>
        </div>

        <!-- Category -->
        <div>
            <flux:field>
                <flux:label>Kategori <span class="text-red-500">*</span></flux:label>
                <flux:select wire:model="category" required>
                    <option value="suggestion">Saran</option>
                    <option value="criticism">Kritik</option>
                </flux:select>
                @error('category')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>
        </div>

        <!-- Message -->
        <div>
            <flux:field>
                <flux:label>Pesan <span class="text-red-500">*</span></flux:label>
                <flux:textarea wire:model="message" rows="4" placeholder="Tulis kritik atau saran Anda di sini..."
                    required />
                @error('message')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                class="w-full bg-[#035270] hover:bg-[#046B8C] text-white font-semibold py-2.5 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                </svg>
                Kirim
            </button>
        </div>
    </form>
</div>
