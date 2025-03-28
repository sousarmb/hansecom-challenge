#!/bin/bash

set -e

cd /var/www/html
# Install necessary packages
composer install --no-interaction
# Start queue consumption
sleep 10
bin/console messenger:setup-transports --no-interaction
bin/console messenger:consume

sleep 365d