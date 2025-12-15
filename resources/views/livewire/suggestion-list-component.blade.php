<div>
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Category Filter -->
            <div>
                <label for="filterCategory" class="block text-sm font-semibold text-gray-700 mb-2">
                    Filter Kategori
                </label>
                <select id="filterCategory" wire:model.live="filterCategory"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#035270] focus:border-transparent transition-all">
                    <option value="">Semua Kategori</option>
                    <option value="suggestion">Saran</option>
                    <option value="criticism">Kritik</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="filterStatus" class="block text-sm font-semibold text-gray-700 mb-2">
                    Filter Status
                </label>
                <select id="filterStatus" wire:model.live="filterStatus"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#035270] focus:border-transparent transition-all">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="reviewed">Reviewed</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Suggestions Grid -->
    <div class="flex flex-wrap gap-6">
        @forelse($suggestions as $suggestion)
            <div
                class="flex-1 min-w-[300px] bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-[#035270]">
                            {{ $suggestion->name || $suggestion->name != '' ? $suggestion->name : 'Anonymous' }}
                        </h3>

                        <div class="flex items-center justify-between gap-1">
                            <span
                                class="px-3 py-1 rounded text-xs font-semibold {{ $suggestion->category === 'criticism' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $suggestion->category === 'criticism' ? 'Kritik' : 'Saran' }}
                            </span>
                            {{-- <span
                                class="px-3 py-1 rounded text-xs font-semibold {{ $suggestion->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($suggestion->status === 'reviewed' ? 'bg-blue-100 text-blue-700' : ($suggestion->status === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')) }}">
                                {{ ucfirst($suggestion->status) }}
                            </span> --}}
                        </div>
                    </div>

                    <p class="text-gray-600 text-sm leading-relaxed line-clamp-3 mb-4">{{ $suggestion->message }}</p>

                    <div class="flex items-center text-xs text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $suggestion->created_at->diffForHumans() }}
                    </div>
                </div>

                @if ($suggestion->admin_response)
                    <div class="bg-[#035270]/5 border-t border-gray-100 p-4">
                        <p class="text-xs font-semibold text-[#035270] mb-1">Tanggapan Admin</p>
                        <p class="text-gray-700 text-sm line-clamp-2">{{ $suggestion->admin_response }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="w-full text-center py-10">
                <p class="text-gray-500 text-lg">Belum ada kritik dan saran</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($suggestions->hasPages())
        <div class="mt-6">
            {{ $suggestions->links() }}
        </div>
    @endif
</div>
