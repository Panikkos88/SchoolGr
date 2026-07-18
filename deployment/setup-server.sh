#!/usr/bin/env bash
set -euo pipefail

if [[ -z "${DB_PASSWORD:-}" ]]; then
  echo "DB_PASSWORD is required" >&2
  exit 1
fi

export DEBIAN_FRONTEND=noninteractive
sudo apt-get update
sudo apt-get install -y \
  apache2 \
  mariadb-server \
  libapache2-mod-php \
  php \
  php-curl \
  php-mbstring \
  php-mysql \
  php-xml \
  php-zip

sudo rm -rf /var/www/schoolmedia
sudo mkdir -p /var/www/schoolmedia
sudo tar -xzf "$HOME/schoolmedia-deploy.tar.gz" \
  --strip-components=1 \
  -C /var/www/schoolmedia

sudo chown -R root:www-data /var/www/schoolmedia
sudo find /var/www/schoolmedia -type d -exec chmod 0750 {} +
sudo find /var/www/schoolmedia -type f -exec chmod 0640 {} +
sudo chmod -R 0770 \
  /var/www/schoolmedia/uploads \
  /var/www/schoolmedia/backups

sudo mariadb <<SQL
CREATE DATABASE IF NOT EXISTS schoolmedia
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'schoolmedia'@'localhost'
  IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER 'schoolmedia'@'localhost'
  IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON schoolmedia.* TO 'schoolmedia'@'localhost';
FLUSH PRIVILEGES;
SQL

sudo mariadb schoolmedia < "$HOME/telikidokimi.sql"

sudo install -d -m 0750 -o root -g www-data /etc/schoolmedia
sudo tee /etc/schoolmedia/apache-env.conf >/dev/null <<CONF
SetEnv SCHOOLMEDIA_DB_HOST 127.0.0.1
SetEnv SCHOOLMEDIA_DB_NAME schoolmedia
SetEnv SCHOOLMEDIA_DB_USER schoolmedia
SetEnv SCHOOLMEDIA_DB_PASSWORD ${DB_PASSWORD}
CONF
sudo chmod 0640 /etc/schoolmedia/apache-env.conf

sudo tee /etc/apache2/sites-available/schoolmedia.conf >/dev/null <<'CONF'
<VirtualHost *:80>
    ServerName schoolmedia.local
    DocumentRoot /var/www/schoolmedia

    Include /etc/schoolmedia/apache-env.conf

    <Directory /var/www/schoolmedia>
        Options -Indexes
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /var/www/schoolmedia/uploads>
        Options -Indexes
        AllowOverride All
        php_admin_flag engine Off
    </Directory>

    <Directory /var/www/schoolmedia/config>
        Require all denied
    </Directory>

    <Directory /var/www/schoolmedia/install>
        Require all denied
    </Directory>

    <Directory /var/www/schoolmedia/backups>
        Require all denied
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/schoolmedia-error.log
    CustomLog ${APACHE_LOG_DIR}/schoolmedia-access.log combined
</VirtualHost>
CONF

sudo a2enmod headers rewrite
sudo a2dissite 000-default
sudo a2ensite schoolmedia
sudo apache2ctl configtest
sudo systemctl enable --now mariadb apache2
sudo systemctl restart apache2

rm -f "$HOME/telikidokimi.sql" "$HOME/schoolmedia-deploy.tar.gz"

curl --fail --silent --show-error \
  --header 'Host: schoolmedia.local' \
  http://127.0.0.1/login.php >/dev/null

echo "DEPLOYMENT_OK"
