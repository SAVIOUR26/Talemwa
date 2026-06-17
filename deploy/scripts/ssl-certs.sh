#!/usr/bin/env bash
# =============================================================================
# Talemwa SSL Certificate Setup (Let's Encrypt via Certbot)
# Run AFTER setup-vps.sh and AFTER DNS has propagated
# =============================================================================
set -euo pipefail

DOMAIN="roberttalemwa.online"
EMAIL="saviour@thirdsan.com"

echo "=== Issuing SSL certificates ==="
echo "Make sure DNS A records are live before running this."
echo ""

# Website (includes www)
certbot --apache \
  -d "$DOMAIN" \
  -d "www.$DOMAIN" \
  --non-interactive \
  --agree-tos \
  --email "$EMAIL" \
  --redirect

# API
certbot --apache \
  -d "api.$DOMAIN" \
  --non-interactive \
  --agree-tos \
  --email "$EMAIL" \
  --redirect

# Admin
certbot --apache \
  -d "admin.$DOMAIN" \
  --non-interactive \
  --agree-tos \
  --email "$EMAIL" \
  --redirect

# Radio (reverse-proxied to AzuraCast on 127.0.0.1:8080 — run AFTER setup-vps.sh
# has created the radio.$DOMAIN.conf vhost and AFTER azuracast-setup.sh)
certbot --apache \
  -d "radio.$DOMAIN" \
  --non-interactive \
  --agree-tos \
  --email "$EMAIL" \
  --redirect

echo ""
echo "=== SSL certificates issued successfully ==="
echo ""
echo "Auto-renewal is already configured by Certbot."
echo "Test renewal: certbot renew --dry-run"
