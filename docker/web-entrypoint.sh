#!/usr/bin/env bash
set -euo pipefail

mkdir -p /var/www/html/uploads /var/www/html/backups
chown -R www-data:www-data /var/www/html/uploads /var/www/html/backups
chmod -R 0770 /var/www/html/uploads /var/www/html/backups

exec "$@"
