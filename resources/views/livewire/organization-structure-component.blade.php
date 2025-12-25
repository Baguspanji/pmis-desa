<?php

use Livewire\Volt\Component;
use App\Models\OrganizationStructure;

new class extends Component {
    public function mount()
    {
        // Initialization if needed
    }

    public function getGovernmentStructure()
    {
        return OrganizationStructure::government()
            ->whereNull('parent_id')
            ->with('children.children')
            ->first();
    }

    public function getConsultativeStructure()
    {
        return OrganizationStructure::consultativeBody()
            ->whereNull('parent_id')
            ->with('children.children')
            ->first();
    }
}; ?>

<div>
    <!-- Organizational Charts -->
    <div class="grid md:grid-cols-2 gap-8 lg:gap-12 mb-16 md:items-stretch">
        <!-- Pemerintah Desa Chart -->
        <div class="flex flex-col items-center">
            <h3 class="text-xl md:text-2xl font-bold text-[#003d82] mb-6">Struktur Organisasi Pemerintah Desa</h3>
            <div
                class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg p-6 md:p-8 border-2 border-blue-200 w-full grow flex flex-col">
                <div class="space-y-8">
                    @if($government = $this->getGovernmentStructure())
                        <!-- Kepala Desa -->
                        <div class="flex justify-center">
                            <div
                                class="bg-linear-to-br from-white to-blue-50 rounded-lg p-6 border-4 border-[#003d82] shadow-xl w-full max-w-xs text-center hover:shadow-2xl transition">
                                <div class="text-sm font-semibold text-gray-500 mb-2 tracking-wide">{{ strtoupper($government->position) }}</div>
                                <div class="text-lg font-bold text-[#003d82]">{{ $government->name }}</div>
                            </div>
                        </div>

                        @if($government->children->count() > 0)
                            <!-- Connecting Line -->
                            <div class="flex justify-center">
                                <div class="w-1 h-8 bg-linear-to-b from-gray-400 to-gray-300"></div>
                            </div>

                            <!-- Vice Level (Sekretaris, Bendahara) -->
                            @php
                                $viceMembers = $government->children->filter(fn($child) => $child->level === 'vice');
                                $staffMembers = $government->children->filter(fn($child) => $child->level === 'staff');
                            @endphp

                            @if($viceMembers->count() > 0)
                                <div class="grid grid-cols-2 gap-4 w-full">
                                    @foreach($viceMembers as $vice)
                                        <div
                                            class="bg-white rounded-lg p-4 border-3 border-[#E9A825] shadow-lg hover:shadow-xl transition text-center hover:scale-105">
                                            <div class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                                                {{ $vice->position }}
                                            </div>
                                            <div class="text-sm font-bold text-gray-800">{{ $vice->name }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($staffMembers->count() > 0)
                                <!-- Connecting Line -->
                                <div class="flex justify-center">
                                    <div class="w-1 h-6 bg-linear-to-b from-gray-300 to-gray-200"></div>
                                </div>

                                <!-- Staff Level -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 w-full">
                                    @foreach($staffMembers as $staff)
                                        <div
                                            class="bg-white rounded-lg p-3 border-2 border-cyan-400 shadow-md hover:shadow-lg hover:border-cyan-500 transition text-center text-xs hover:scale-105">
                                            <div class="font-semibold text-gray-600 mb-1">{{ $staff->position }}</div>
                                            <div class="font-bold text-gray-700">{{ $staff->name }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="text-center py-10 text-gray-500">
                            Belum ada data struktur organisasi pemerintah desa
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Badan Permasyarakatan Desa Chart -->
        <div class="flex flex-col items-center">
            <h3 class="text-xl md:text-2xl font-bold text-[#003d82] mb-6">Struktur Organisasi Badan Permasyarakatan Desa</h3>
            <div class="bg-linear-to-br from-blue-50 to-cyan-100 rounded-lg p-6 md:p-8 border-2 border-cyan-200 w-full grow flex flex-col">
                <div class="space-y-8">
                    @if($consultative = $this->getConsultativeStructure())
                        <!-- Ketua -->
                        <div class="flex justify-center">
                            <div
                                class="bg-white rounded-full p-4 border-4 border-[#FDB913] shadow-lg w-28 h-28 flex items-center justify-center hover:shadow-xl transition">
                                <div class="text-center">
                                    <div class="text-xs font-semibold text-gray-600 mb-1">{{ strtoupper($consultative->position) }}</div>
                                    <div class="text-sm font-bold text-[#003d82]">{{ $consultative->name }}</div>
                                </div>
                            </div>
                        </div>

                        @if($consultative->children->count() > 0)
                            <!-- Connecting Line -->
                            <div class="flex justify-center">
                                <div class="w-1 h-8 bg-linear-to-b from-gray-400 to-gray-300"></div>
                            </div>

                            @php
                                $viceLeaders = $consultative->children->filter(fn($child) => $child->level === 'vice');
                                $members = $consultative->children->filter(fn($child) => $child->level === 'member');
                            @endphp

                            <!-- Vice Leaders -->
                            @if($viceLeaders->count() > 0)
                                <div class="flex flex-col items-center">
                                    <div class="grid grid-cols-{{ min($viceLeaders->count(), 3) }} gap-6">
                                        @foreach($viceLeaders as $vice)
                                            <div
                                                class="bg-white rounded-full p-4 border-4 border-[#7B3FF2] shadow-lg w-24 h-24 flex items-center justify-center hover:shadow-xl transition">
                                                <div class="text-center text-xs">
                                                    <div class="font-semibold text-gray-600 mb-0.5">{{ explode(' ', $vice->name)[0] ?? 'Officer' }}</div>
                                                    <div class="font-bold text-gray-700">{{ explode(' ', $vice->name)[1] ?? '' }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($members->count() > 0)
                                <!-- Connecting Line -->
                                <div class="flex justify-center">
                                    <div class="w-1 h-6 bg-linear-to-b from-gray-300 to-gray-200"></div>
                                </div>

                                <!-- Members -->
                                <div class="flex flex-col items-center w-full">
                                    <div class="grid grid-cols-{{ min($members->count(), 4) }} gap-4 w-full">
                                        @foreach($members as $member)
                                            <div
                                                class="bg-white rounded-full p-3 border-3 border-cyan-400 shadow-md hover:shadow-lg transition w-20 h-20 flex items-center justify-center mx-auto">
                                                <div class="text-center text-xs">
                                                    <div class="font-semibold text-gray-600">{{ $member->name }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="text-center py-10 text-gray-500">
                            Belum ada data struktur organisasi badan permasyarakatan desa
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
