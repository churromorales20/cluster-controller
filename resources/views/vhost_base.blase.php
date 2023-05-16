server {
    listen 80;
    server_name {DOMAIN_NAME};

    location / {
        proxy_pass http://127.0.0.1:{TENANT_PORT};
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }

    location /app/ {
        proxy_pass http://127.0.0.1:2403/app/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
    }

    location /apps/ {
        proxy_pass http://127.0.0.1:2403/apps/;
        proxy_set_header Host $host;
    }
 
    location /api/ {
        proxy_pass http://localhost:1118;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
    }

}