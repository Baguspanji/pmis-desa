# Dokumentasi Laporan PDF Task

## Ringkasan
Sistem laporan PDF telah berhasil diimplementasikan untuk menghasilkan laporan detail task dalam format PDF yang sederhana dan mudah dipahami.

## Fitur yang Diimplementasikan

### 1. Laporan Detail Task (Individual)
- **URL**: `/tasks/{taskId}/report/pdf`
- **Route Name**: `tasks.report.pdf`
- **Konten Laporan**:
  - Informasi umum task (nama, deskripsi, program, penanggung jawab, status, prioritas, tanggal, anggaran)
  - Statistik (total target, target tercapai, progress, logbook)
  - Daftar sub-tasks lengkap dengan status dan penanggung jawab
  - Target task dengan nilai target dan tercapai
  - Realisasi anggaran dengan rincian transaksi
  - Logbook aktivitas dengan status verifikasi
  - Daftar lampiran file

### 2. Laporan Program (Semua Task dalam Program)
- **URL**: `/programs/{programId}/tasks/report/pdf`
- **Route Name**: `programs.tasks.report.pdf`
- **Konten Laporan**:
  - Informasi program
  - Statistik program (total task, task selesai, persentase, anggaran)
  - Daftar semua task dalam program
  - Detail lengkap setiap task (target, realisasi anggaran, logbook)

## Cara Penggunaan

### Dari Halaman Daftar Task
1. **Export PDF Program**: Klik tombol "Export PDF" di header halaman untuk menghasilkan laporan semua task dalam program
2. **Export PDF Task Individual**: Klik menu dropdown (•••) pada card task → pilih "Export PDF Task"

### Secara Langsung via URL
```
# Laporan task individual
https://domain.com/tasks/1/report/pdf

# Laporan program
https://domain.com/programs/1/tasks/report/pdf
```

## Desain PDF

### Karakteristik Desain
- **Warna**: Minimalis dengan penggunaan warna seminim mungkin
  - Abu-abu untuk border dan background table header (#f5f5f5, #ddd)
  - Hitam untuk teks utama (#333)
  - Warna status hanya untuk badge (biru, hijau, orange, merah - namun tetap subtle)
- **Font**: Arial, ukuran 11px untuk konten, 10px untuk tabel
- **Layout**: Clean dengan spacing yang jelas dan border yang sederhana
- **Tabel**: Grid sederhana dengan border 1px solid

### Elemen Visual
1. **Header**: Judul laporan dengan border bawah tebal
2. **Section Title**: Bold dengan underline tipis
3. **Info Grid**: Layout dua kolom untuk informasi detail
4. **Stats Grid**: Kotak statistik dengan border
5. **Tables**: Tabel standar untuk data terstruktur
6. **Footer**: Informasi tanggal cetak

## File yang Dibuat/Dimodifikasi

### File Baru
1. `app/Http/Controllers/TaskReportController.php` - Controller untuk generate PDF
2. `resources/views/reports/task-report.blade.php` - Template PDF task individual
3. `resources/views/reports/program-tasks-report.blade.php` - Template PDF program

### File yang Dimodifikasi
1. `composer.json` - Menambahkan package `barryvdh/laravel-dompdf`
2. `routes/web.php` - Menambahkan route PDF
3. `resources/views/livewire/project-task/index.blade.php` - Menambahkan tombol export

## Package yang Digunakan
- **barryvdh/laravel-dompdf** v3.1.1
  - Wrapper Laravel untuk Dompdf
  - Mendukung Laravel 12
  - Konversi HTML ke PDF

## Teknologi
- **Backend**: Laravel Controller
- **PDF Engine**: Dompdf
- **Template**: Blade dengan inline CSS
- **Paper Size**: A4 Portrait

## Keamanan
- Route dilindungi dengan middleware `auth`
- Hanya user yang terautentikasi dapat mengakses laporan
- Data task diambil menggunakan Eloquent dengan relasi yang tepat

## Performa
- Menggunakan eager loading untuk menghindari N+1 query problem
- PDF di-generate on-demand (tidak disimpan di server)
- Download langsung ke browser pengguna

## Catatan Tambahan
- Laporan otomatis mencantumkan tanggal generate
- Nama file menggunakan format: `Laporan_Task_{id}_{timestamp}.pdf`
- Mendukung page break untuk konten yang panjang
- Responsive terhadap data kosong (menampilkan "-" atau pesan yang sesuai)
