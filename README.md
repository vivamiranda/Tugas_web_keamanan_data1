# WEB-SECURITY-LABS-NAMA_NIM

## Aplikasi Pengujian Kerentanan Web (Web Security Labs)

Repository ini dikembangkan untuk memenuhi tugas UTS, mendemonstrasikan empat kerentanan web utama dan teknik mitigasi yang sesuai, dengan struktur yang menyerupai DVWA (Damn Vulnerable Web App). Aplikasi ini menggunakan PHP dan MySQL.

---

## 🔑 STRUKTUR & KEAMANAN (Soal No. 2 & 3)

Aplikasi ini dibagi menjadi dua lingkungan utama untuk tujuan komparasi:

* **`/vulnerable/`**: Modul yang sengaja dibuat rentan (*insecure*) untuk tujuan pengujian eksploitasi.
* **`/secure/`**: Modul yang telah diperkuat dengan implementasi mitigasi keamanan.

### Fitur Autentikasi Lanjutan: CSRF Token

Pada *form* sensitif di versi **`/secure/`** (seperti `post.php` dan logika komentar), telah ditambahkan mekanisme pengamanan **CSRF Token** (*UUID-style*) untuk mencegah serangan *Cross-Site Request Forgery*. Token ini dihasilkan pada sesi dan divalidasi pada setiap *request* POST.

---

## 🛡️ ANALISIS KERENTANAN & MITIGASI

### 1. SQL Injection (SQLi)

| Mode | Mekanisme Rentan | Mekanisme Aman (Mitigasi) |
| :--- | :--- | :--- |
| **Rentan** | Menggunakan *Raw Query* (interpolasi input langsung). | |
| **Aman** | Menggunakan **Prepared Statements** (`$stmt->bind_param()`) untuk memisahkan *query* dari data input. |

### 2. Cross-Site Scripting (XSS)

| Modul | Mekanisme Rentan | Mekanisme Aman (Mitigasi) |
| :--- | :--- | :--- |
| **Komentar** | Mencetak *input* komentar secara mentah (*raw output*) di `artikel_view.php` (mode `vul`). | Menerapkan **Output Encoding** (`htmlspecialchars()`) saat mencetak komentar di *mode safe*. |

### 3. Broken Access Control (BAC) / IDOR

| Modul | Mekanisme Rentan | Mekanisme Aman (Mitigasi) |
| :--- | :--- | :--- |
| **Hapus Post** | Logika di `/vulnerable/delete_post_vul.php` **menghilangkan pengecekan kepemilikan**, rentan terhadap **IDOR** (menghapus *resource* milik orang lain). | Logika di `/secure/delete_post_safe.php` menggunakan **Verifikasi Kepemilikan** (`WHERE id=? AND author_id=?`) untuk memblokir IDOR. |

### 4. Kerentanan File Upload

| Modul | Mekanisme Rentan | Mekanisme Aman (Mitigasi) |
| :--- | :--- | :--- |
| **Upload** | Tidak ada validasi ekstensi, memungkinkan pengunggahan file eksekusi (`.php`). | Menerapkan **Whitelist Ekstensi** (hanya `jpg`, `png`, dll.) dan **Randomisasi Nama File** (`uniqid()`) untuk mencegah RCE. |

---

## 🖼️ TANGKAPAN LAYAR HASIL UJI UTAMA

(Tambahkan 3 hingga 5 *screenshot* yang membuktikan pengujian Anda di sini, misalnya SS 2.1 hingga SS 2.5 seperti yang kita bahas sebelumnya).

1.  **[SS]** *Authentication Bypass* Berhasil (Login Rentan).
2.  **[SS]** *Popup Alert* XSS Tereduksi (Komentar Rentan yang berhasil dieksploitasi).
3.  **[SS]** Pesan *Error* Setelah Mencoba Hapus Postingan Orang Lain (Hapus Aman) — Bukti Mitigasi BAC.
4.  **[SS]** Modul Upload Aman Menolak File PHP.
