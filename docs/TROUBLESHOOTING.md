# Troubleshooting

Kumpulan masalah yang ditemui selama implementasi beserta solusinya.

## Upload Foto

### "Unable to locate credentials"

**Penyebab:** EC2 tidak punya AWS credentials — IAM Role belum di-attach atau `aws configure` belum dijalankan.

**Solusi:**

Opsi A — IAM Role (direkomendasikan):
```
EC2 Console → pilih Web1 → Actions → Security → Modify IAM role → pilih EC2-S3-Role
```
Verifikasi:
```bash
aws sts get-caller-identity
```

Opsi B — AWS CLI manual:
```bash
aws configure
# Isi Access Key ID dan Secret Access Key dari IAM User s3-webserver-user
# Default region: us-east-1
```

---

### "The bucket does not allow ACLs" / "AccessControlListNotSupported"

**Penyebab:** Sejak April 2023, S3 bucket baru menggunakan Object Ownership = "Bucket owner enforced" yang menonaktifkan ACL. Perintah `aws s3 cp --acl public-read` gagal pada bucket dengan setting ini.

**Solusi:**
```
S3 Console → pilih bucket → Permissions → Object Ownership → Edit
→ pilih "ACLs enabled" → "Bucket owner preferred" → Save changes
```

---

### Upload gagal tanpa pesan error jelas

**Penyebab:** File terlalu besar (> 2MB) atau tipe file tidak diizinkan.

**Solusi:** Cek validasi di `userController.php`. Tipe yang diizinkan: `image/jpeg`, `image/png`, `image/gif`, `image/webp`. Maks 2MB.

---

## Akses Web

### "This site can't be reached"

**Penyebab:** Browser otomatis redirect ke HTTPS, padahal server hanya aktif di port 80 tanpa SSL certificate.

**Solusi:** Paksa akses dengan `http://` bukan `https://`:
```
http://[IP_INSTANCE]/index.php?action=login
http://[ALB_DNS_NAME]/index.php?action=login
```

---

### Halaman PHP tampil sebagai teks mentah (source code)

**Penyebab:** Module PHP Apache belum diaktifkan.

**Solusi:**
```bash
sudo a2enmod php8.5
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

### Apache error 403 Forbidden pada semua halaman

**Penyebab:** `DocumentRoot` salah atau permission folder tidak benar.

**Solusi:**
```bash
# Cek DocumentRoot di VirtualHost
sudo cat /etc/apache2/sites-available/ha-webserver.conf

# Pastikan path benar, contoh Web1:
# DocumentRoot /var/www/html/ha-webserver/web1/public

# Fix permission
sudo chown -R www-data:www-data /var/www/html/ha-webserver
sudo chmod -R 755 /var/www/html/ha-webserver

sudo systemctl restart apache2
```

---

## Database

### Koneksi RDS gagal

**Penyebab:** Security Group `rds-sg` tidak mengizinkan koneksi dari EC2.

**Solusi:**
```
VPC Console → Security Groups → rds-sg → Inbound rules → Edit
→ tambah rule: MySQL/Aurora (3306) → Source: Custom → pilih webserver-sg
```

Verifikasi dari EC2:
```bash
mysql -h [RDS_ENDPOINT] -u admin -p -e "SELECT 1"
```

---

### PHP error 500

**Penyebab:** Konfigurasi `database.php` salah atau RDS tidak bisa dijangkau.

**Solusi:**
```bash
sudo tail -f /var/log/apache2/error.log
sudo tail -f /var/log/apache2/ha-webserver-error.log
```

---

## Load Balancer

### Badge SERVER tidak bergantian saat curl dari satu terminal

**Penyebab:** ALB request-level routing bisa mengarahkan request dari satu sumber yang konsisten ke instance yang sama, terutama jika interval request terlalu cepat.

**Solusi:** Gunakan DNS ALB (bukan IP langsung), akses dari browser berbeda atau mode incognito:
```bash
# Dari terminal, tambah jeda antar request
for i in {1..10}; do
  curl -s http://[ALB_DNS_NAME]/health.php | grep server_id
  sleep 0.5
done
```

---

### ALB Target status "unhealthy"

**Penyebab:** `/health.php` tidak bisa diakses atau mengembalikan non-200.

**Solusi:**
```bash
# Test langsung ke instance
curl http://[IP_WEB1]/health.php
# Harusnya: {"status":"healthy","server_id":"1","db":"connected"}

# Jika gagal, cek Apache dan koneksi DB
sudo systemctl status apache2
sudo tail /var/log/apache2/error.log
```

---

## Git

### git clone gagal

**Penyebab:** `git` belum terinstall.

**Solusi:**
```bash
sudo apt install git -y
```

---

### git pull tidak update file di web server

**Penyebab:** File di `/var/www/html` adalah hasil `cp` dari folder repo, bukan symlink.

**Solusi:** Setelah `git pull`, copy ulang file yang berubah:
```bash
cd /var/www/html/ha-webserver
sudo git pull
# Tidak perlu cp ulang jika VirtualHost mengarah langsung ke web1/public atau web2/public
```
