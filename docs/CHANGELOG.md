# Changelog

## v2.0.0 — 2026-06-16

### Perubahan Arsitektur
- Ganti Aurora MySQL → RDS MySQL t3.micro (Free Tier compatible)
- Tambah Application Load Balancer (ALB) menggantikan akses manual per-IP
- Ganti region ap-southeast-1 → us-east-1 (N. Virginia)
- Tambah IAM Role (EC2-S3-Role) menggantikan static Access Key via `aws configure`
- Aktifkan S3 Object Ownership ACLs enabled (fix "bucket does not allow ACLs")

### Fitur Baru
- Tambah `stress.php` — CPU stress test untuk pengujian CloudWatch alarm
- Tambah redirect root `/` → `/index.php?action=login` via `.htaccess`
- Tambah GitHub Actions workflow untuk auto-sync ke repo web1/web2

### Fix
- `web2/config/database.php` — fix SERVER_ID yang salah (1 → 2)
- `web2/config/database.php` — fix SERVER_LABEL yang salah
- `web2/config/database.php` — hapus hardcoded endpoint dan password
- `web1/config/database.php` — update region ke us-east-1
- `database/init.sql` — hapus referensi Aurora endpoint
- `deploy-web1.sh`, `deploy-web2.sh` — hapus referensi Aurora endpoint
- `install-web1.sh`, `install-web2.sh` — tambah `libapache2-mod-php`, `a2enmod php8.5`

### Docs
- Rewrite `README.md` — sesuai arsitektur aktual (RDS MySQL, ALB, us-east-1)
- Tambah `TROUBLESHOOTING.md` — kumpulan masalah dan solusi dari implementasi nyata
- Tambah `GLOSSARY.md` — definisi istilah teknis
- Tambah `CHANGELOG.md`

---

## v1.0.0 — 2026-06-09

### Rilis Awal
- Implementasi awal dengan Aurora MySQL dan akses manual per-IP
- MVC PHP dengan front controller
- S3 media storage
- Web1 (full CRUD) dan Web2 (read-only via PHP restriction)
