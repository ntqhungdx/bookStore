#!/bin/sh

echo "================"
echo "Build docker....."
echo "================"

[ ! -d "../api/" ] && echo "Directory api DOES NOT exists." &&  exit 0

# Build images and launch containers
rm -f docker-compose.yml .env
cp .env.local .env
cp docker-compose.local.yml docker-compose.yml

if ! command docker compose up -d; then
    echo "================"
    echo "Build failed"
    echo "================"
    exit
fi

echo "================"
echo "Build application server...."
echo "================"

# Create env file
cp ../api/.env.example ../api/.env

# Change owner and chmod all necessary files
sudo docker compose exec api find . -type f -exec chmod 664 {} \;
sudo docker compose exec api find . -type d -exec chmod 775 {} \;
sudo docker compose exec api chgrp -R www-data storage bootstrap/cache
sudo docker compose exec api chmod -R ug+rwx storage bootstrap/cache

# Install PHP packages
sudo docker compose exec api composer install
sudo docker compose exec api php artisan key:generate
sudo docker compose exec api php artisan storage:link
sleep 5s

# Migrate DB and seeder
sudo docker compose exec api php artisan migrate
sudo docker compose exec api php artisan db:seed

echo "================"
echo "Build done"
echo "================"