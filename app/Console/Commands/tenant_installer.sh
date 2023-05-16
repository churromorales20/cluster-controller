#!/bin/bash

# Default values
WITH_WWW=false

# Parse command-line options
while getopts ":t:s:d:w" opt; do
  case $opt in
    t) TENANT_ID=$OPTARG ;;
    s) SHARED_FOLDER=$OPTARG ;;
    d) APP_DOMAIN=$OPTARG ;;
    p) DB_PASSWORD=$OPTARG ;;
    n) DB_NAME=$OPTARG ;;
    a) APP_PORT=$OPTARG ;;
    w) WITH_WWW=true ;;
    \?) echo "Invalid option: -$OPTARG" >&2; exit 1 ;;
  esac
done

# Check required options
if [[ -z $TENANT_ID || -z $SHARED_FOLDER || -z $APP_DOMAIN ]]; then
  echo "Missing required options. Usage: myscript.sh -t <tenant_id> -s <shared_folder> -d <app_domain> [-w]"
  exit 1
fi

# Create symlink in nginx
sudo ln -s /etc/nginx/sites-available/$APP_DOMAIN.conf /etc/nginx/sites-enabled/
sudo systemctl reload nginx

# SSL
if $WITH_WWW; then
  sudo certbot --nginx -d www.$APP_DOMAIN -d $APP_DOMAIN --email cecilio.dev@gmail.com
else
  sudo certbot --nginx -d $APP_DOMAIN --email cecilio.dev@gmail.com
fi

# Create directories
mkdir $SHARED_FOLDER/tenant_$TENANT_ID
mkdir $SHARED_FOLDER/tenant_$TENANT_ID/css/
mkdir $SHARED_FOLDER/tenant_$TENANT_ID/icons/
mkdir $SHARED_FOLDER/tenant_$TENANT_ID/images/
mkdir $SHARED_FOLDER/tenant_$TENANT_ID/products/images/

#Create DB
docker exec UchipDatabase-container mysql -uroot -p$DB_PASSWORD -e "CREATE DATABASE $DB_NAME;"

#Migrate DB
docker exec UchipRestApi-container php artisan tenants:artisan "migrate --database=tenant" --tenant=$TENANT_ID

#Seed DB
docker exec UchipRestApi-container php artisan uchip-customer:data --json=/home/shared/config-$TENANT_ID.json

#SELINUX
semanage port -a -t http_port_t -p tcp $APP_PORT

START_CONTAINER
docker run -d --name NAMESITE -e UCHIP_CUSTOMER_ID=1 -e WS_KEY=ARTIWS2023 -e WS_URL=www.artisanburguers.pe -e API_URL=https://www.artisanburguers.pe/api/ -e NODE_ENV=production -e PORT=$APP_PORT -e TLS=true -v "/home/shared/data/tenant_$TENANT_ID:/app/uchip_assets:z" -p $APP_PORT:$APP_PORT uchip-website-image:latest