# Talemwa Platform — Deployment Guide
## roberttalemwa.online

---

## Domain Setup (Namecheap)

### Domains to register
1. `roberttalemwa.online` — primary (register this first)
2. `roberttalemwa.com` — brand protection (optional but recommended)

### DNS Records (point all to your RackNerd VPS IP)
```
Type    Host        Value               TTL
A       @           YOUR.VPS.IP.HERE    300
A       www         YOUR.VPS.IP.HERE    300
A       api         YOUR.VPS.IP.HERE    300
A       admin       YOUR.VPS.IP.HERE    300
A       radio       YOUR.VPS.IP.HERE    300
```

---

## VPS Setup (RackNerd — Ubuntu 22.04)

### 1. Install required software
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y apache2 php8.2 php8.2-sqlite3 php8.2-curl \
  php8.2-mbstring php8.2-json certbot python3-certbot-apache unzip git
```

### 2. Enable Apache modules
```bash
sudo a2enmod rewrite ssl headers
sudo systemctl restart apache2
```

### 3. Create virtual hosts
```bash
# API
sudo nano /etc/apache2/sites-available/api.roberttalemwa.online.conf

# Paste:
<VirtualHost *:80>
    ServerName api.roberttalemwa.online
    DocumentRoot /var/www/talemwa/backend
    <Directory /var/www/talemwa/backend>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Repeat for:
- `roberttalemwa.online` → `/var/www/talemwa/website`
- `admin.roberttalemwa.online` → `/var/www/talemwa/dashboard`

```bash
sudo a2ensite api.roberttalemwa.online.conf
sudo a2ensite roberttalemwa.online.conf
sudo a2ensite admin.roberttalemwa.online.conf
sudo systemctl reload apache2
```

### 4. SSL certificates (free via Let's Encrypt)
```bash
sudo certbot --apache -d roberttalemwa.online -d www.roberttalemwa.online
sudo certbot --apache -d api.roberttalemwa.online
sudo certbot --apache -d admin.roberttalemwa.online
# AzuraCast handles its own SSL for radio.roberttalemwa.online
```

### 5. Deploy files
```bash
sudo mkdir -p /var/www/talemwa
sudo chown -R www-data:www-data /var/www/talemwa

# Upload via FTP or SCP:
scp -r backend/ user@YOUR.VPS.IP:/var/www/talemwa/
scp -r dashboard/ user@YOUR.VPS.IP:/var/www/talemwa/
scp -r website/ user@YOUR.VPS.IP:/var/www/talemwa/
```

### 6. Set environment variables
```bash
sudo nano /var/www/talemwa/backend/.env
# Paste and fill all values from .env.example
```

### 7. Set permissions
```bash
sudo mkdir -p /var/www/talemwa/backend/database
sudo mkdir -p /var/www/talemwa/backend/uploads/sermons
sudo chown -R www-data:www-data /var/www/talemwa/backend/database
sudo chown -R www-data:www-data /var/www/talemwa/backend/uploads
sudo chmod 755 /var/www/talemwa/backend/uploads/sermons
```

### 8. Test the API
```bash
curl https://api.roberttalemwa.online/api/live
# Expected: {"status":"success","data":{"is_live":false,...}}
```

---

## AzuraCast Setup (Online Radio)

### Install AzuraCast (Docker-based)
```bash
# AzuraCast needs Docker
sudo apt install -y docker.io docker-compose
sudo systemctl enable docker

# Install AzuraCast
mkdir -p /var/azuracast
cd /var/azuracast
curl -fsSL https://raw.githubusercontent.com/AzuraCast/AzuraCast/stable/docker.sh > docker.sh
chmod a+x docker.sh
sudo ./docker.sh install
```

### Configure subdomain
```bash
# During AzuraCast install, set domain to: radio.roberttalemwa.online
# AzuraCast will auto-provision SSL via Let's Encrypt
```

### First-time AzuraCast setup
1. Visit https://radio.roberttalemwa.online
2. Create admin account
3. Create station: "Talemwa Radio"
4. Set stream URL path: `/stream`
5. Upload intro/hold music (worship songs)
6. Create weekly schedule
7. Copy API key → paste into backend `.env` as `AZURACAST_API_KEY`
8. Copy stream URL → `https://radio.roberttalemwa.online/stream`

---

## Firebase Setup (Push Notifications)

1. Go to https://console.firebase.google.com
2. Create project: "Talemwa"
3. Add Android app: `com.thirdsan.talemwa`
4. Add iOS app: `com.thirdsan.talemwa`
5. Download `google-services.json` → place in `flutter-app/android/app/`
6. Download `GoogleService-Info.plist` → place in `flutter-app/ios/Runner/`
7. Project Settings → Cloud Messaging → copy Server Key → paste into `.env` as `FCM_SERVER_KEY`

---

## Flutter App Build & Deploy

### Android (Google Play)
```bash
cd flutter-app
flutter pub get
flutter build apk --release
# APK at: build/app/outputs/flutter-apk/app-release.apk

# For Play Store:
flutter build appbundle --release
# Bundle at: build/app/outputs/bundle/release/app-release.aab
```

### iOS (App Store)
```bash
flutter build ios --release
# Then open ios/ in Xcode → Archive → Upload to App Store Connect
```

### App Store setup
- **Google Play Console**: https://play.google.com/console
  - App name: Talemwa
  - Package: com.thirdsan.talemwa
  - Category: Lifestyle / Religion & Spirituality
  - One-time fee: $25

- **Apple App Store Connect**: https://appstoreconnect.apple.com
  - App name: Talemwa
  - Bundle ID: com.thirdsan.talemwa
  - Category: Lifestyle / Religion & Spirituality
  - Annual fee: $99/yr

---

## Post-Deployment Checklist

- [ ] All DNS records pointing to VPS
- [ ] SSL active on all 4 subdomains
- [ ] API returns valid JSON at api.roberttalemwa.online/api/live
- [ ] Admin dashboard login works at admin.roberttalemwa.online
- [ ] Default admin password changed from `TalemwaAdmin2024!`
- [ ] AzuraCast station streaming at radio.roberttalemwa.online/stream
- [ ] FCM test notification sent and received on test device
- [ ] Flutterwave test payment completed successfully
- [ ] Flutter app connects to API in debug mode
- [ ] Flutter app release build generated
- [ ] App submitted to Google Play
- [ ] App submitted to Apple App Store

---

## Support Contacts

- **Developer**: Saviour — Thirdsan Enterprises Ltd, Kampala
- **Client**: Pastor Robert Talemwa
- **Domain registrar**: Namecheap
- **Hosting**: RackNerd VPS
- **Radio**: AzuraCast (open source)
- **Payments**: Flutterwave (Africa) + PayPal (diaspora)
