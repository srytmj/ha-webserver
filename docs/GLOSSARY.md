# Glossary

Istilah teknis yang digunakan dalam proyek ini.

## AWS Services

**ALB (Application Load Balancer)**
Layanan load balancing AWS yang mendistribusikan trafik HTTP/HTTPS ke beberapa instance secara otomatis. Mendukung routing berbasis request, health check, dan round-robin. Berbeda dengan CLB (Classic Load Balancer) yang berbasis source IP.

**EC2 (Elastic Compute Cloud)**
Layanan komputasi virtual AWS. Setiap instance adalah server virtual yang berjalan di infrastruktur AWS. Proyek ini menggunakan dua instance t3.micro dengan Ubuntu 22.04 LTS.

**RDS (Relational Database Service)**
Layanan database terkelola AWS. Menangani backup, patching, dan recovery secara otomatis. Proyek ini menggunakan RDS MySQL t3.micro (Free Tier).

**S3 (Simple Storage Service)**
Layanan object storage AWS dengan durabilitas 99.999999999%. Digunakan untuk menyimpan foto pengguna yang dapat diakses via URL publik oleh kedua web instance.

**IAM (Identity and Access Management)**
Layanan manajemen akses AWS. Mengontrol siapa yang bisa mengakses resource AWS dan apa yang boleh dilakukan. Proyek ini menggunakan IAM Role untuk memberikan akses S3 ke EC2 tanpa static credentials.

**IAM Role**
Identitas IAM yang dapat di-attach ke EC2 instance. EC2 dengan IAM Role mendapat credentials sementara secara otomatis melalui Instance Metadata Service — lebih aman dibanding menyimpan Access Key di server.

**Security Group**
Firewall virtual di level instance AWS. Mengontrol traffic masuk (inbound) dan keluar (outbound) berdasarkan protokol, port, dan source IP/Security Group. Proyek ini memisahkan `webserver-sg` untuk EC2 dan `rds-sg` untuk RDS.

**VPC (Virtual Private Cloud)**
Jaringan virtual terisolasi di dalam AWS. Semua resource dalam proyek ini (EC2, RDS, ALB) berada dalam satu VPC untuk komunikasi internal yang aman.

**Target Group**
Komponen ALB yang mendefinisikan kumpulan instance yang menerima trafik. ALB meneruskan request ke target group, target group memilih instance berdasarkan algoritma (round-robin) dan status health check.

**Availability Zone (AZ)**
Pusat data fisik yang terpisah dalam satu AWS Region. Proyek ini menggunakan us-east-1a dan us-east-1b untuk memastikan redundansi.

**CloudWatch**
Layanan monitoring AWS. Mengumpulkan metrik (CPU, Network, Memory) dari EC2 dan layanan lain secara otomatis setiap 5 menit (basic monitoring, gratis). Mendukung alarm berbasis threshold.

---

## Arsitektur & Pola

**High Availability (HA)**
Desain sistem yang memastikan layanan tetap berjalan meski terjadi kegagalan pada satu komponen. Dicapai melalui redundansi (dua instance), distribusi beban (ALB), dan pemisahan tugas (R/W separation).

**MVC (Model-View-Controller)**
Pola arsitektur pemisahan concern dalam aplikasi: Model mengelola data (User.php), View menampilkan UI (login.php, read.php, dll.), Controller menangani logika request (loginController.php, mainController.php, userController.php).

**Front Controller**
Satu file entry point (`public/index.php`) yang menerima semua HTTP request dan mendistribusikannya ke controller yang sesuai berdasarkan parameter `action`.

**Round-Robin**
Algoritma distribusi trafik yang mengarahkan setiap request baru ke instance berikutnya secara bergantian. ALB menggunakan ini untuk memastikan beban terdistribusi merata ke Web1 dan Web2.

**Read/Write Separation**
Pemisahan operasi baca (SELECT) dan tulis (INSERT, UPDATE, DELETE) ke instance yang berbeda. Dalam proyek ini dilakukan di level aplikasi PHP: Web1 menangani semua CRUD, Web2 hanya Read.

**Least Privilege**
Prinsip keamanan: berikan hanya izin minimum yang diperlukan. Diterapkan melalui `rds-sg` yang hanya menerima koneksi dari `webserver-sg`, bukan dari internet langsung.

---

## PHP & Web

**PDO (PHP Data Objects)**
Extension PHP untuk koneksi database yang mendukung prepared statements. Digunakan untuk mencegah SQL injection pada semua query database.

**Prepared Statement**
Query database dengan parameter terpisah dari query string, mencegah SQL injection. Semua query di `User.php` menggunakan prepared statements via PDO.

**.htaccess**
File konfigurasi Apache per-direktori. Digunakan untuk mengaktifkan URL rewriting dan redirect root URL ke halaman login.

**AWS CLI**
Command-line tool untuk berinteraksi dengan layanan AWS. Digunakan di `userController.php` melalui `shell_exec()` untuk upload foto ke S3 dengan perintah `aws s3 cp`.

**Health Check**
Endpoint `/health.php` yang mengembalikan JSON `{"status":"healthy"}` saat database terhubung. ALB menggunakan endpoint ini secara berkala untuk memastikan instance siap menerima trafik.

**ACL (Access Control List)**
Mekanisme kontrol akses per-objek di S3. Harus diaktifkan di Object Ownership bucket (ACLs enabled) agar perintah `aws s3 cp --acl public-read` bisa berjalan.
