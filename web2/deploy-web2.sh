#!/bin/bash
# deploy-web2.sh — Script deploy untuk EC2 Web Instance 2 (Replica / Read-Only)
# Jalankan sebagai: bash deploy-web2.sh
# OS: Ubuntu 22.04 / Amazon Linux 2023

set -e

echo "========================================================"
echo "  HA Web Server — Deploy Web2 (Replica Node)"
echo "========================================================"

echo "[1/6] Update package list..."
sudo apt-get update -y

echo "[2/6] Install Apache & PHP..."
sudo apt-get install -y apache2 php8.1 php8.1-mysql php8.1-mbstring libapache2-mod-php8.1

# AWS CLI tidak diperlukan di Web2 (tidak ada upload S3)
# Tapi install jika ingin debugging
echo "[3/6] Install AWS CLI v2 (opsional untuk debugging)..."
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o /tmp/awscliv2.zip
unzip -q /tmp/awscliv2.zip -d /tmp/
sudo /tmp/aws/install --update
rm -rf /tmp/awscliv2.zip /tmp/aws

echo "[4/6] Deploy file aplikasi..."
sudo mkdir -p /var/www/html
sudo cp -r public/     /var/www/html/
sudo cp -r controller/ /var/www/html/
sudo cp -r model/      /var/www/html/
sudo cp -r view/       /var/www/html/
sudo cp -r config/     /var/www/html/

sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

echo "[5/6] Konfigurasi Apache..."
sudo cp ha-webserver.conf /etc/apache2/sites-available/ha-webserver.conf
sudo a2ensite ha-webserver.conf
sudo a2dissite 000-default.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
sudo systemctl enable apache2

echo "[6/6] Verifikasi..."
echo -n "Apache status: "
sudo systemctl is-active apache2

echo -n "Health check: "
curl -s http://localhost/health.php | python3 -m json.tool 2>/dev/null || curl -s http://localhost/health.php

echo ""
echo "========================================================"
echo "  Web2 (Replica) berhasil di-deploy!"
echo "  Jangan lupa update config/database.php dengan:"
echo "  - RDS_ENDPOINT (sama dengan Web1)"
echo "  - DB_PASSWORD"
echo "  - S3_BUCKET name"
echo "  Region: us-east-1"
echo "========================================================"
