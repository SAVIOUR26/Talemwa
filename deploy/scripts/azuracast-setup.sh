#!/usr/bin/env bash
# =============================================================================
# AzuraCast Installation Script
# Run on the SAME VPS — AzuraCast runs on port 8080, proxied via radio subdomain
# Docs: https://www.azuracast.com/docs/getting-started/installation/docker/
# =============================================================================
set -euo pipefail

INSTALL_DIR="/var/azuracast"

echo "=== Installing AzuraCast (Docker) ==="
echo "This installs on port 8080 — radio.roberttalemwa.online will proxy to it."
echo ""

# Install Docker if not already present
if ! command -v docker &>/dev/null; then
  echo ">>> Installing Docker..."
  curl -fsSL https://get.docker.com | sh
  systemctl enable docker
  systemctl start docker
fi

# Create install directory
mkdir -p "$INSTALL_DIR"
cd "$INSTALL_DIR"

# Download AzuraCast Docker installer
echo ">>> Downloading AzuraCast..."
curl -fsSL https://raw.githubusercontent.com/AzuraCast/AzuraCast/main/docker.sh \
  -o docker.sh
chmod +x docker.sh

# Run installation
# Apache already owns the public 80/443 for the other 3 domains on this VPS,
# and radio.roberttalemwa.online is reverse-proxied to AzuraCast via Apache
# (see deploy/apache/radio.roberttalemwa.online.conf). So AzuraCast must NOT
# bind 80/443 itself and must NOT run its own Let's Encrypt for this domain —
# Apache/Certbot handles TLS for radio.roberttalemwa.online instead.
#
# It will prompt for:
#   - Application Environment (production)
#   - Customize ports (yes — set HTTP to 127.0.0.1:8080, disable HTTPS/SFTP
#     external exposure, or leave HTTPS unset since Apache terminates TLS)
#   - Enable Let's Encrypt (NO — Apache/Certbot handles this domain's SSL)
echo ""
echo ">>> Starting AzuraCast installer..."
echo "    When prompted:"
echo "    - Environment: production"
echo "    - Customize ports: YES"
echo "      - HTTP port: 127.0.0.1:8080 (bind to localhost only)"
echo "      - HTTPS port: leave disabled — Apache terminates TLS"
echo "    - Enable Let's Encrypt: NO (Apache/Certbot handles radio.roberttalemwa.online)"
echo ""
./docker.sh install

echo ""
echo "=== AzuraCast installed ==="
echo ""
echo "Next steps:"
echo "1. Run setup-vps.sh (if not already) to enable proxy modules + radio vhost"
echo "2. Run ssl-certs.sh to issue the radio.roberttalemwa.online cert via Apache"
echo "3. Visit https://radio.roberttalemwa.online"
echo "4. Create admin account"
echo "5. Create station: 'Talemwa Radio'"
echo "6. Set stream path: /stream"
echo "7. Upload intro/hold music"
echo "8. Admin → API Keys → Generate key"
echo "9. Add key to /var/www/talemwa/backend/.env as AZURACAST_API_KEY"
