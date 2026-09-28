<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SEKA WARUNG - Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r fixed left-0 top-0 bottom-0">

        <!-- LOGO -->
        <div class="p-6 border-b">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <span class="text-red-500 font-bold text-xl">S</span>
                </div>

                <div>
                    <h1 class="font-bold text-xl">SEKA WARUNG</h1>
                    <p class="text-sm text-gray-500">Admin Panel</p>
                </div>
            </div>
        </div>

        <!-- MENU -->
        <div class="p-5">

            <p class="text-xs font-bold text-gray-400 mb-4">
                MENU UTAMA
            </p>

            <nav class="space-y-2">

                <a href="/admin/dashboard"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-50 text-red-500 font-medium">
                    <span>▦</span>
                    Dashboard
                </a>

                <a href="/admin/produk"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                    <span>🛒</span>
                    Produk
                </a>

                <a href="/admin/penjualan"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                    <span>▤</span>
                    Penjualan
                </a>

                <a href="/admin/pelanggan"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                    <span>♟</span>
                    Pelanggan
                </a>

                <a href="/admin/kategori"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                    <span>◇</span>
                    Kategori
                </a>

                <a href="/admin/laporan"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                    <span>▥</span>
                    Laporan
                </a>

                <a href="/admin/pengaturan"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                    <span>⚙</span>
                    Pengaturan
                </a>

            </nav>

            <div class="border-t mt-6 pt-4">

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-red-500">
                    <span>↪</span>
                    Keluar
                </a>

            </div>

        </div>

    </aside>


    <!-- CONTENT -->
    <main class="ml-64 flex-1">

        <!-- HEADER -->
        <header class="bg-white border-b px-8 py-5 flex justify-between items-center">

            <div>
                <h2 class="text-2xl font-bold">
                    Dashboard
                </h2>

                <p class="text-gray-500 mt-1">
                    Kelola penjualan warung dengan mudah.
                </p>
            </div>

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500 font-bold">
                    A
                </div>

                <div>
                    <p class="font-semibold">
                        Admin Warung
                    </p>

                    <p class="text-xs text-gray-500">
                        Administrator
                    </p>
                </div>

            </div>

        </header>


        <!-- DASHBOARD -->
        <section class="p-8">

            <!-- WELCOME -->
            <div class="mb-8">

                <h3 class="text-2xl font-bold">
                    Selamat datang, Admin! 👋
                </h3>

                <p class="text-gray-500 mt-1">
                    Berikut ringkasan aktivitas warung hari ini.
                </p>

            </div>


            <!-- STATISTIC -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                <!-- PENJUALAN -->
                <div class="bg-white rounded-xl border p-5">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-sm text-gray-500">
                                Penjualan Hari Ini
                            </p>

                            <h3 class="text-2xl font-bold mt-2">
                                Rp 0
                            </h3>
                        </div>

                        <div class="w-11 h-11 bg-red-50 rounded-lg flex items-center justify-center">
                            🛍️
                        </div>

                    </div>

                    <p class="text-xs text-gray-400 mt-4">
                        Belum ada transaksi
                    </p>

                </div>


                <!-- PRODUK -->
                <div class="bg-white rounded-xl border p-5">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-sm text-gray-500">
                                Total Produk
                            </p>

                            <h3 class="text-2xl font-bold mt-2">
                                0
                            </h3>
                        </div>

                        <div class="w-11 h-11 bg-blue-50 rounded-lg flex items-center justify-center">
                            📦
                        </div>

                    </div>

                    <p class="text-xs text-gray-400 mt-4">
                        Produk tersedia
                    </p>

                </div>


                <!-- PELANGGAN -->
                <div class="bg-white rounded-xl border p-5">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-sm text-gray-500">
                                Total Pelanggan
                            </p>

                            <h3 class="text-2xl font-bold mt-2">
                                0
                            </h3>
                        </div>

                        <div class="w-11 h-11 bg-green-50 rounded-lg flex items-center justify-center">
                            👥
                        </div>

                    </div>

                    <p class="text-xs text-gray-400 mt-4">
                        Pelanggan terdaftar
                    </p>

                </div>


                <!-- STOK -->
                <div class="bg-white rounded-xl border p-5">

                    <div class="flex justify-between items-start">

                        <div>
                            <p class="text-sm text-gray-500">
                                Stok Produk
                            </p>

                            <h3 class="text-2xl font-bold mt-2">
                                0
                            </h3>
                        </div>

                        <div class="w-11 h-11 bg-yellow-50 rounded-lg flex items-center justify-center">
                            📊
                        </div>

                    </div>

                    <p class="text-xs text-gray-400 mt-4">
                        Total stok
                    </p>

                </div>

            </div>


            <!-- CONTENT BAWAH -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- TRANSAKSI -->
                <div class="bg-white rounded-xl border">

                    <div class="p-6 border-b">

                        <h3 class="font-bold text-lg">
                            Transaksi Terbaru
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Daftar transaksi terbaru.
                        </p>

                    </div>

                    <div class="p-10 text-center">

                        <div class="text-4xl mb-3">
                            🧾
                        </div>

                        <p class="font-medium">
                            Belum ada transaksi
                        </p>

                        <p class="text-sm text-gray-400 mt-1">
                            Transaksi akan muncul di sini.
                        </p>

                    </div>

                </div>


                <!-- STOK RENDAH -->
                <div class="bg-white rounded-xl border">

                    <div class="p-6 border-b">

                        <h3 class="font-bold text-lg">
                            Produk Stok Rendah
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Produk yang perlu diperhatikan.
                        </p>

                    </div>

                    <div class="p-10 text-center">

                        <div class="text-4xl mb-3">
                            📦
                        </div>

                        <p class="font-medium">
                            Semua stok aman
                        </p>

                        <p class="text-sm text-gray-400 mt-1">
                            Belum ada produk dengan stok rendah.
                        </p>

                    </div>

                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>