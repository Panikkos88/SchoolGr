#!/usr/bin/env bash
set -euo pipefail

hostname='schoolmedia.34-116-171-152.sslip.io'
site_conf='/etc/apache2/sites-available/schoolmedia.conf'

export DEBIAN_FRONTEND=noninteractive
sudo apt-get update
sudo apt-get install -y certbot python3-certbot-apache

sudo sed -i \
  "s/^    ServerName .*/    ServerName ${hostname}/" \
  "$site_conf"

sudo apache2ctl configtest
sudo systemctl reload apache2

sudo certbot --apache \
  --non-interactive \
  --agree-tos \
  --register-unsafely-without-email \
  --redirect \
  --domain "$hostname"

ssl_conf='/etc/apache2/sites-available/schoolmedia-le-ssl.conf'
if ! sudo grep -q 'Strict-Transport-Security' "$ssl_conf"; then
  sudo sed -i \
    '/SSLCertificateKeyFile/a\\        Header always set Strict-Transport-Security "max-age=31536000"' \
    "$ssl_conf"
fi

sudo apache2ctl configtest
sudo systemctl reload apache2
sudo certbot renew --dry-run

curl --fail --silent --show-error \
  "https://${hostname}/login.php" >/dev/null

echo "SSL_OK"
