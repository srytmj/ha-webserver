#!/bin/bash
# deploy-web1.sh — Script deploy untuk EC2 Web Instance 1 (Master Node)
# Jalankan sebagai: bash deploy-web1.sh
# OS: Ubuntu 22.04 / Amazon Linux 2023

set -e  # Hentikan jika ada error

echo "========================================================"
echo "  HA Web Server — Deploy Web1 (Master Node)"
echo "========================================================"

# 1. Update system
echo "[1/7] Update package list..."
sudo apt-get update -y

# 2. Install Apache + PHP
echo "[2/7] Install Apache & PHP..."
sudo apt-get install -y apache2 php8.1 php8.1-mysql php8.1-mbstring libapache2-mod-php8.1 unzip curl

# 3. Install AWS CLI v2
echo "[3/7] Install AWS CLI v2..."
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o /tmp/awscliv2.zip
unzip -q /tmp/awscliv2.zip -d /tmp/
sudo /tmp/aws/install --update
rm -rf /tmp/awscliv2.zip /tmp/aws
aws --version

# 4. Konfigurasi AWS credentials
echo "[4/7] Konfigurasi AWS credentials..."
echo "  >>> MASUKKAN ACCESS KEY DAN SECRET KEY IAM USER (s3-webserver-user) <<<"
aws configure
# Atau jika menggunakan IAM Role (direkomendasikan):
# Tidak perlu aws configure — IAM Role di EC2 instance sudah cukup

# 5. Copy file aplikasi
echo "[5/7] Deploy file aplikasi..."
sudo mkdir -p /var/www/html
sudo cp -r public/     /var/www/html/
sudo cp -r controller/ /var/www/html/
sudo cp -r model/      /var/www/html/
sudo cp -r view/       /var/www/html/
sudo cp -r config/     /var/www/html/

# Set permission
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

# 6. Konfigurasi Apache
echo "[6/7] Konfigurasi Apache VirtualHost..."
sudo cp ha-webserver.conf /etc/apache2/sites-available/ha-webserver.conf
sudo a2ensite ha-webserver.conf
sudo a2dissite 000-default.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
sudo systemctl enable apache2

# 7. Verifikasi
echo "[7/7] Verifikasi..."
echo -n "Apache status: "
sudo systemctl is-active apache2

echo -n "Health check: "
curl -s http://localhost/health.php | python3 -m json.tool 2>/dev/null || curl -s http://localhost/health.php

echo ""
echo "========================================================"
echo "  Web1 (Master) berhasil di-deploy!"
echo "  Jangan lupa update config/database.php dengan:"
echo "  - RDS_ENDPOINT"
echo "  - DB_PASSWORD"
echo "  - S3_BUCKET name"
echo "  Region: us-east-1"
echo "========================================================"
