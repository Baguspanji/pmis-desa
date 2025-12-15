<div class="py-20 px-5 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-[#035270] mb-2">Kritik dan Saran</h1>
            <p class="text-gray-600">Sampaikan kritik dan saran Anda untuk membantu kami meningkatkan pelayanan</p>
        </div>

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Section (1 column on lg) -->
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-8">
                    <livewire:suggestion-form-component />
                </div>
            </div>

            <!-- List Section (2 columns on lg) -->
            <div class="lg:col-span-2">
                <livewire:suggestion-list-component />
            </div>
        </div>
    </div>
</div>
