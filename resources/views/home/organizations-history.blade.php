<!-- Bagan Desa and History Section -->
<section id="bagan-desa" class="bg-white py-20 px-5">
    <div class="max-w-7xl mx-auto">
        <!-- Section Title -->
        <div class="mb-10 md:mb-12">
            <h2 class="text-xl md:text-3xl font-bold text-gray-900 mb-2">Bagan Desa</h2>
            <div class="w-50 h-1 bg-[#0A4194]"></div>
        </div>

        <!-- Organizational Charts -->
        <div class="grid md:grid-cols-2 gap-8 lg:gap-12 mb-16 md:items-stretch">
            <!-- Pemerintah Desa Chart -->
            <div class="flex flex-col items-center">
                <h3 class="text-xl md:text-2xl font-bold text-[#003d82] mb-6">Struktur Organisasi Pemerintah Desa</h3>
                <div
                    class="bg-linear-to-br from-blue-50 to-blue-100 rounded-lg p-6 md:p-8 border-2 border-blue-200 w-full grow flex flex-col">
                    <div class="space-y-8">
                        <!-- Kepala Desa -->
                        <div class="flex justify-center">
                            <div
                                class="bg-linear-to-br from-white to-blue-50 rounded-lg p-6 border-4 border-[#003d82] shadow-xl w-full max-w-xs text-center hover:shadow-2xl transition">
                                <div class="text-sm font-semibold text-gray-500 mb-2 tracking-wide">KEPALA DESA</div>
                                <div class="text-lg font-bold text-[#003d82]">Anam Syaifudin</div>
                            </div>
                        </div>

                        <!-- Connecting Line -->
                        <div class="flex justify-center">
                            <div class="w-1 h-8 bg-linear-to-b from-gray-400 to-gray-300"></div>
                        </div>

                        <!-- Devices -->
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div
                                class="bg-white rounded-lg p-4 border-3 border-[#E9A825] shadow-lg hover:shadow-xl transition text-center hover:scale-105">
                                <div class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                                    Sekretaris Desa</div>
                                <div class="text-sm font-bold text-gray-800">Nama Perangkat</div>
                            </div>
                            <div
                                class="bg-white rounded-lg p-4 border-3 border-[#7B3FF2] shadow-lg hover:shadow-xl transition text-center hover:scale-105">
                                <div class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">Bendahara
                                    Desa</div>
                                <div class="text-sm font-bold text-gray-800">Nama Perangkat</div>
                            </div>
                        </div>

                        <!-- Connecting Line -->
                        <div class="flex justify-center">
                            <div class="w-1 h-6 bg-linear-to-b from-gray-300 to-gray-200"></div>
                        </div>

                        <!-- Staff Level -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 w-full">
                            @for ($i = 0; $i < 4; $i++)
                                <div
                                    class="bg-white rounded-lg p-3 border-2 border-cyan-400 shadow-md hover:shadow-lg hover:border-cyan-500 transition text-center text-xs hover:scale-105">
                                    <div class="font-semibold text-gray-600 mb-1">Staff</div>
                                    <div class="font-bold text-gray-700">Nama</div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Badan Permasyarakatan Desa Chart -->
            <div class="flex flex-col items-center">
                <h3 class="text-xl md:text-2xl font-bold text-[#003d82] mb-6">Struktur Organisasi Badan Permasyarakatan Desa</h3>
                <div class="bg-linear-to-br from-blue-50 to-cyan-100 rounded-lg p-6 md:p-8 border-2 border-cyan-200 w-full grow flex flex-col">
                    <div class="space-y-8">
                        <!-- Ketua -->
                        <div class="flex justify-center">
                            <div
                                class="bg-white rounded-full p-4 border-4 border-[#FDB913] shadow-lg w-28 h-28 flex items-center justify-center hover:shadow-xl transition">
                                <div class="text-center">
                                    <div class="text-xs font-semibold text-gray-600 mb-1">KETUA</div>
                                    <div class="text-sm font-bold text-[#003d82]">Mark Tom</div>
                                </div>
                            </div>
                        </div>

                        <!-- Connecting Line -->
                        <div class="flex justify-center">
                            <div class="w-1 h-8 bg-linear-to-b from-gray-400 to-gray-300"></div>
                        </div>

                        <!-- Vice Leaders -->
                        <div class="flex flex-col items-center">
                            <div class="grid grid-cols-3 gap-6">
                                <div
                                    class="bg-white rounded-full p-4 border-4 border-[#7B3FF2] shadow-lg w-24 h-24 flex items-center justify-center hover:shadow-xl transition">
                                    <div class="text-center text-xs">
                                        <div class="font-semibold text-gray-600 mb-0.5">Tom</div>
                                        <div class="font-bold text-gray-700">Hiddles</div>
                                    </div>
                                </div>
                                <div
                                    class="bg-white rounded-full p-4 border-4 border-[#7B3FF2] shadow-lg w-24 h-24 flex items-center justify-center hover:shadow-xl transition">
                                    <div class="text-center text-xs">
                                        <div class="font-semibold text-gray-600 mb-0.5">Frankie</div>
                                        <div class="font-bold text-gray-700">James</div>
                                    </div>
                                </div>
                                <div
                                    class="bg-white rounded-full p-4 border-4 border-[#7B3FF2] shadow-lg w-24 h-24 flex items-center justify-center hover:shadow-xl transition">
                                    <div class="text-center text-xs">
                                        <div class="font-semibold text-gray-600 mb-0.5">Ella</div>
                                        <div class="font-bold text-gray-700">Linda</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Connecting Line -->
                        <div class="flex justify-center">
                            <div class="w-1 h-6 bg-linear-to-b from-gray-300 to-gray-200"></div>
                        </div>

                        <!-- Members -->
                        <div class="flex flex-col items-center w-full">
                            <div class="grid grid-cols-4 gap-4 w-full">
                                <div
                                    class="bg-white rounded-full p-3 border-3 border-cyan-400 shadow-md hover:shadow-lg transition w-20 h-20 flex items-center justify-center mx-auto">
                                    <div class="text-center text-xs">
                                        <div class="font-semibold text-gray-600">Member</div>
                                    </div>
                                </div>
                                <div
                                    class="bg-white rounded-full p-3 border-3 border-cyan-400 shadow-md hover:shadow-lg transition w-20 h-20 flex items-center justify-center mx-auto">
                                    <div class="text-center text-xs">
                                        <div class="font-semibold text-gray-600">Member</div>
                                    </div>
                                </div>
                                <div
                                    class="bg-white rounded-full p-3 border-3 border-cyan-400 shadow-md hover:shadow-lg transition w-20 h-20 flex items-center justify-center mx-auto">
                                    <div class="text-center text-xs">
                                        <div class="font-semibold text-gray-600">Member</div>
                                    </div>
                                </div>
                                <div
                                    class="bg-white rounded-full p-3 border-3 border-cyan-400 shadow-md hover:shadow-lg transition w-20 h-20 flex items-center justify-center mx-auto">
                                    <div class="text-center text-xs">
                                        <div class="font-semibold text-gray-600">Member</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
