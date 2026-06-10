# Talemwa Platform — Deployment Guide
**Built by Thirdsan Enterprises Ltd · Kampala, Uganda**

---

## Prerequisites
- RackNerd VPS with Ubuntu 22.04
- Domain `roberttalemwa.online` registered on Namecheap
- Firebase project created
- Flutterwave account (for payments)
- PayPal developer account (for diaspora giving)

---

## Step 1 — DNS Records (Namecheap)

Login to Namecheap → Domain List → Manage → Advanced DNS

| Type | Host  | Value              | TTL |
|------|-------|--------------------|-----|
| A    | @     | `YOUR.VPS.IP.HERE` | 300 |
| A    | www   | `YOUR.VPS.IP.HERE` | 300 |
| A    | api   | `YOUR.VPS.IP.HERE` | 300 |
| A    | admin | `YOUR.VPS.IP.HERE` | 300 |
| A    | radio | `YOUR.VPS.IP.HERE` | 300 |

> Wait 5–30 minutes for DNS to propagate before running SSL setup.
> Verify: `dig api.roberttalemwa.online` should return your VPS IP.

---

## Step 2 — VPS Setup

SSH into your VPS as root, then:

```bash
# Upload setup script
scp deploy/scripts/setup-vps.sh root@YOUR.VPS.IP:/root/
scp deploy/scripts/ssl-certs.sh root@YOUR.VPS.IP:/root/

# Run VPS setup (installs Apache, PHP 8.2, Certbot, UFW)
ssh root@YOUR.VPS.IP 'bash /root/setup-vps.sh'

# After DNS has propagated, run SSL
ssh root@YOUR.VPS.IP 'bash /root/ssl-certs.sh'
```

Alternatively run commands manually — see `deploy/scripts/setup-vps.sh`.

---

## Step 3 — Upload Files

From your local machine:

```bash
# Deploy all three apps at once
bash deploy/scripts/deploy.sh YOUR.VPS.IP
```

This rsync's `backend/`, `website/`, and `dashboard/` to `/var/www/talemwa/`
(skips `.env` and `database/` so existing data is never overwritten).

---

## Step 4 — Configure Environment

```bash
ssh root@YOUR.VPS.IP

# Create .env from template
cp /var/www/talemwa/backend/.env.example /var/www/talemwa/backend/.env

# Edit and fill all values
nano /var/www/talemwa/backend/.env
```

**Required values to fill in:**

| Key | Where to get it |
|-----|----------------|
| `JWT_SECRET` | Run: `openssl rand -hex 32` |
| `FCM_SERVER_KEY` | Firebase Console → Project Settings → Cloud Messaging |
| `AZURACAST_API_KEY` | AzuraCast Admin → API Keys (after Step 5) |
| `FLUTTERWAVE_PUBLIC_KEY` | Flutterwave Dashboard → Settings → API Keys |
| `FLUTTERWAVE_SECRET_KEY` | Same as above |
| `FLUTTERWAVE_ENCRYPTION_KEY` | Same as above |
| `PAYPAL_CLIENT_ID` | PayPal Developer → My Apps |
| `PAYPAL_CLIENT_SECRET` | Same as above |

---

## Step 5 — AzuraCast (Online Radio)

```bash
ssh root@YOUR.VPS.IP
bash deploy/scripts/azuracast-setup.sh
```

During installation when prompted:
- Environment: **production**
- HTTP port: **8080**, HTTPS port: **8443**
- Let's Encrypt domain: **radio.roberttalemwa.online**
- Let's Encrypt email: **saviour@thirdsan.com**

**After installation:**
1. Visit `https://radio.roberttalemwa.online`
2. Create admin account
3. Create station: **"Talemwa Radio"**
4. Set stream URL path: `/stream`
5. Upload intro/hold music (worship songs, ~30 min loop)
6. Admin → API Keys → Generate → copy key → paste into `.env` as `AZURACAST_API_KEY`
7. Set weekly broadcast schedule

---

## Step 6 — Firebase Setup (Push Notifications)

