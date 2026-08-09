#!/bin/sh
set -e

echo "Running migrations and seeding..."
php artisan migrate:fresh --seed --force

echo "Starting HTTP server on 0.0.0.0:${PORT:-8000}"
exec php -S 0.0.0.0:${PORT:-8000} -t public
