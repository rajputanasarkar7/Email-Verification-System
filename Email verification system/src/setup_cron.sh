#!/bin/bash

# Absolute path to this script
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# PHP path
PHP_PATH=$(which php)

# cron.php full path
CRON_FILE="$DIR/cron.php"

# Add cron job to crontab (every 5 minutes)
(crontab -l 2>/dev/null; echo "*/5 * * * * $PHP_PATH $CRON_FILE") | crontab -

echo " CRON job added to run cron.php every 5 minutes."