1. Go to [Firebase Console](https://console.firebase.google.com)
2. Create project: **"Talemwa"**
3. **Add Android app**
   - Package name: `com.thirdsan.talemwa`
   - Download `google-services.json`
   - Place at `flutter-app/android/app/google-services.json`
4. **Add iOS app**
   - Bundle ID: `com.thirdsan.talemwa`
   - Download `GoogleService-Info.plist`
   - Place at `flutter-app/ios/Runner/GoogleService-Info.plist`
5. Project Settings → Cloud Messaging → **Server Key** → copy → paste into `.env` as `FCM_SERVER_KEY`

---

## Step 7 — Change Default Admin Password

**CRITICAL — do this before going live.**

Login to `https://admin.roberttalemwa.online`  
Default credentials (from seed data):
- Email: `admin@roberttalemwa.online`
- Password: `TalemwaAdmin2024!`

Go to Settings → Change Password → set a strong new password.

---

## Step 8 — Flutter App Build

```bash
cd flutter-app

# Verify Firebase files are in place:
# - android/app/google-services.json    (from Firebase Console)
# - ios/Runner/GoogleService-Info.plist (from Firebase Console)

# Get dependencies
flutter pub get

# Android — release APK (for direct install / testing)
flutter build apk --release
# Output: build/app/outputs/flutter-apk/app-release.apk

# Android — App Bundle (for Google Play)
flutter build appbundle --release
# Output: build/app/outputs/bundle/release/app-release.aab

# iOS (requires macOS + Xcode)
flutter build ios --release
# Then: open ios/ in Xcode → Product → Archive → Upload to App Store Connect
```

**Before release build:**
- Update `flutter-app/lib/core/constants.dart` → `flutterwavePublicKey` with real key
- Verify `apiBaseUrl` is `https://api.roberttalemwa.online`

---

## Step 9 — App Store Submissions

### Google Play Console
- URL: https://play.google.com/console
- App name: **Talemwa**
- Package: `com.thirdsan.talemwa`
- Category: Lifestyle → Religion & Spirituality
- Rating: Everyone
- One-time registration fee: **$25 USD**

Required assets:
- App icon: 512×512 PNG (no alpha)
- Feature graphic: 1024×500 PNG
- Screenshots: phone (min 2), tablet (optional)
- Short description (80 chars): "Sermons, radio & live streaming from Pastor Robert Talemwa"
- Full description: ministry overview + features

### Apple App Store Connect
- URL: https://appstoreconnect.apple.com
- App name: **Talemwa**
- Bundle ID: `com.thirdsan.talemwa`
- SKU: `com.thirdsan.talemwa`
- Category: Lifestyle (primary), Reference (secondary)
- Annual developer fee: **$99 USD/year**

---

## Step 10 — Flutterwave Webhook

In your Flutterwave dashboard, set the webhook URL to:
```
https://api.roberttalemwa.online/api/give/webhook
```

This fires when a payment completes and marks the giving record as `completed`.

---

## Post-Deployment Checklist

### Infrastructure
- [ ] DNS A records live for `@`, `www`, `api`, `admin`, `radio`
- [ ] SSL active on `roberttalemwa.online` — green padlock in browser
- [ ] SSL active on `api.roberttalemwa.online`
- [ ] SSL active on `admin.roberttalemwa.online`
- [ ] SSL active on `radio.roberttalemwa.online` (AzuraCast handles this)

### Backend API
- [ ] `curl https://api.roberttalemwa.online/api/live` returns `{"status":"success",...}`
- [ ] `curl https://api.roberttalemwa.online/api/sermons` returns sermon list
- [ ] Admin login: `POST /api/auth/login` works
- [ ] `.env` has all production values (no `REPLACE_` placeholders remain)

### Admin Dashboard
- [ ] Login works at `https://admin.roberttalemwa.online`
- [ ] **Default password changed** from `TalemwaAdmin2024!`
- [ ] Radio live control: GO LIVE button toggles `is_live` correctly
- [ ] Upload a test sermon — confirms SQLite write works
- [ ] Prayer submission shows in prayers list
- [ ] Push notification composer sends a test notification

### Radio
- [ ] `https://radio.roberttalemwa.online/stream` streams audio
- [ ] AzuraCast dashboard accessible
- [ ] Station name: "Talemwa Radio"
- [ ] Schedule populated with Sunday/Wednesday/Friday/Daily slots
- [ ] API proxy works: `curl https://api.roberttalemwa.online/api/radio`

### Flutter App
- [ ] Debug build connects to production API
- [ ] Live banner shows when `is_live=true`
- [ ] Sermon audio plays (both YouTube and MP3)
- [ ] Radio stream plays
- [ ] Push notification received on test device
- [ ] Prayer submission works
- [ ] Giving flow opens Flutterwave checkout
- [ ] FCM token registers on device boot
- [ ] Release APK / App Bundle generated successfully
- [ ] Submitted to Google Play (in review)
- [ ] Submitted to Apple App Store (in review)

### Website
- [ ] `https://roberttalemwa.online` loads correctly
- [ ] Sermons archive lists sermons
- [ ] Radio bar at bottom plays stream
- [ ] Live page shows YouTube player when live
- [ ] Giving page opens Flutterwave

---

## Ongoing Maintenance

### Weekly
- Check AzuraCast is running: `cd /var/azuracast && ./docker.sh status`
- Check disk space: `df -h` (uploads/sermons dir will grow)

### Monthly
- SSL renewal is automatic (Certbot timer). Verify: `certbot renew --dry-run`
- Review error logs: `tail -100 /var/log/apache2/api-error.log`

### Backups
```bash
# Backup database + uploads (run on VPS, store offsite)
tar -czf talemwa-backup-$(date +%Y%m%d).tar.gz \
  /var/www/talemwa/backend/database/ \
  /var/www/talemwa/backend/uploads/ \
  /var/www/talemwa/backend/.env
```

---

## Support

- **Developer**: Saviour — Thirdsan Enterprises Ltd, Kampala
- **Contact**: saviour@thirdsan.com
- **Client**: Pastor Robert Talemwa — roberttalemwa.online
