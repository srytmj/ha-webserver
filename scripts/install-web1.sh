#!/bin/bash
# install-web1.sh — Setup EC2 Web Instance 1 (Master Node)
# Jalankan sebagai: sudo bash install-web1.sh
set -e

apt-get update && apt-get upgrade -y
apt-get install -y apache2 php libapache2-mod-php php-mysql \
    php-curl php-mbstring php-xml php-zip unzip curl git

a2enmod php8.5
a2enmod rewrite
systemctl restart apache2

curl -s "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "/tmp/awscliv2.zip"
unzip -q /tmp/awscliv2.zip -d /tmp && /tmp/aws/install && rm -rf /tmp/awscliv2.zip /tmp/aws

echo "DONE. Langkah selanjutnya:"
echo "  1. git clone https://github.com/srytmj/ha-webserver /var/www/html/ha-webserver"
echo "  2. Edit /var/www/html/ha-webserver/web1/config/database.php"
echo "  3. Konfigurasi Apache VirtualHost (lihat web1/ha-webserver.conf)"
echo "  4. aws configure (atau attach IAM Role EC2-S3-Role)"
