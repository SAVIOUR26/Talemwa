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
# It will prompt for:
#   - Application Environment (production)
#   - Customize ports (yes — use default 80/443 or 8080/8443 if Apache is on 80)
#   - Enable Let's Encrypt (yes — enter radio.roberttalemwa.online)
echo ""
echo ">>> Starting AzuraCast installer..."
echo "    When prompted:"
echo "    - Environment: production"
echo "    - External ports: 8080 (HTTP) and 8443 (HTTPS)"
echo "    - Let's Encrypt domain: radio.roberttalemwa.online"
echo "    - Let's Encrypt email:  saviour@thirdsan.com"
echo ""
./docker.sh install

echo ""
echo "=== AzuraCast installed ==="
echo ""
echo "Next steps:"
echo "1. Visit https://radio.roberttalemwa.online"
echo "2. Create admin account"
echo "3. Create station: 'Miracles Now Radio'"
echo "4. Set stream path: /listen/miracles_now_radio/radio.mp3"
echo "5. Upload intro/hold music"
echo "6. Admin → API Keys → Generate key"
echo "7. Add key to /var/www/talemwa/backend/.env as AZURACAST_API_KEY"
