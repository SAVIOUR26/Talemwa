# Talemwa — Ministry Digital Platform

> **"Preach the gospel and take healing to nations"**
> — Pastor Robert Talemwa

Built by **Thirdsan Enterprises Ltd** · Kampala, Uganda

---

## Overview

A full ministry digital platform for **Pastor Robert Talemwa** — serving a local congregation in Kampala and diaspora members across the UK, US, Canada, and Europe.

| Deliverable | URL | Status |
|---|---|---|
| Ministry Website | roberttalemwa.online | ✅ Built |
| Admin Dashboard | admin.roberttalemwa.online | ✅ Built |
| Flutter Mobile App | com.thirdsan.talemwa | ✅ Built |
| Online Radio | radio.roberttalemwa.online | ✅ Built |

---

## Tech Stack

### Backend API (`/backend`)
- **PHP 8.1+** — no framework, custom router
- **SQLite** via PDO, WAL mode
- **JWT** authentication — stateless, custom implementation
- **FCM** — Firebase Cloud Messaging for push notifications
- **Flutterwave** — payments (Africa/Uganda)
- **PayPal** — diaspora giving
- **AzuraCast** — self-hosted online radio

### Admin Dashboard (`/dashboard`)
- PHP server-rendered templates
- **Alpine.js** (CDN) — reactive UI
- **Chart.js** (CDN) — statistics
- **Tailwind CSS** (CDN) — styling
- **Tabler Icons** (CDN) — iconography

### Ministry Website (`/website`)
- PHP server-rendered templates
- **Alpine.js** + **Tailwind CSS** (CDN)
- Sticky radio player, live stream banner

### Flutter App (`/flutter-app`)
- **Flutter** — Android + iOS
- **Riverpod** — state management
- **just_audio** + **audio_service** — background playback + lock screen controls
- **youtube_player_flutter** — live stream + sermons
- **Dio** — HTTP client
- **Hive** — offline cache
- **firebase_messaging** — push notifications
- **go_router** — navigation + deep links

---

## Project Structure

```
talemwa/
├── backend/                   PHP API (api.roberttalemwa.online)
│   ├── index.php              Router + bootstrap + .env loader
│   ├── .env.example           Environment template
│   ├── .htaccess              Clean URL rewrites
│   ├── core/
│   │   ├── Database.php       SQLite + auto-migrations + seed data
│   │   ├── Router.php         Path router
│   │   ├── Auth.php           JWT auth
│   │   ├── Response.php       JSON response helpers
│   │   └── Notify.php         FCM broadcast
│   └── controllers/
│       ├── SermonController.php
│       ├── RadioController.php
│       ├── LiveController.php
│       ├── EventController.php
│       ├── GivingController.php
│       ├── PrayerController.php
│       ├── CampaignController.php
│       ├── StatsController.php
│       ├── NotificationController.php
│       ├── AdminController.php
│       ├── AuthController.php
│       └── DeviceController.php
│
├── dashboard/                 Admin panel (admin.roberttalemwa.online)
│   ├── index.php
│   ├── auth/login.php
│   ├── partials/              Shared header, sidebar, footer
│   ├── pages/                 17 admin pages
│   └── assets/js/app.js       Shared helpers (apiFetch, formatCurrency, etc.)
│
├── website/                   Public site (roberttalemwa.online)
│   ├── index.php              Router
│   ├── partials/              Nav, footer, radio bar
│   └── pages/                 10 public pages
│
├── flutter-app/               Mobile app (Android + iOS)
│   ├── lib/
│   │   ├── core/              theme.dart, router.dart, constants.dart
│   │   ├── models/            Sermon, Event, LiveStatus, RadioStatus, Campaign, Prayer
│   │   ├── services/          api_service.dart, audio_service.dart, notification_service.dart
│   │   ├── providers/         sermon, radio, live, event, audio, campaign
│   │   ├── screens/           home, sermons, radio, live, give, more
│   │   └── widgets/           ScaffoldWithNav, MiniPlayer
│   └── pubspec.yaml
│
├── deploy/
│   ├── apache/                Vhost configs for all 3 subdomains
│   └── scripts/
│       ├── setup-vps.sh       Full VPS bootstrap
│       ├── ssl-certs.sh       Certbot SSL for all subdomains
│       ├── deploy.sh          rsync deploy to VPS
│       └── azuracast-setup.sh AzuraCast Docker install
│
├── DEPLOY.md                  Step-by-step deployment guide
└── CLAUDE.md                  Full platform specification
```

---

## App Features

