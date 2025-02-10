#!/bin/sh

# Check if the Laravel project is already present in /var/www
if [ ! -f /var/www/artisan ]; then
  echo "Laravel project not found in /var/www. Installing..."
  composer create-project laravel/laravel /tmp/my-project
  cd /tmp/my-project && composer require laravel/octane --no-install && npm install chokidar --save-dev --no-install
  rsync -av /tmp/my-project/ /var/www/; \
  rm -rf /tmp/my-project; \
  chown -R laravel:laravel /var/www
fi

exec "$@"
