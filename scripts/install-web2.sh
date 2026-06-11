#!/bin/bash
# install-web2.sh — Setup EC2 Web Instance 2 (Replica Node)
# sudo bash install-web2.sh
set -e
apt-get update && apt-get upgrade -y
apt-get install -y apache2 php8.1 php8.1-mysql php8.1-curl php8.1-mbstring php8.1-xml unzip curl
a2enmod rewrite && systemctl restart apache2
curl -s "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "/tmp/awscliv2.zip"
unzip -q /tmp/awscliv2.zip -d /tmp && /tmp/aws/install && rm -rf /tmp/awscliv2.zip /tmp/aws
cp -r /tmp/ha-webserver/web2/* /var/www/html/
chown -R www-data:www-data /var/www/html/
cp /var/www/html/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
systemctl restart apache2
echo "DONE. Edit /var/www/html/config/database.php lalu jalankan: aws configure"
