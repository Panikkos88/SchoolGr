#!/usr/bin/env bash
set -euo pipefail

sudo rm -f /var/www/schoolmedia/test.php

echo "SERVICES"
systemctl is-active apache2 mariadb

echo "DATABASE_TABLES"
sudo mariadb -Nse \
  "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='schoolmedia'"

echo "PHP_SYNTAX_FAILURES"
sudo find /var/www/schoolmedia -name '*.php' -exec php -l {} \; \
  >/tmp/schoolmedia-php-lint.log 2>&1
grep -c -v '^No syntax errors detected' /tmp/schoolmedia-php-lint.log || true

echo "APACHE_ERRORS"
sudo tail -n 20 /var/log/apache2/schoolmedia-error.log