### Flutter App — 5 Tab Navigation
| Tab | Features |
|---|---|
| **Home** | Live banner (pulses red when live), radio mini-player, featured sermon, events strip, quick actions |
| **Sermons** | Infinite scroll, search, series filter chips, audio player with seek + speed control |
| **Radio** | Full-screen player, now-playing (30s poll), schedule grid |
| **Give** | Tithe/Offering/Project/Campaign types, Flutterwave WebView checkout, campaign progress bars |
| **More** | Prayer request, events, about pastor, social links, notification preferences |

**Persistent mini-player** — shows above nav bar whenever a sermon is playing. Background audio + lock screen controls via `audio_service`.

**Push notifications** deep-link to:
- `live` → Live screen
- `sermon:{id}` → Sermon Detail
- `event:{id}` → Event Detail

**Offline support** — Hive caches sermon list, events, live status, and radio status.

### Admin Dashboard — 17 Pages
- **Radio Live Control** — GO LIVE / END STREAM toggle with YouTube ID + title prompt; fires FCM to all users automatically
- **Sermon Upload** — YouTube oEmbed auto-fetch (thumbnail + duration), MP3 upload, series management, push-on-publish
- **Giving Records** — Filterable table, CSV export, per-currency totals
- **Prayer Requests** — Split-pane list + detail, mark read/prayed/urgent, mailto reply
- **Push Notifications** — Compose with live phone preview, target by platform
- **Statistics** — App installs (Android vs iOS), giving trends, top sermons by plays

### API — 40+ Endpoints
All responses follow the envelope: `{ "status": "success|error", "data": {...} }`

Public endpoints cover sermons, radio, live status, events, campaigns, prayer, giving, and device registration. Admin endpoints (JWT-protected) cover all CRUD operations + stats + FCM broadcast.

---

## Getting Started (Local Development)

### Backend API
```bash
# PHP built-in server — no Apache needed locally
cd backend
cp .env.example .env       # fill in values
php -S localhost:8000
```

### Admin Dashboard
```bash
cd dashboard
php -S localhost:8001
# Login: admin@roberttalemwa.online / TalemwaAdmin2024!
```

### Website
```bash
cd website
php -S localhost:8002
```

### Flutter App
```bash
cd flutter-app

# Place real Firebase config files:
# android/app/google-services.json     (from Firebase Console)
# ios/Runner/GoogleService-Info.plist  (from Firebase Console)

flutter pub get
flutter run
```

> **Note:** For local development, update `flutter-app/lib/core/constants.dart` → `apiBaseUrl` to point to your local PHP server IP.

---

## Deployment

See **[DEPLOY.md](DEPLOY.md)** for the full 10-step production deployment guide covering:
- Namecheap DNS setup
- VPS bootstrap (Apache + PHP 8.2 + UFW)
- Let's Encrypt SSL for all subdomains
- AzuraCast Docker install for online radio
- Firebase push notification setup
- Flutter release build (APK + App Bundle)
- Google Play + Apple App Store submission checklist

**Quick deploy** (after VPS is configured):
```bash
bash deploy/scripts/deploy.sh YOUR.VPS.IP
```

---

## Environment Variables

Copy `backend/.env.example` to `backend/.env` and fill in:

| Variable | Description |
|---|---|
| `JWT_SECRET` | 64-char random string (`openssl rand -hex 32`) |
| `FCM_SERVER_KEY` | Firebase → Project Settings → Cloud Messaging |
| `AZURACAST_API_KEY` | AzuraCast Admin → API Keys |
| `FLUTTERWAVE_SECRET_KEY` | Flutterwave Dashboard → Settings → API Keys |
| `PAYPAL_CLIENT_ID` / `SECRET` | PayPal Developer → My Apps |

---

## Database

SQLite at `backend/database/ministry.db` — auto-created and migrated on first boot.

**Tables:** `admins`, `sermons`, `events`, `live_status`, `givings`, `campaigns`, `prayers`, `device_tokens`, `app_installs`, `notifications_log`, `radio_schedule`

Default admin seeded on first boot:
- Email: `admin@roberttalemwa.online`
- Password: `TalemwaAdmin2024!` ← **change this before going live**

---

## Design System

| Token | Value |
|---|---|
| Primary | Deep Navy `#0A1628` |
| Accent | Gold `#C9A84C` |
| Background | White `#FFFFFF` |
| Surface | Soft Grey `#F5F5F7` |
| Live | Red `#DC2626` |
| Font | Inter (all weights) |
| Dark mode | Fully supported (`ThemeMode.system`) |

---

## Credits

| Role | Person |
|---|---|
| Developer | Saviour — Thirdsan Enterprises Ltd, Kampala |
| Client | Pastor Robert Talemwa |
| Domain registrar | Namecheap |
| Hosting | RackNerd VPS |
| Radio | AzuraCast (open source) |
| Payments | Flutterwave + PayPal |

---

*Stack philosophy: Use the simplest thing that survives production. Add complexity only when you have evidence that simplicity has failed.*
