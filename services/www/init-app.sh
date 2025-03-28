#!/bin/bash

set -e

cd /var/www/html
# Install necessary packages
composer install --no-interaction
# Update database
php bin/console doctrine:migrations:migrate --no-interaction
# Start Apache in foreground mode
apache2-foreground