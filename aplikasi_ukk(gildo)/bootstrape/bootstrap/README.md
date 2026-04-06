# Bootstrap Offline untuk Aplikasi Parkir

Folder ini berisi file Bootstrap dan Bootstrap Icons yang diperlukan untuk menjalankan aplikasi secara offline tanpa koneksi internet.

## Struktur Folder

```
bootstrap/
├── bootstrap.min.css                    # CSS Bootstrap utama (232KB)
├── bootstrap.bundle.min.js              # JavaScript Bootstrap dengan dependencies (80KB)
├── bootstrap-icons-1.11.0/              # Bootstrap Icons lengkap
│   ├── bootstrap-icons.min.css          # CSS untuk icons
│   ├── fonts/                           # Font files (woff2)
│   └── *.svg                            # Individual icon files (2000+ icons)
└── README.md                            # Dokumentasi ini
```

## Total Ukuran
- **CSS + JS + Icons**: ~2.5MB (compressed)
- **Expanded**: ~15MB (dengan semua SVG icons)

## Cara Penggunaan

File-file ini sudah terintegrasi dalam `includes/head.php` sehingga aplikasi akan menggunakan versi offline secara otomatis.

## Keuntungan

- ✅ **Offline-first**: Tidak memerlukan koneksi internet
- ✅ **Fast loading**: Semua assets lokal
- ✅ **Complete**: Semua komponen Bootstrap 5.3.7 + Icons 1.11.0
- ✅ **Optimized**: File minified untuk performa maksimal

## Cleanup yang Telah Dilakukan

- ❌ Dihapus: `bootstrap-5.3.7-dist/` (folder lengkap 25MB+)
- ❌ Dihapus: File duplikat dan tidak terpakai
- ✅ Dipertahankan: Hanya file yang digunakan aplikasi

## Troubleshooting

Jika ada masalah tampilan:
1. Pastikan path di `head.php` benar
2. Cek console browser untuk error 404
3. Pastikan file tidak corrupt (bandingkan checksum jika perlu)

---

**Versi**: Bootstrap 5.3.7 | Icons 1.11.0
**Status**: ✅ Optimized & Ready for UKK