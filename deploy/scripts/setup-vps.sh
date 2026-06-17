#!/usr/bin/env bash
# =============================================================================
# Talemwa VPS Setup Script
# Run as root on a fresh Ubuntu 22.04 RackNerd VPS
# Usage: bash setup-vps.sh
# =============================================================================
set -euo pipefail

VPS_IP=""            # Fill this in before running
DOMAIN="roberttalemwa.online"
EMAIL="saviour@thirdsan.com"
WEB_ROOT="/var/www/talemwa"

echo "=== Talemwa VPS Setup ==="
echo "Domain : $DOMAIN"
echo "Root   : $WEB_ROOT"
echo ""

# ── 1. System update ──────────────────────────────────────────────────────────
echo ">>> Updating system..."
apt update && apt upgrade -y

# ── 2. Install Apache + PHP + tools ──────────────────────────────────────────
echo ">>> Installing Apache, PHP 8.2, Certbot..."
apt install -y \
  apache2 \
  php8.2 php8.2-sqlite3 php8.2-curl php8.2-mbstring php8.2-json \
  certbot python3-certbot-apache \
  unzip git curl ufw

# ── 3. Enable Apache modules ──────────────────────────────────────────────────
echo ">>> Enabling Apache modules..."
a2enmod rewrite ssl headers proxy proxy_http proxy_wstunnel
systemctl enable apache2
systemctl restart apache2

# ── 4. Firewall ───────────────────────────────────────────────────────────────
echo ">>> Configuring UFW firewall..."
ufw allow OpenSSH
ufw allow 'Apache Full'
ufw --force enable

# ── 5. Directory structure ────────────────────────────────────────────────────
echo ">>> Creating directory structure..."
mkdir -p "$WEB_ROOT/backend/database"
mkdir -p "$WEB_ROOT/backend/uploads/sermons"
mkdir -p "$WEB_ROOT/website"
mkdir -p "$WEB_ROOT/dashboard"
chown -R www-data:www-data "$WEB_ROOT"
chmod -R 755 "$WEB_ROOT"
chmod -R 775 "$WEB_ROOT/backend/database"
chmod -R 775 "$WEB_ROOT/backend/uploads"

# ── 6. Apache vhosts ──────────────────────────────────────────────────────────
echo ">>> Configuring Apache virtual hosts..."

# Website — roberttalemwa.online
cat > /etc/apache2/sites-available/$DOMAIN.conf << EOF
<VirtualHost *:80>
    ServerName  $DOMAIN
    ServerAlias www.$DOMAIN
    DocumentRoot $WEB_ROOT/website
    <Directory $WEB_ROOT/website>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog  \${APACHE_LOG_DIR}/website-error.log
    CustomLog \${APACHE_LOG_DIR}/website-access.log combined
</VirtualHost>
EOF

# API — api.roberttalemwa.online
cat > /etc/apache2/sites-available/api.$DOMAIN.conf << EOF
<VirtualHost *:80>
    ServerName api.$DOMAIN
    DocumentRoot $WEB_ROOT/backend
    <Directory $WEB_ROOT/backend>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=\$1
    Header always set Access-Control-Allow-Origin  "*"
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization"
    RewriteEngine On
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule .* - [R=200,L]
    ErrorLog  \${APACHE_LOG_DIR}/api-error.log
    CustomLog \${APACHE_LOG_DIR}/api-access.log combined
</VirtualHost>
EOF

# Admin — admin.roberttalemwa.online
cat > /etc/apache2/sites-available/admin.$DOMAIN.conf << EOF
<VirtualHost *:80>
    ServerName admin.$DOMAIN
    DocumentRoot $WEB_ROOT/dashboard
    <Directory $WEB_ROOT/dashboard>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    <DirectoryMatch "$WEB_ROOT/dashboard/partials">
        Require all denied
    </DirectoryMatch>
    ErrorLog  \${APACHE_LOG_DIR}/admin-error.log
    CustomLog \${APACHE_LOG_DIR}/admin-access.log combined
</VirtualHost>
EOF

# Radio — radio.roberttalemwa.online (reverse proxy to AzuraCast on :8080)
cat > /etc/apache2/sites-available/radio.$DOMAIN.conf << EOF
<VirtualHost *:80>
    ServerName radio.$DOMAIN
    ProxyPreserveHost On
    ProxyRequests Off
    RewriteEngine On
    RewriteCond %{HTTP:Upgrade} websocket [NC]
    RewriteCond %{HTTP:Connection} upgrade [NC]
    RewriteRule ^/(.*)\$ ws://127.0.0.1:8080/\$1 [P,L]
    ProxyPass        / http://127.0.0.1:8080/
    ProxyPassReverse / http://127.0.0.1:8080/
    ErrorLog  \${APACHE_LOG_DIR}/radio-error.log
    CustomLog \${APACHE_LOG_DIR}/radio-access.log combined
</VirtualHost>
EOF

# Disable default site, enable ours
a2dissite 000-default.conf
a2ensite $DOMAIN.conf api.$DOMAIN.conf admin.$DOMAIN.conf radio.$DOMAIN.conf
systemctl reload apache2

echo ""
echo "=== Apache configured. Now run SSL setup: ==="
echo "  bash ssl-certs.sh"
echo ""
echo "=== Then upload files to $WEB_ROOT ==="
echo "  scp -r backend/  root@$VPS_IP:$WEB_ROOT/"
echo "  scp -r website/  root@$VPS_IP:$WEB_ROOT/"
echo "  scp -r dashboard/ root@$VPS_IP:$WEB_ROOT/"
echo ""
echo "=== Then create .env: ==="
echo "  cp $WEB_ROOT/backend/.env.example $WEB_ROOT/backend/.env"
echo "  nano $WEB_ROOT/backend/.env"
