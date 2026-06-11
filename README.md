# HA Web Server — AWS Deployment Package

## Isi Package

```
ha-webserver/
├── web1/                    # Deploy ke EC2 Instance 1 (Master R/W)
│   ├── public/
│   │   ├── index.php        # Front controller
│   │   ├── health.php       # ALB health check endpoint
│   │   └── .htaccess
│   ├── controller/
│   │   ├── loginController.php
│   │   ├── mainController.php
│   │   └── userController.php  ← EKSKLUSIF Web1 (Create/Update/Delete)
│   ├── model/
│   │   └── User.php
│   ├── view/
│   │   ├── login.php
│   │   ├── main.php
│   │   ├── read.php
│   │   ├── create.php       ← EKSKLUSIF Web1
│   │   └── update.php       ← EKSKLUSIF Web1
│   ├── config/
│   │   └── database.php     ← WAJIB DIEDIT (Aurora Writer Endpoint)
│   └── apache-vhost.conf
│
├── web2/                    # Deploy ke EC2 Instance 2 (Replica R/O)
│   ├── public/
│   │   ├── index.php
│   │   ├── health.php
│   │   └── .htaccess
│   ├── controller/
│   │   ├── loginController.php
│   │   └── mainController.php
│   ├── model/
│   │   └── User.php
│   ├── view/
│   │   ├── login.php
│   │   ├── main.php
│   │   └── read.php
│   ├── config/
│   │   └── database.php     ← WAJIB DIEDIT (Aurora Reader Endpoint)
│   └── apache-vhost.conf
│
├── database/
│   └── init.sql             # Jalankan di Aurora Master sebelum deploy
│
└── scripts/
    ├── install-web1.sh      # Setup otomatis Web Instance 1
    └── install-web2.sh      # Setup otomatis Web Instance 2
```

---

## Langkah Deploy

### Step 1 — Inisialisasi Database Aurora
```bash
mysql -h [AURORA_WRITER_ENDPOINT] -u admin -p < database/init.sql
```

### Step 2 — Edit Config Web1
File: `web1/config/database.php`
- `DB_HOST`      → Aurora **Writer** Endpoint
- `DB_PASS`      → Password Aurora
- `S3_BUCKET`    → Nama bucket S3 kamu
- `S3_BASE_URL`  → URL publik bucket S3

### Step 3 — Edit Config Web2
File: `web2/config/database.php`
- `DB_HOST`      → Aurora **Reader** Endpoint (cluster-ro-...)
- Sisanya sama dengan Web1

### Step 4 — Upload & Install ke EC2
```bash
# Upload ke EC2 Web1
scp -r -i your-key.pem web1/ ubuntu@[IP-WEB1]:/tmp/ha-webserver/web1/
ssh -i your-key.pem ubuntu@[IP-WEB1] "sudo bash /tmp/ha-webserver/scripts/install-web1.sh"

# Upload ke EC2 Web2
scp -r -i your-key.pem web2/ ubuntu@[IP-WEB2]:/tmp/ha-webserver/web2/
ssh -i your-key.pem ubuntu@[IP-WEB2] "sudo bash /tmp/ha-webserver/scripts/install-web2.sh"
```

### Step 5 — Konfigurasi AWS CLI di masing-masing EC2
```bash
aws configure
# AWS Access Key ID: [dari IAM User s3-webserver-user]
# AWS Secret Access Key: [dari IAM User s3-webserver-user]
# Default region: ap-southeast-1
# Default output: json
```

### Step 6 — Test Health Check
```bash
curl http://[IP-WEB1]/health.php
curl http://[IP-WEB2]/health.php
# Expected: {"status":"healthy","server_id":"1",...}
```

---

## Akses Aplikasi
- **URL**: http://[ALB-DNS-NAME]/index.php?action=login
- **Login**: admin / password123 *(ganti di loginController.php untuk produksi)*

## Perbedaan Web1 vs Web2
| Fitur            | Web1 (Master) | Web2 (Replica) |
|-----------------|--------------|----------------|
| Login           | ✅            | ✅              |
| Lihat Data      | ✅            | ✅              |
| Tambah User     | ✅            | ❌ (403)        |
| Edit User       | ✅            | ❌ (403)        |
| Hapus User      | ✅            | ❌ (403)        |
| Upload S3       | ✅            | ❌              |
| DB Connection   | Writer EP     | Reader EP      |
