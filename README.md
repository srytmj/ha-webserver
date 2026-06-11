## Prasyarat AWS

Sebelum deploy, pastikan sudah menyiapkan:

- [ ] S3 Bucket dengan public read policy
- [ ] IAM User `s3-webserver-user` dengan Access Key (untuk AWS CLI)
- [ ] Security Group `sg-webserver` — inbound SSH (22) My IP + HTTP (80) Anywhere
- [ ] Security Group `sg-rds` — inbound MySQL 3306 dari `sg-webserver`
- [ ] DB Subnet Group dengan minimal 2 Availability Zone
- [ ] RDS MySQL `ha-rds-mysql` — Free Tier (db.t4g.micro), menggunakan `sg-rds`
- [ ] EC2 Web1 dan Web2 — t2.micro Ubuntu 22.04, menggunakan `sg-webserver`

## Langkah Deploy

### Step 1 — Edit config sebelum clone (atau edit setelah clone)

**web1/config/database.php:**
```php
define('DB_HOST',      '[RDS_ENDPOINT]');
define('DB_PASS',      '[PASSWORD_RDS]');
define('SERVER_ID',    '1');
define('SERVER_LABEL', 'Web Server 1 — Master Node');
define('S3_BUCKET',    '[NAMA_BUCKET_S3]');
define('S3_BASE_URL',  'https://[NAMA_BUCKET_S3].s3.ap-southeast-1.amazonaws.com/');
```

**web2/config/database.php:**
```php
define('DB_HOST',      '[RDS_ENDPOINT]');   // endpoint sama dengan Web1
define('DB_PASS',      '[PASSWORD_RDS]');
define('SERVER_ID',    '2');
define('SERVER_LABEL', 'Web Server 2 — Replica Node');
define('S3_BUCKET',    '[NAMA_BUCKET_S3]');
define('S3_BASE_URL',  'https://[NAMA_BUCKET_S3].s3.ap-southeast-1.amazonaws.com/');
```

> Kedua instance menggunakan RDS endpoint yang **sama** karena Free Tier hanya punya single
> RDS instance. Pemisahan Read/Write dilakukan di level PHP — Web2 mengembalikan 403 untuk
> semua operasi tulis.

---

### Step 2 — Setup EC2 Web1

SSH ke Web1:
```bash
ssh -i webserver1.pem ubuntu@[IP_WEB1]
```

Install dependensi:
```bash
sudo apt-get update && sudo apt-get upgrade -y
sudo apt-get install -y apache2 php8.1 php8.1-mysql php8.1-curl \
    php8.1-mbstring php8.1-xml php8.1-zip unzip curl git
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip && sudo ./aws/install
sudo a2enmod rewrite && sudo systemctl restart apache2
```

Clone repo dan deploy Web1:
```bash
cd /var/www/html
sudo git clone https://github.com/srytmj/ha-webserver .
sudo cp -r web1/* .
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
```

Konfigurasi Apache:
```bash
sudo nano /etc/apache2/sites-available/000-default.conf
# Ubah DocumentRoot menjadi /var/www/html/public
# Tambahkan AllowOverride All di block <Directory>
sudo systemctl restart apache2
```

Konfigurasi AWS CLI:
```bash
aws configure
# AWS Access Key ID     : [dari IAM User s3-webserver-user]
# AWS Secret Access Key : [dari IAM User s3-webserver-user]
# Default region        : ap-southeast-1
# Default output format : json

# Verifikasi:
aws s3 ls s3://[NAMA_BUCKET_S3]/
```

Edit config database Web1:
```bash
sudo nano /var/www/html/config/database.php
# Isi DB_HOST, DB_PASS, S3_BUCKET, S3_BASE_URL
```

---

### Step 3 — Inisialisasi Database

Dari EC2 Web1, jalankan SQL init:
```bash
mysql -h [RDS_ENDPOINT] -u admin -p < /var/www/html/database/init.sql
```

Atau manual:
```bash
mysql -h [RDS_ENDPOINT] -u admin -p
```
```sql
CREATE DATABASE ha_webserver;
USE ha_webserver;
CREATE TABLE user (
    id   INT NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    nim  VARCHAR(20) NOT NULL,
    foto VARCHAR(500) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nim (nim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### Step 4 — Setup EC2 Web2

SSH ke Web2:
```bash
ssh -i webserver2.pem ubuntu@[IP_WEB2]
```

Install dependensi (sama dengan Web1):
```bash
sudo apt-get update && sudo apt-get upgrade -y
sudo apt-get install -y apache2 php8.1 php8.1-mysql php8.1-curl \
    php8.1-mbstring php8.1-xml php8.1-zip unzip curl git
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip && sudo ./aws/install
sudo a2enmod rewrite && sudo systemctl restart apache2
```

Clone repo dan deploy Web2:
```bash
cd /var/www/html
sudo git clone https://github.com/srytmj/ha-webserver .
sudo cp -r web2/* .
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
```

Konfigurasi Apache dan AWS CLI (sama dengan Web1, ganti key pair).

Edit config database Web2:
```bash
sudo nano /var/www/html/config/database.php
# SERVER_ID = '2', SERVER_LABEL = 'Web Server 2 — Replica Node'
# DB_HOST, DB_PASS, S3_BUCKET, S3_BASE_URL sama dengan Web1
```

---

### Step 5 — Update Kode (jika ada perubahan)

```bash
cd /var/www/html
sudo git pull
sudo cp -r web1/* .   # di Web1
# atau
sudo cp -r web2/* .   # di Web2
sudo chown -R www-data:www-data /var/www/html
```

---

## Verifikasi

```bash
# Health check
curl http://[IP_WEB1]/health.php
# {"status":"healthy","server_id":"1","db":"connected"}

curl http://[IP_WEB2]/health.php
# {"status":"healthy","server_id":"2","db":"connected"}

# Web2 harus tolak operasi tulis
curl -I http://[IP_WEB2]/index.php?action=create
# HTTP/1.1 403 Forbidden
```

## Akses Aplikasi

| Instance | URL | Mode |
|----------|-----|------|
| Web1 | `http://[IP_WEB1]/index.php?action=login` | Read + Write |
| Web2 | `http://[IP_WEB2]/index.php?action=login` | Read Only |

Login default: `admin` / `password123` — **ganti di `loginController.php` untuk produksi.**

## Perbedaan Web1 vs Web2

| Fitur | Web1 (Master) | Web2 (Replica) |
|-------|---------------|----------------|
| Login | ✅ | ✅ |
| Lihat Data | ✅ | ✅ |
| Tambah User | ✅ | ❌ 403 |
| Edit User | ✅ | ❌ 403 |
| Hapus User | ✅ | ❌ 403 |
| Upload S3 | ✅ | ❌ |
| DB Endpoint | RDS Endpoint | RDS Endpoint (sama) |
| Server Badge | Hijau — Server 1 | Oranye — Server 2 |

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Koneksi RDS gagal | Cek inbound rule `sg-rds`: TCP 3306 dari `sg-webserver` |
| Upload S3 gagal | Jalankan `aws s3 ls s3://[bucket]/` untuk verifikasi credentials |
| Foto tidak tampil | Cek `S3_BASE_URL` di database.php dan bucket policy |
| PHP error 500 | Cek `sudo tail -f /var/log/apache2/error.log` |
| Web2 bisa create | Pastikan kamu deploy dari folder `web2/`, bukan `web1/` |
| git clone gagal | Pastikan `git` sudah terinstall: `sudo apt-get install git` |