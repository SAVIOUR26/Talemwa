#!/usr/bin/env bash
# =============================================================================
# Talemwa Deploy Script — upload latest code from local machine to VPS
# Usage: bash deploy.sh <VPS_IP>
# Example: bash deploy.sh 192.168.1.100
# =============================================================================
set -euo pipefail

VPS_IP="${1:?Usage: $0 <VPS_IP>}"
VPS_USER="root"
WEB_ROOT="/var/www/talemwa"
REPO_ROOT="$(cd "$(dirname "$0")/../.." && pwd)"

echo "=== Deploying Talemwa to $VPS_IP ==="
echo "Source: $REPO_ROOT"
echo ""

# Backend API
echo ">>> Uploading backend..."
rsync -avz --exclude='.env' --exclude='database/' --exclude='uploads/' \
  "$REPO_ROOT/backend/" \
  "$VPS_USER@$VPS_IP:$WEB_ROOT/backend/"

# Website
echo ">>> Uploading website..."
rsync -avz \
  "$REPO_ROOT/website/" \
  "$VPS_USER@$VPS_IP:$WEB_ROOT/website/"

# Admin dashboard
echo ">>> Uploading dashboard..."
rsync -avz \
  "$REPO_ROOT/dashboard/" \
  "$VPS_USER@$VPS_IP:$WEB_ROOT/dashboard/"

# Fix permissions after upload
echo ">>> Fixing permissions..."
ssh "$VPS_USER@$VPS_IP" bash << 'REMOTE'
  chown -R www-data:www-data /var/www/talemwa
  chmod -R 755 /var/www/talemwa
  chmod -R 775 /var/www/talemwa/backend/database
  chmod -R 775 /var/www/talemwa/backend/uploads
  systemctl reload apache2
REMOTE

echo ""
echo "=== Deploy complete ==="
echo "API:   https://api.roberttalemwa.online/api/live"
echo "Site:  https://roberttalemwa.online"
echo "Admin: https://admin.roberttalemwa.online"
