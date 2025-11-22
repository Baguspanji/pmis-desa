<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="font-sans text-gray-800">
    <!-- Hero Section with Header -->
    <section class="h-screen flex flex-col text-white bg-linear-to-r from-black/40 to-black/40"
        style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('assets/landing-page.png'); background-size: cover; background-position: center;">

        <!-- Header with Transparent Background -->
        <header class="bg-transparent py-4 w-full z-50">
            <div class="max-w-7xl mx-auto px-5 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/logo-pemerintah-mandiri.png') }}" alt="logo-pemerintah-mandiri"
                        class="h-14">
                </div>
                <div class="flex items-center gap-3">
                    <input type="text"
                        class="px-4 py-2 border border-white/30 bg-white/10 backdrop-blur-sm rounded-full w-64 text-sm text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50"
                        placeholder="Cari Program">
                    <a href="{{ route('dashboard') }}"
                        class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center cursor-pointer hover:bg-white/30 transition">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="8" r="4" stroke="white" stroke-width="2" />
                            <path d="M6 21V19C6 16.7909 7.79086 15 10 15H14C16.2091 15 18 16.7909 18 19V21"
                                stroke="white" stroke-width="2" />
                        </svg>
                    </a>
                </div>
            </div>
        </header>

        <!-- Hero Content -->
        <div class="flex-1 flex items-center justify-center text-center">
            <div class="max-w-4xl px-5">
                <h2 class="text-2xl md:text-3xl font-normal mb-3">Selamat Datang</h2>
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-5 capitalize">MABES Kertosari</h1>
                <h3 class="text-2xl md:text-3xl lg:text-4xl font-semibold mb-5">Manegemen APBDesa Kertosari</h3>
                <p class="text-base md:text-lg leading-tight mb-8 max-w-3xl mx-auto">
                    Desa Kertosari secara resmi ditetapkan sebagai Desa Wisata Kertosari melalui Keputusan Bupati
                    Pasuruan pada tahun 2019. Visi kami adalah menjadi kawasan wisata pedesaan yang menjadi percontohan
                    dalam pembangunan pariwisata berkelanjutan, dengan fokus pada konservasi alam, pelestarian seni
                    budaya, dan peningkatan kesejahteraan masyarakat (Perekonomian Kerakyatan). Kami dikelola oleh
                    Kelompok Sadar Wisata (POKDARWIS) yang beranggotakan masyarakat setempat.
                </p>
                <button onclick="document.getElementById('features').scrollIntoView({ behavior: 'smooth' })"
                    class="bg-[#FDB913] hover:bg-yellow-500 text-gray-800 px-9 py-2 rounded-full text-xl font-semibold transition-all hover:-translate-y-1 hover:shadow-xl cursor-pointer">
                    Selengkapnya
                </button>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 px-5 bg-gray-100">
        <div class="max-w-7xl mx-auto">
            <!-- Header Features Section -->
            <div class="text-center mb-8">
                <h2 class="text-4xl font-semibold text-gray-800 pb-6 inline-block relative">
                    Fitur - Fitur Kami
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-2/3 h-[0.20rem] bg-gray-300"></span>
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature Card 1 - Blue -->
                <div
                    class="bg-[#003d82] text-white p-4 rounded-2xl shadow-lg transition-all hover:-translate-y-2 hover:shadow-2xl flex items-center gap-5">
                    <div class="shrink-0">
                        <svg class="w-24 h-24 mb-2" viewBox="0 0 134 134" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M94.9167 72.5834H39.0833C37.6025 72.5834 36.1824 71.9951 35.1353 70.948C34.0882 69.901 33.5 68.4808 33.5 67C33.5 65.5192 34.0882 64.0991 35.1353 63.052C36.1824 62.0049 37.6025 61.4167 39.0833 61.4167H94.9167C96.3975 61.4167 97.8176 62.0049 98.8647 63.052C99.9118 64.0991 100.5 65.5192 100.5 67C100.5 68.4808 99.9118 69.901 98.8647 70.948C97.8176 71.9951 96.3975 72.5834 94.9167 72.5834Z"
                                fill="white" />
                            <path opacity="0.5"
                                d="M66.9998 11.1667C56.4313 11.1651 46.0794 14.1631 37.1474 19.8123C28.2153 25.4614 21.0701 33.5296 16.5421 43.079C12.0142 52.6284 10.2896 63.2669 11.5687 73.7577C12.8478 84.2485 17.0782 94.1609 23.7681 102.343L12.8024 113.303C12.0218 114.083 11.4903 115.078 11.2749 116.161C11.0596 117.244 11.1702 118.366 11.5926 119.387C12.0151 120.407 12.7306 121.279 13.6485 121.892C14.5665 122.506 15.6457 122.833 16.7498 122.833H66.9998C81.8078 122.833 96.0092 116.951 106.48 106.48C116.951 96.0094 122.833 81.8079 122.833 67C122.833 52.1921 116.951 37.9907 106.48 27.5199C96.0092 17.0491 81.8078 11.1667 66.9998 11.1667ZM50.2498 39.0834H83.7498C85.2306 39.0834 86.6508 39.6716 87.6979 40.7187C88.7449 41.7658 89.3332 43.1859 89.3332 44.6667C89.3332 46.1475 88.7449 47.5676 87.6979 48.6147C86.6508 49.6618 85.2306 50.25 83.7498 50.25H50.2498C48.7691 50.25 47.3489 49.6618 46.3018 48.6147C45.2548 47.5676 44.6665 46.1475 44.6665 44.6667C44.6665 43.1859 45.2548 41.7658 46.3018 40.7187C47.3489 39.6716 48.7691 39.0834 50.2498 39.0834ZM83.7498 94.9167H50.2498C48.7691 94.9167 47.3489 94.3285 46.3018 93.2814C45.2548 92.2343 44.6665 90.8141 44.6665 89.3334C44.6665 87.8526 45.2548 86.4324 46.3018 85.3854C47.3489 84.3383 48.7691 83.75 50.2498 83.75H83.7498C85.2306 83.75 86.6508 84.3383 87.6979 85.3854C88.7449 86.4324 89.3332 87.8526 89.3332 89.3334C89.3332 90.8141 88.7449 92.2343 87.6979 93.2814C86.6508 94.3285 85.2306 94.9167 83.7498 94.9167ZM94.9165 72.5834H39.0832C37.6024 72.5834 36.1822 71.9951 35.1352 70.948C34.0881 69.901 33.4998 68.4808 33.4998 67C33.4998 65.5192 34.0881 64.0991 35.1352 63.052C36.1822 62.0049 37.6024 61.4167 39.0832 61.4167H94.9165C96.3973 61.4167 97.8174 62.0049 98.8645 63.052C99.9116 64.0991 100.5 65.5192 100.5 67C100.5 68.4808 99.9116 69.901 98.8645 70.948C97.8174 71.9951 96.3973 72.5834 94.9165 72.5834Z"
                                fill="#0877CA" />
                            <path
                                d="M83.7498 94.9166H50.2498C48.769 94.9166 47.3489 94.3284 46.3018 93.2813C45.2547 92.2343 44.6665 90.8141 44.6665 89.3333C44.6665 87.8525 45.2547 86.4324 46.3018 85.3853C47.3489 84.3382 48.769 83.75 50.2498 83.75H83.7498C85.2306 83.75 86.6508 84.3382 87.6978 85.3853C88.7449 86.4324 89.3332 87.8525 89.3332 89.3333C89.3332 90.8141 88.7449 92.2343 87.6978 93.2813C86.6508 94.3284 85.2306 94.9166 83.7498 94.9166ZM83.7498 50.25H50.2498C48.769 50.25 47.3489 49.6617 46.3018 48.6147C45.2547 47.5676 44.6665 46.1474 44.6665 44.6666C44.6665 43.1859 45.2547 41.7657 46.3018 40.7186C47.3489 39.6716 48.769 39.0833 50.2498 39.0833H83.7498C85.2306 39.0833 86.6508 39.6716 87.6978 40.7186C88.7449 41.7657 89.3332 43.1859 89.3332 44.6666C89.3332 46.1474 88.7449 47.5676 87.6978 48.6147C86.6508 49.6617 85.2306 50.25 83.7498 50.25Z"
                                fill="white" />
                        </svg>
                    </div>
                    <div class="flex flex-col justify-center">
                        <h3 class="text-xl font-semibold mb-2">Administrasi</h3>
                        <p class="text-base leading-tight text-white/90 mb-3">
                            Pengelola sistem informasi pemerintah secara mandiri
                        </p>
                    </div>
                </div>

                <!-- Feature Card 2 - White -->
                <div
                    class="bg-[#E9F1FC] text-black p-4 rounded-2xl shadow-lg transition-all hover:-translate-y-2 hover:shadow-2xl flex items-center gap-5">
                    <div class="shrink-0">
                        <svg class="w-20 h-20 mb-2" viewBox="0 0 112 112" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M55.8413 0C70.6491 0 84.851 5.88191 95.3218 16.3525C105.793 26.8233 111.675 41.0251 111.675 55.833C111.675 70.6409 105.793 84.8427 95.3218 95.3135C84.851 105.784 70.6491 111.666 55.8413 111.666H5.59131C4.48737 111.666 3.40855 111.338 2.49072 110.725C1.57288 110.111 0.857566 109.24 0.435059 108.22C0.0125775 107.2 -0.0986159 106.077 0.116699 104.994C0.332013 103.911 0.863569 102.917 1.64404 102.136L12.6099 91.1758C5.92001 82.9942 1.6898 73.0816 0.410645 62.5908C-0.868497 52.1 0.85634 41.4615 5.38428 31.9121C9.91221 22.3627 17.0577 14.2946 25.9897 8.64551C34.9216 2.99655 45.273 -0.00147653 55.8413 0Z"
                                fill="#7599D0" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M38.5005 51.083L50.7505 58.083L56.0005 55.458L61.2505 58.083L73.5005 51.083V30.083C73.5005 28.6906 72.9474 27.3553 71.9628 26.3707C70.9782 25.3861 69.6429 24.833 68.2505 24.833H43.7505C42.3581 24.833 41.0227 25.3861 40.0382 26.3707C39.0536 27.3553 38.5005 28.6906 38.5005 30.083V51.083ZM35.0005 35.7985L31.707 37.5555C30.5873 38.1525 29.6509 39.0427 28.998 40.1308C28.3452 41.219 28.0004 42.4641 28.0005 43.733V44.8285L35.0005 48.934V35.7985ZM77.0005 48.934L84.0005 44.8285V43.733C84.0006 42.4641 83.6558 41.219 83.0029 40.1308C82.3501 39.0427 81.4137 38.1525 80.294 37.5555L77.0005 35.7985V48.934ZM64.075 60.568L84.0005 48.8885V71.6385L64.075 60.568ZM83.794 75.527L56.0005 60.085L28.207 75.527C28.585 77.0426 29.4591 78.3882 30.6901 79.3497C31.9212 80.3112 33.4385 80.8334 35.0005 80.833H77.0005C78.5625 80.8334 80.0798 80.3112 81.3108 79.3497C82.5419 78.3882 83.416 77.0426 83.794 75.527ZM47.926 60.568L28.0005 71.635V48.885L47.926 60.568ZM56.0005 31.77C61.8245 25.9145 76.388 36.159 56.0005 49.333C35.613 36.159 50.1765 25.918 56.0005 31.77Z"
                                fill="white" />
                        </svg>
                    </div>
                    <div class="flex flex-col justify-center">
                        <h3 class="text-xl font-semibold mb-2">Transparansi Program</h3>
                        <p class="text-base leading-tight mb-3">
                            Memantau jalanya program yang di kerjakan oleh pemerintah desa
                        </p>
                    </div>
                </div>

                <!-- Feature Card 3 - White -->
                <div
                    class="bg-[#003d82] text-white p-4 rounded-2xl shadow-lg transition-all hover:-translate-y-2 hover:shadow-2xl flex items-center gap-5">
                    <div class="shrink-0">
                        <svg class="w-20 h-20 mb-2" viewBox="0 0 112 112" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.5"
                                d="M55.8416 6.13504e-07C45.2731 -0.0015658 34.9212 2.99646 25.9892 8.64558C17.0571 14.2947 9.91186 22.3629 5.38392 31.9123C0.855984 41.4618 -0.868649 52.1002 0.410493 62.591C1.68964 73.0819 5.92001 82.9942 12.6099 91.1758L1.64422 102.136C0.863622 102.917 0.332053 103.912 0.116717 104.994C-0.0986185 106.077 0.0119484 107.2 0.434439 108.22C0.85693 109.24 1.57238 110.112 2.49033 110.725C3.40829 111.339 4.48754 111.666 5.59164 111.667H55.8416C70.6496 111.667 84.851 105.784 95.3218 95.3135C105.793 84.8427 111.675 70.6413 111.675 55.8333C111.675 41.0254 105.793 26.824 95.3218 16.3532C84.851 5.88242 70.6496 6.13504e-07 55.8416 6.13504e-07ZM39.0916 27.9167H72.5916C74.0724 27.9167 75.4926 28.5049 76.5396 29.552C77.5867 30.5991 78.175 32.0192 78.175 33.5C78.175 34.9808 77.5867 36.4009 76.5396 37.448C75.4926 38.4951 74.0724 39.0833 72.5916 39.0833H39.0916C37.6109 39.0833 36.1907 38.4951 35.1436 37.448C34.0966 36.4009 33.5083 34.9808 33.5083 33.5C33.5083 32.0192 34.0966 30.5991 35.1436 29.552C36.1907 28.5049 37.6109 27.9167 39.0916 27.9167ZM72.5916 83.75H39.0916C37.6109 83.75 36.1907 83.1618 35.1436 82.1147C34.0966 81.0676 33.5083 79.6475 33.5083 78.1667C33.5083 76.6859 34.0966 75.2657 35.1436 74.2187C36.1907 73.1716 37.6109 72.5833 39.0916 72.5833H72.5916C74.0724 72.5833 75.4926 73.1716 76.5396 74.2187C77.5867 75.2657 78.175 76.6859 78.175 78.1667C78.175 79.6475 77.5867 81.0676 76.5396 82.1147C75.4926 83.1618 74.0724 83.75 72.5916 83.75ZM83.7583 61.4167H27.925C26.4442 61.4167 25.024 60.8284 23.977 59.7813C22.9299 58.7343 22.3416 57.3141 22.3416 55.8333C22.3416 54.3525 22.9299 52.9324 23.977 51.8853C25.024 50.8382 26.4442 50.25 27.925 50.25H83.7583C85.2391 50.25 86.6592 50.8382 87.7063 51.8853C88.7534 52.9324 89.3416 54.3525 89.3416 55.8333C89.3416 57.3141 88.7534 58.7343 87.7063 59.7813C86.6592 60.8284 85.2391 61.4167 83.7583 61.4167Z"
                                fill="#095CAF" />
                            <circle cx="55.8418" cy="55.8333" r="41" fill="#095CAF" />
                            <path
                                d="M57.2989 27.4152C68.2414 27.4152 77.012 36.5802 77.012 47.6992C77.012 52.0902 76.1755 54.9132 73.6709 58.4792C70.3514 63.1672 67.1559 65.9472 67.1559 71.7182V76.1322C67.1559 81.6062 62.5197 85.8082 57.1336 85.8082C51.6825 85.8082 47.1103 81.4172 47.1103 75.8782C47.1103 74.5622 48.0698 73.4152 49.3659 73.4152C51.3282 73.4152 51.2446 75.9832 51.9551 77.8302C52.832 80.0792 54.6477 81.3942 57.0499 81.3942C60.1194 81.3942 62.813 79.0822 62.813 75.9622V71.0392C62.813 61.0872 72.8363 57.5662 72.8363 47.6142C72.8363 38.8082 65.7978 31.6582 57.1326 31.6582C51.7652 31.6582 47.9861 33.4402 44.769 37.7692C42.8687 40.3372 42.0125 42.2252 41.5952 45.4072C41.3443 47.3802 41.3049 49.9912 39.3406 49.9912C38.0455 49.9912 37.085 48.8452 37.085 47.5292C37.084 36.3272 46.2728 27.4152 57.2989 27.4152Z"
                                fill="white" />
                            <path
                                d="M56.7136 33.5253C64.4823 33.5253 71.1636 39.5523 71.1636 47.4453C71.1636 48.4633 70.4954 49.4813 69.4935 49.4813C68.407 49.4813 68.1571 48.1873 67.9898 47.1053C67.6138 44.7293 67.4249 43.2013 66.1514 41.1653C64.0641 37.8333 61.0369 36.7513 57.1319 36.7513C53.4355 36.7513 50.8049 38.0033 48.6113 40.9943C47.4205 42.6083 46.6775 44.7653 46.3026 46.7603C46.0103 48.3943 46.337 49.6523 44.6876 49.6523C43.6641 49.6523 43.1002 48.5703 43.1002 47.5293C43.1022 39.8483 49.1575 33.5253 56.7156 33.5253H56.7136ZM32.82 64.8433L40.0484 72.1883L38.2435 74.0223L31.0151 66.6773L32.82 64.8433ZM36.4564 61.1493L43.6858 68.4953L41.8799 70.3303L34.6505 62.9843L36.4564 61.1493ZM40.2148 57.2833L47.4432 64.6303L45.6383 66.4643L38.4099 59.1173L40.2148 57.2833ZM44.5813 52.9713L51.8097 60.3183L50.0039 62.1523L42.7755 54.8053L44.5813 52.9713Z"
                                fill="white" />
                            <path
                                d="M48.6301 49.0923L55.8585 56.4373L54.0536 58.2713L46.8252 50.9263L48.6301 49.0923ZM52.7447 44.5203L59.9731 51.8673L58.1673 53.7013L50.9388 46.3543L52.7447 44.5203ZM73.0797 28.3273L76.0144 31.3093L74.118 33.2363L71.1833 30.2543L73.0797 28.3273ZM76.8962 24.4233L79.8299 27.4053L77.9325 29.3323L74.9988 26.3503L76.8962 24.4233Z"
                                fill="white" />
                        </svg>
                    </div>
                    <div class="flex flex-col justify-center relative">
                        <h3 class="text-xl font-semibold mb-2">Bantuan Sosial</h3>
                        <p class="text-base leading-tight text-white/90 mb-3">
                            Menyimpan dan mengelola data penerima bantuan sosial
                        </p>
                        <span class="absolute -top-2 right-0 bg-[#FDB913] text-gray-800 text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            Coming Soon
                        </span>
                    </div>
                </div>

                <!-- Feature Card 4 - Blue -->
                <div
                    class="bg-[#E9F1FC] text-black p-4 rounded-2xl shadow-lg transition-all hover:-translate-y-2 hover:shadow-2xl flex items-center gap-5">
                    <div class="shrink-0">
                        <svg class="w-24 h-24 mb-2" viewBox="0 0 134 134" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M94.9167 72.5834H39.0833C37.6025 72.5834 36.1824 71.9951 35.1353 70.948C34.0882 69.901 33.5 68.4808 33.5 67C33.5 65.5192 34.0882 64.0991 35.1353 63.052C36.1824 62.0049 37.6025 61.4167 39.0833 61.4167H94.9167C96.3975 61.4167 97.8176 62.0049 98.8647 63.052C99.9118 64.0991 100.5 65.5192 100.5 67C100.5 68.4808 99.9118 69.901 98.8647 70.948C97.8176 71.9951 96.3975 72.5834 94.9167 72.5834Z"
                                fill="white" />
                            <path opacity="0.5"
                                d="M66.9998 11.1667C56.4313 11.1651 46.0794 14.1631 37.1474 19.8123C28.2153 25.4614 21.0701 33.5296 16.5421 43.079C12.0142 52.6284 10.2896 63.2669 11.5687 73.7577C12.8478 84.2485 17.0782 94.1609 23.7681 102.343L12.8024 113.303C12.0218 114.083 11.4903 115.078 11.2749 116.161C11.0596 117.244 11.1702 118.366 11.5926 119.387C12.0151 120.407 12.7306 121.279 13.6485 121.892C14.5665 122.506 15.6457 122.833 16.7498 122.833H66.9998C81.8078 122.833 96.0092 116.951 106.48 106.48C116.951 96.0094 122.833 81.8079 122.833 67C122.833 52.1921 116.951 37.9907 106.48 27.5199C96.0092 17.0491 81.8078 11.1667 66.9998 11.1667ZM50.2498 39.0834H83.7498C85.2306 39.0834 86.6508 39.6716 87.6979 40.7187C88.7449 41.7658 89.3332 43.1859 89.3332 44.6667C89.3332 46.1475 88.7449 47.5676 87.6979 48.6147C86.6508 49.6618 85.2306 50.25 83.7498 50.25H50.2498C48.7691 50.25 47.3489 49.6618 46.3018 48.6147C45.2548 47.5676 44.6665 46.1475 44.6665 44.6667C44.6665 43.1859 45.2548 41.7658 46.3018 40.7187C47.3489 39.6716 48.7691 39.0834 50.2498 39.0834ZM83.7498 94.9167H50.2498C48.7691 94.9167 47.3489 94.3285 46.3018 93.2814C45.2548 92.2343 44.6665 90.8141 44.6665 89.3334C44.6665 87.8526 45.2548 86.4324 46.3018 85.3854C47.3489 84.3383 48.7691 83.75 50.2498 83.75H83.7498C85.2306 83.75 86.6508 84.3383 87.6979 85.3854C88.7449 86.4324 89.3332 87.8526 89.3332 89.3334C89.3332 90.8141 88.7449 92.2343 87.6979 93.2814C86.6508 94.3285 85.2306 94.9167 83.7498 94.9167ZM94.9165 72.5834H39.0832C37.6024 72.5834 36.1822 71.9951 35.1352 70.948C34.0881 69.901 33.4998 68.4808 33.4998 67C33.4998 65.5192 34.0881 64.0991 35.1352 63.052C36.1822 62.0049 37.6024 61.4167 39.0832 61.4167H94.9165C96.3973 61.4167 97.8174 62.0049 98.8645 63.052C99.9116 64.0991 100.5 65.5192 100.5 67C100.5 68.4808 99.9116 69.901 98.8645 70.948C97.8174 71.9951 96.3973 72.5834 94.9165 72.5834Z"
                                fill="#0877CA" />
                            <path
                                d="M83.7498 94.9166H50.2498C48.769 94.9166 47.3489 94.3284 46.3018 93.2813C45.2547 92.2343 44.6665 90.8141 44.6665 89.3333C44.6665 87.8525 45.2547 86.4324 46.3018 85.3853C47.3489 84.3382 48.769 83.75 50.2498 83.75H83.7498C85.2306 83.75 86.6508 84.3382 87.6978 85.3853C88.7449 86.4324 89.3332 87.8525 89.3332 89.3333C89.3332 90.8141 88.7449 92.2343 87.6978 93.2813C86.6508 94.3284 85.2306 94.9166 83.7498 94.9166ZM83.7498 50.25H50.2498C48.769 50.25 47.3489 49.6617 46.3018 48.6147C45.2547 47.5676 44.6665 46.1474 44.6665 44.6666C44.6665 43.1859 45.2547 41.7657 46.3018 40.7186C47.3489 39.6716 48.769 39.0833 50.2498 39.0833H83.7498C85.2306 39.0833 86.6508 39.6716 87.6978 40.7186C88.7449 41.7657 89.3332 43.1859 89.3332 44.6666C89.3332 46.1474 88.7449 47.5676 87.6978 48.6147C86.6508 49.6617 85.2306 50.25 83.7498 50.25Z"
                                fill="white" />
                        </svg>
                    </div>
                    <div class="flex flex-col justify-center relative">
                        <h3 class="text-xl font-semibold mb-2">Web Profile</h3>
                        <p class="text-base leading-tight mb-3">
                            Website profil untuk meningkatkan potensi wisata desa
                        </p>
                        <span class="absolute -top-2 right-0 bg-[#FDB913] text-gray-800 text-xs font-bold px-3 py-1 rounded-full shadow-lg shadow-white/50">
                            Coming Soon
                        </span>
                    </div>
                </div>

                <!-- Feature Card 5 - Blue -->
                <div
                    class="bg-[#003d82] text-white p-4 rounded-2xl shadow-lg transition-all hover:-translate-y-2 hover:shadow-2xl flex items-center gap-5">
                    <div class="shrink-0">
                        <svg class="w-20 h-20 mb-2" viewBox="0 0 112 112" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M55.8413 0C70.6491 0 84.851 5.88191 95.3218 16.3525C105.793 26.8233 111.675 41.0251 111.675 55.833C111.675 70.6409 105.793 84.8427 95.3218 95.3135C84.851 105.784 70.6491 111.666 55.8413 111.666H5.59131C4.48737 111.666 3.40855 111.338 2.49072 110.725C1.57288 110.111 0.857566 109.24 0.435059 108.22C0.0125775 107.2 -0.0986159 106.077 0.116699 104.994C0.332013 103.911 0.863569 102.917 1.64404 102.136L12.6099 91.1758C5.92001 82.9942 1.6898 73.0816 0.410645 62.5908C-0.868497 52.1 0.85634 41.4615 5.38428 31.9121C9.91221 22.3627 17.0577 14.2946 25.9897 8.64551C34.9216 2.99655 45.273 -0.00147653 55.8413 0Z"
                                fill="#7599D0" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M38.5005 51.083L50.7505 58.083L56.0005 55.458L61.2505 58.083L73.5005 51.083V30.083C73.5005 28.6906 72.9474 27.3553 71.9628 26.3707C70.9782 25.3861 69.6429 24.833 68.2505 24.833H43.7505C42.3581 24.833 41.0227 25.3861 40.0382 26.3707C39.0536 27.3553 38.5005 28.6906 38.5005 30.083V51.083ZM35.0005 35.7985L31.707 37.5555C30.5873 38.1525 29.6509 39.0427 28.998 40.1308C28.3452 41.219 28.0004 42.4641 28.0005 43.733V44.8285L35.0005 48.934V35.7985ZM77.0005 48.934L84.0005 44.8285V43.733C84.0006 42.4641 83.6558 41.219 83.0029 40.1308C82.3501 39.0427 81.4137 38.1525 80.294 37.5555L77.0005 35.7985V48.934ZM64.075 60.568L84.0005 48.8885V71.6385L64.075 60.568ZM83.794 75.527L56.0005 60.085L28.207 75.527C28.585 77.0426 29.4591 78.3882 30.6901 79.3497C31.9212 80.3112 33.4385 80.8334 35.0005 80.833H77.0005C78.5625 80.8334 80.0798 80.3112 81.3108 79.3497C82.5419 78.3882 83.416 77.0426 83.794 75.527ZM47.926 60.568L28.0005 71.635V48.885L47.926 60.568ZM56.0005 31.77C61.8245 25.9145 76.388 36.159 56.0005 49.333C35.613 36.159 50.1765 25.918 56.0005 31.77Z"
                                fill="white" />
                        </svg>
                    </div>
                    <div class="flex flex-col justify-center">
                        <h3 class="text-xl font-semibold mb-2">Kecepatan</h3>
                        <p class="text-base leading-tight text-white/90 mb-3">
                            Kecepatan berinteraksi dengan masyarakat kini lebih mudah dan akurat
                        </p>
                    </div>
                </div>

                <!-- Feature Card 6 - White -->
                <div
                    class="bg-[#E9F1FC] text-black p-4 rounded-2xl shadow-lg transition-all hover:-translate-y-2 hover:shadow-2xl flex items-center gap-5">
                    <div class="shrink-0">
                        <svg class="w-24 h-24 mb-2" viewBox="0 0 134 134" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M94.9167 72.5834H39.0833C37.6025 72.5834 36.1824 71.9951 35.1353 70.948C34.0882 69.901 33.5 68.4808 33.5 67C33.5 65.5192 34.0882 64.0991 35.1353 63.052C36.1824 62.0049 37.6025 61.4167 39.0833 61.4167H94.9167C96.3975 61.4167 97.8176 62.0049 98.8647 63.052C99.9118 64.0991 100.5 65.5192 100.5 67C100.5 68.4808 99.9118 69.901 98.8647 70.948C97.8176 71.9951 96.3975 72.5834 94.9167 72.5834Z"
                                fill="white" />
                            <path opacity="0.5"
                                d="M66.9998 11.1667C56.4313 11.1651 46.0794 14.1631 37.1474 19.8123C28.2153 25.4614 21.0701 33.5296 16.5421 43.079C12.0142 52.6284 10.2896 63.2669 11.5687 73.7577C12.8478 84.2485 17.0782 94.1609 23.7681 102.343L12.8024 113.303C12.0218 114.083 11.4903 115.078 11.2749 116.161C11.0596 117.244 11.1702 118.366 11.5926 119.387C12.0151 120.407 12.7306 121.279 13.6485 121.892C14.5665 122.506 15.6457 122.833 16.7498 122.833H66.9998C81.8078 122.833 96.0092 116.951 106.48 106.48C116.951 96.0094 122.833 81.8079 122.833 67C122.833 52.1921 116.951 37.9907 106.48 27.5199C96.0092 17.0491 81.8078 11.1667 66.9998 11.1667ZM50.2498 39.0834H83.7498C85.2306 39.0834 86.6508 39.6716 87.6979 40.7187C88.7449 41.7658 89.3332 43.1859 89.3332 44.6667C89.3332 46.1475 88.7449 47.5676 87.6979 48.6147C86.6508 49.6618 85.2306 50.25 83.7498 50.25H50.2498C48.7691 50.25 47.3489 49.6618 46.3018 48.6147C45.2548 47.5676 44.6665 46.1475 44.6665 44.6667C44.6665 43.1859 45.2548 41.7658 46.3018 40.7187C47.3489 39.6716 48.7691 39.0834 50.2498 39.0834ZM83.7498 94.9167H50.2498C48.7691 94.9167 47.3489 94.3285 46.3018 93.2814C45.2548 92.2343 44.6665 90.8141 44.6665 89.3334C44.6665 87.8526 45.2548 86.4324 46.3018 85.3854C47.3489 84.3383 48.7691 83.75 50.2498 83.75H83.7498C85.2306 83.75 86.6508 84.3383 87.6979 85.3854C88.7449 86.4324 89.3332 87.8526 89.3332 89.3334C89.3332 90.8141 88.7449 92.2343 87.6979 93.2814C86.6508 94.3285 85.2306 94.9167 83.7498 94.9167ZM94.9165 72.5834H39.0832C37.6024 72.5834 36.1822 71.9951 35.1352 70.948C34.0881 69.901 33.4998 68.4808 33.4998 67C33.4998 65.5192 34.0881 64.0991 35.1352 63.052C36.1822 62.0049 37.6024 61.4167 39.0832 61.4167H94.9165C96.3973 61.4167 97.8174 62.0049 98.8645 63.052C99.9116 64.0991 100.5 65.5192 100.5 67C100.5 68.4808 99.9116 69.901 98.8645 70.948C97.8174 71.9951 96.3973 72.5834 94.9165 72.5834Z"
                                fill="#0877CA" />
                            <path
                                d="M83.7498 94.9166H50.2498C48.769 94.9166 47.3489 94.3284 46.3018 93.2813C45.2547 92.2343 44.6665 90.8141 44.6665 89.3333C44.6665 87.8525 45.2547 86.4324 46.3018 85.3853C47.3489 84.3382 48.769 83.75 50.2498 83.75H83.7498C85.2306 83.75 86.6508 84.3382 87.6978 85.3853C88.7449 86.4324 89.3332 87.8525 89.3332 89.3333C89.3332 90.8141 88.7449 92.2343 87.6978 93.2813C86.6508 94.3284 85.2306 94.9166 83.7498 94.9166ZM83.7498 50.25H50.2498C48.769 50.25 47.3489 49.6617 46.3018 48.6147C45.2547 47.5676 44.6665 46.1474 44.6665 44.6666C44.6665 43.1859 45.2547 41.7657 46.3018 40.7186C47.3489 39.6716 48.769 39.0833 50.2498 39.0833H83.7498C85.2306 39.0833 86.6508 39.6716 87.6978 40.7186C88.7449 41.7657 89.3332 43.1859 89.3332 44.6666C89.3332 46.1474 88.7449 47.5676 87.6978 48.6147C86.6508 49.6617 85.2306 50.25 83.7498 50.25Z"
                                fill="white" />
                        </svg>
                    </div>
                    <div class="flex flex-col justify-center">
                        <h3 class="text-xl font-semibold mb-2">Kecerdasan</h3>
                        <p class="text-base leading-tight mb-3">
                            Dengan bantuan teknologi yang memudahkan komunikasi desa dengan masyarakat
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Program Section -->
    <section class="py-20 px-5 bg-white">
        <div class="max-w-7xl mx-auto">
            <!-- Header Features Section -->
            <div class="text-center mb-8">
                <h2 class="text-4xl font-semibold text-gray-800 pb-6 inline-block relative">
                    Program Desa Kami
                    <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-2/3 h-[0.20rem] bg-gray-300"></span>
                </h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
                <!-- Program Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Card 1 - Light Blue -->
                    <div class="bg-[#E9F1FC] text-gray-800 p-6 rounded-3xl shadow-lg flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold mb-3 leading-tight">
                                Realisasi Pembuatan dan Penempatan Sarana Air Bersih
                            </h3>
                            <p class="text-sm leading-tight mb-4">
                                Akses terhadap air bersih dan sanitasi yang layak adalah hak dasar manusia dan kunci utama peningkatan kualitas hidup.
                            </p>
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="currentColor"/>
                                </svg>
                                <span class="text-sm font-semibold">Rp 200.000.000</span>
                            </div>
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z" fill="currentColor"/>
                                </svg>
                                <span class="text-sm font-semibold">2025</span>
                            </div>
                        </div>
                        <button class="bg-[#FDB913] hover:bg-yellow-500 text-gray-800 px-6 py-2 rounded-2xl text-sm font-semibold transition w-full">
                            Direncanakan
                        </button>
                    </div>

                    <!-- Card 2 - Blue -->
                    <div class="bg-[#003d82] text-white p-6 rounded-3xl shadow-lg flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold mb-3 leading-tight">
                                Pembangunan Gudang real estate BUMDES
                            </h3>
                            <p class="text-sm leading-tight mb-4">
                                Penguatan Badan Usaha Milik Desa sebagai mesin ekonomi desa (misalnya, unit usaha wisata, unit usaha pengolahan sampah, unit usaha penyewaan alat pertanian).
                            </p>
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="currentColor"/>
                                </svg>
                                <span class="text-sm font-semibold">Rp 200.000.000</span>
                            </div>
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z" fill="currentColor"/>
                                </svg>
                                <span class="text-sm font-semibold">2025</span>
                            </div>
                        </div>
                        <button class="bg-[#FDB913] hover:bg-yellow-500 text-gray-800 px-6 py-2 rounded-2xl text-sm font-semibold transition w-full">
                            Direncanakan
                        </button>
                    </div>

                    <!-- Card 3 - Blue -->
                    <div class="bg-[#003d82] text-white p-6 rounded-3xl shadow-lg flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold mb-3 leading-tight">
                                Realisasi Pembuatan dan Penempatan Sarana Air Bersih
                            </h3>
                            <p class="text-sm leading-tight mb-4">
                                Akses terhadap air bersih dan sanitasi yang layak adalah hak dasar manusia dan kunci utama peningkatan kualitas hidup. Di wilayah sasaran program ini, masyarakat masih menghadapi kendala signifikan
                            </p>
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="currentColor"/>
                                </svg>
                                <span class="text-sm font-semibold">Rp 200.000.000</span>
                            </div>
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z" fill="currentColor"/>
                                </svg>
                                <span class="text-sm font-semibold">2025</span>
                            </div>
                        </div>
                        <button class="bg-[#FDB913] hover:bg-yellow-500 text-gray-800 px-6 py-2 rounded-2xl text-sm font-semibold transition w-full">
                            Direncanakan
                        </button>
                    </div>

                    <!-- Card 4 - Light Blue -->
                    <div class="bg-[#E9F1FC] text-gray-800 p-6 rounded-3xl shadow-lg flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold mb-3 leading-tight">
                                Realisasi Pembuatan dan Penempatan Sarana Air Bersih
                            </h3>
                            <p class="text-sm leading-tight mb-4">
                                Akses terhadap air bersih dan sanitasi yang layak adalah hak dasar manusia dan kunci utama peningkatan kualitas hidup. Di wilayah sasaran program ini, masyarakat masih menghadapi kendala signifikan
                            </p>
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="currentColor"/>
                                </svg>
                                <span class="text-sm font-semibold">Rp 200.000.000</span>
                            </div>
                            <div class="flex items-center gap-2 mb-4">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z" fill="currentColor"/>
                                </svg>
                                <span class="text-sm font-semibold">2025</span>
                            </div>
                        </div>
                        <button class="bg-[#FDB913] hover:bg-yellow-500 text-gray-800 px-6 py-2 rounded-2xl text-sm font-semibold transition w-full">
                            Direncanakan
                        </button>
                    </div>
                </div>

                <!-- Program Details -->
                <div class="bg-gray-50 p-10 rounded-3xl">
                    <span
                        class="inline-block bg-[#FDB913] text-gray-800 px-4 py-1 rounded-2xl text-sm font-semibold mb-4">
                        Desa Wisata
                    </span>
                    <h2 class="text-2xl lg:text-3xl font-bold mb-5 text-gray-800 leading-tight">
                        Harmoni Alam dan Budaya di Kaki Gunung Baung
                    </h2>
                    <p class="text-base leading-tight text-gray-600 mb-6">
                        Visi kami adalah menjadi kawasan wisata
                        pedesaan yang menjadi percontohan dalam pembangunan pariwisata berkelanjutan, dengan fokus pada
                        konservasi alam, pelestarian seni budaya, dan peningkatan kesejahteraan masyarakat
                    </p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-800 text-base">
                            <span class="mr-3">●</span>
                            <span>Peraih Indonesia Sustainable Tourism Award (ISTA) 2019</span>
                        </li>
                        <li class="flex items-center text-gray-800 text-base">
                            <span class="mr-3">●</span>
                            <span>300 Besar Anugerah Desa Wisata Indonesia (ADWI)</span>
                        </li>
                        <li class="flex items-center text-gray-800 text-base">
                            <span class="mr-3">●</span>
                            <span>Green Rafting / Arung Jeram</span>
                        </li>
                        <li class="flex items-center text-gray-800 text-base">
                            <span class="mr-3">●</span>
                            <span>Air Terjun Coban Baung</span>
                        </li>
                        <li class="flex items-center text-gray-800 text-base">
                            <span class="mr-3 mt-1">●</span>
                            <span>Baung Camp & Camping Ground</span>
                        </li>
                    </ul>
                    <div class="relative h-60 mb-4">
                        <div
                            class="absolute z-20 left-12 top-16 w-60 h-48 rounded-2xl overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-300">
                            <img src="{{ asset('assets/program-1.png') }}" alt="program-1"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-linear-to-t from-black/10 to-transparent"></div>
                        </div>
                        <div
                            class="absolute z-10 right-12 top-0 w-60 h-48 rounded-2xl overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-300">
                            <img src="{{ asset('assets/program-2.png') }}" alt="program-2"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-linear-to-t from-black/10 to-transparent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#E9F1FC] py-10 px-5 border-t border-gray-200">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start gap-8">
            <!-- Footer Logo & Info -->
            <div class="flex-1 lg:min-w-[250px]">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-12 h-12">
                        <img src="{{ asset('logo_paskab.png') }}" alt="logo-paskab">
                    </div>
                    <h3 class="text-lg font-extrabold text-[#003d82] leading-[1.2rem] w-[126px]">
                        Pemerintah Desa Kertosari
                    </h3>
                </div>

                <div class="px-2">
                    <p class="text-sm font-semibold leading-tight text-gray-600">Jl. Sekolahan, Kademarangan Kidul,</p>
                    <p class="text-sm font-semibold leading-tight text-gray-600">Purwosari, Kec. Purwodadi, Pasuruan, Jawa</p>
                    <p class="text-sm font-semibold leading-tight text-gray-600">Timur 67162</p>
                    <div class="flex items-center gap-2 mt-2">
                        <svg class="w-6 h-6" viewBox="0 0 29 29" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.5984 4.04143C9.9251 3.08312 8.99286 2.65129 8.00413 2.66194C7.06601 2.6714 6.1526 3.07484 5.38986 3.61196C4.61189 4.16248 3.94853 4.8606 3.43711 5.66701C2.95452 6.43484 2.6061 7.34227 2.65319 8.22841C2.88036 12.4911 5.26627 17.046 8.61738 20.4167C11.9661 23.7838 16.4378 26.124 20.953 25.6768C21.8382 25.5892 22.6751 25.1361 23.359 24.5587C24.0663 23.9565 24.6442 23.2158 25.0575 22.3818C25.4577 21.5631 25.6966 20.619 25.5648 19.6997C25.4282 18.7414 24.895 17.8849 23.9145 17.317C23.7114 17.1985 23.5105 17.0762 23.3119 16.9502C23.1353 16.8402 22.947 16.7207 22.7186 16.5835C22.2522 16.2944 21.76 16.0496 21.2485 15.8523C20.7223 15.6595 20.1208 15.5329 19.4946 15.6192C18.8461 15.7091 18.2434 16.0168 17.722 16.5681C17.3206 16.994 16.7297 17.1265 15.8987 16.8851C15.0536 16.6391 14.1025 16.0286 13.2762 15.2028C12.4499 14.3793 11.8249 13.4163 11.5577 12.5467C11.294 11.6854 11.4106 11.0525 11.8225 10.6159C12.3793 10.0267 12.6759 9.37129 12.7383 8.67799C12.7995 8.00362 12.6324 7.36948 12.3934 6.81579C12.0356 5.98881 11.4282 5.16655 10.9527 4.52532C10.8331 4.365 10.7154 4.2033 10.5996 4.04025"
                                fill="#0A4194" />
                        </svg>
                        <p class="text-sm text-gray-600">+62 812-3241-1003</p>
                    </div>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="flex-1 lg:min-w-[250px] flex lg:justify-end">
                <div class="w-80 h-44 rounded-lg overflow-hidden shadow-lg">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15812.023107631881!2d112.753263!3d-7.78921105!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7d3a8f9cc3f3f%3A0x74849944d1e7755a!2sKertosari%2C%20Kec.%20Purwosari%2C%20Pasuruan%2C%20Jawa%20Timur!5e0!3m2!1sid!2sid!4v1763633763310!5m2!1sid!2sid"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
