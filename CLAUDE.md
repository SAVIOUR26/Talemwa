# Talemwa — Ministry Digital Platform
## Claude Code Handoff Document
### Built by Thirdsan Enterprises Ltd · Kampala, Uganda

---

## Client

**Pastor Robert Talemwa**
- Missionary preacher, Kampala, Uganda
- Mission: "Preach the gospel and take healing to nations"
- Audience: Local congregation (Kampala) + diaspora members (UK, US, Canada, Europe)
- Social presence: YouTube, TikTok, Facebook (already active — 11,000+ Facebook likes)
- YouTube channel: https://www.youtube.com/@pastortalemwarobert4160

---

## Project Identity

| Item | Value |
|------|-------|
| App name | **Talemwa** |
| App subtitle | Pastor Robert Talemwa · Ministry App |
| Primary domain | **roberttalemwa.online** |
| Admin dashboard | **admin.roberttalemwa.online** |
| Radio stream | **radio.roberttalemwa.online** |
| API base URL | **api.roberttalemwa.online** |
| Bundle ID (Android) | `com.thirdsan.talemwa` |
| Bundle ID (iOS) | `com.thirdsan.talemwa` |
| App Store name | Talemwa |

---

## Core Philosophy
> "Use the simplest thing that survives production. Add complexity only when you have
> evidence that simplicity has failed. Never add it in anticipation."
> — Thirdsan Engineering Principle (ThirdMoney stack discovery)

This is not a Laravel project. This is not a React project. Do not suggest adding
frameworks, build pipelines, or additional abstraction layers unless explicitly asked.
The stack was chosen deliberately — respect that choice.

---

## Deliverables

1. **Ministry website** — roberttalemwa.online
2. **Admin dashboard** — admin.roberttalemwa.online
3. **Flutter mobile app** — "Talemwa" (Android + iOS)
4. **Online radio** — AzuraCast on radio.roberttalemwa.online

---

## Tech Stack

### Backend API
- **Language**: PHP 8.1+ — no framework
- **Router**: Custom (Router.php — already built)
- **Database**: SQLite via PDO, WAL mode — `backend/database/ministry.db`
- **Auth**: Stateless JWT — custom implementation (Auth.php — already built)
- **Radio**: AzuraCast self-hosted on same VPS
- **Video CDN**: YouTube (live stream + sermon archive — free, global CDN)
- **Push**: Firebase Cloud Messaging (FCM)
- **Payments**: Flutterwave (Africa/Uganda) + PayPal (diaspora)
- **Deployment**: PHP files via FTP/SSH — no Docker, no CI pipeline

### Admin Dashboard
- **PHP** templates (server-rendered)
- **Alpine.js** via CDN — reactive UI, no build step
- **Chart.js** via CDN — statistics and charts
- **Tabler Icons** via CDN — consistent iconography
- **Same JWT auth** as the API

### Flutter App ("Talemwa")
- **Flutter** — Dart, cross-platform Android + iOS
- **State**: Riverpod
- **Audio**: just_audio + audio_service (background playback + lock screen controls)
- **Video**: youtube_player_flutter
- **HTTP**: Dio
- **Local cache**: Hive
- **Push**: firebase_messaging
- **Navigation**: go_router (bottom nav + deep links)

### Infrastructure
- **VPS**: RackNerd (Thirdsan existing infrastructure)
- **Domain**: roberttalemwa.online (Namecheap)
- **SSL**: Let's Encrypt (auto via Certbot)
- **Radio**: AzuraCast (Docker on VPS, subdomain radio.roberttalemwa.online)
- **Firebase**: FCM for push notifications (free tier)

---

## Database Schema (SQLite — auto-migrated on boot)

```sql
-- Admin accounts
admins (
  id, name, email, password, role, last_login, created_at
)
-- role: super_admin | media | viewer

-- Sermons
sermons (
  id, title, series, speaker, description,
  youtube_url, mp3_url, duration_seconds,
  thumbnail_url, scripture, tags,
  published, download_count, play_count,
  created_at
)

-- Events
events (
  id, title, description, event_date, event_time,
  location, is_online, stream_url, created_at
)

-- Live status (single row, always id=1)
live_status (
  id, is_live, youtube_id, title, updated_at
)

-- Giving records
givings (
  id, reference, amount, currency, giving_type,
  donor_name, donor_email, status, provider,
  campaign_id, created_at
)
-- giving_type: tithe | offering | project | campaign
-- status: pending | completed | failed

-- Giving campaigns
campaigns (
  id, title, description, goal_amount, currency,
  raised_amount, deadline, is_active, created_at
)

-- Prayer requests
prayers (
  id, message, contact, is_read, is_prayed,
  is_urgent, created_at
)

-- Device tokens (for FCM push)
device_tokens (
  id, token, platform, country, created_at
)
-- platform: android | ios

-- App installs tracking
app_installs (
  id, platform, country, app_version, created_at
)

-- Push notification log
notifications_log (
  id, title, message, target, sent_count,
  delivered_count, opened_count, created_at
)
-- target: all | android | ios

-- Radio schedule
radio_schedule (
  id, day_of_week, start_time, end_time,
  program_name, description, is_active
)
-- day_of_week: monday | tuesday | ... | sunday | daily
```

**Seed data on first boot:**
- Default admin: `admin@roberttalemwa.online` / `TalemwaAdmin2024!` ← CHANGE IN PRODUCTION
- Default live_status row: is_live=0
- Default radio schedule: Sunday 10AM service, Wednesday 7PM Bible study, Friday 8PM prayer, Daily 6AM devotion

---

## Full API Endpoints

### Public (no auth)
```
GET    /api/sermons                  List sermons (paginated, ?search=, ?series=, ?page=)
GET    /api/sermons/{id}             Single sermon (also increments play_count)
GET    /api/sermons/series           All series names + sermon counts
GET    /api/radio                    Now playing + stream URL (proxies AzuraCast)
GET    /api/radio/schedule           Weekly broadcast schedule
GET    /api/live                     Live status (is_live, youtube_id, title)
GET    /api/events                   Upcoming events
GET    /api/events/{id}              Single event detail
GET    /api/campaigns                Active giving campaigns
POST   /api/prayer                   Submit prayer request {message, contact?}
POST   /api/give/initiate            Start giving {amount, currency, giving_type, donor_name?, donor_email?, campaign_id?}
POST   /api/give/webhook             Flutterwave webhook receiver
POST   /api/device/register          Register FCM token {token, platform, country?}
POST   /api/install/track            Track app install {platform, country?, app_version?}
POST   /api/auth/login               Admin login {email, password} → {token, name, role}
```

### Admin (JWT: Authorization: Bearer {token})
```
POST   /api/admin/sermons            Upload sermon
PUT    /api/admin/sermons/{id}       Edit sermon
DELETE /api/admin/sermons/{id}       Soft-delete sermon (sets published=0)

POST   /api/admin/live               Toggle live {is_live, youtube_id?, title?}

POST   /api/admin/events             Create event
PUT    /api/admin/events/{id}        Edit event
DELETE /api/admin/events/{id}        Delete event

POST   /api/admin/campaigns          Create giving campaign
PUT    /api/admin/campaigns/{id}     Update campaign

GET    /api/admin/prayers            All prayer requests
PUT    /api/admin/prayers/{id}       Update prayer {is_read, is_prayed, is_urgent}

GET    /api/admin/givings            All giving records (filterable by date, currency, type)
GET    /api/admin/givings/summary    Totals by currency, type, month

GET    /api/admin/stats              Dashboard overview stats
GET    /api/admin/stats/installs     App install trends (daily/weekly/monthly)
GET    /api/admin/stats/sermons      Top sermons by plays + downloads
GET    /api/admin/stats/giving       Giving trends + breakdown

POST   /api/admin/notify             Broadcast push {title, message, target, data?}
GET    /api/admin/notify/history     Notification send history

POST   /api/admin/radio/schedule     Update radio schedule slot
GET    /api/admin/admins             List admin users (super_admin only)
POST   /api/admin/admins             Create admin user (super_admin only)
DELETE /api/admin/admins/{id}        Remove admin (super_admin only)
```

### All API responses follow this envelope:
```json
{ "status": "success", "data": {...} }
{ "status": "error", "data": { "error": "message" } }
```

---

## Admin Dashboard — Full Specification

**URL**: admin.roberttalemwa.online
**Auth**: Email + password → JWT stored in localStorage
**Stack**: PHP templates + Alpine.js + Chart.js (all via CDN, no build step)

### Sidebar Navigation
```
Dashboard (overview)
├── 📻 Radio
│   ├── Live Control
│   ├── Schedule
│   └── Listener Stats
├── 🎙️ Sermons
│   ├── All Sermons
│   ├── Upload Sermon
│   └── Series Manager
├── 📱 App
│   ├── Installs & Users
│   ├── Notifications
│   └── Notification History
├── 💝 Giving
│   ├── Records
│   ├── Campaigns
│   └── Reports
├── 🙏 Prayer Requests
├── 📅 Events
├── 👥 Admin Users  (super_admin only)
└── ⚙️ Settings
```

### Page 1 — Dashboard Overview
Visible immediately on login.

**Top stat cards (grid of 4):**
- Total sermons uploaded
- Radio status (🟢 Live / ⚫ Off-air) + current listener count
- App installs (total, +X this week)
- Total giving this month (primary currency)

**Charts (Chart.js):**
- App installs — 30-day line chart (Android vs iOS)
- Giving trend — 6-month bar chart (by currency)
- Sermon plays — top 10 bar chart

**Activity feed (last 20 events):**
- New prayer request received
- Sermon uploaded
- Giving record (amount + type, no donor name in feed)
- App install milestone
- Live stream started/ended
- Push notification sent

### Page 2 — Radio: Live Control
The most critical page — pastor's team uses this every Sunday.

**Live toggle section:**
- Giant toggle button: GO LIVE / END STREAM
- When toggling ON: modal prompts for YouTube Video ID + service title
- Confirmation sends: PUT /api/admin/live + FCM broadcast fires automatically
- Current live status shown with duration counter
- "Preview stream" button — opens stream URL in new tab

**Now Playing panel:**
- Pulls from AzuraCast API every 30 seconds
- Shows: current track/sermon title, artist, album art
- Listener count (live, updates every 30s)
- Stream health indicator (bitrate, uptime)

### Page 3 — Radio: Schedule
Weekly grid view.

- 7-day × time-slot grid
- Click any slot to assign a program
- Programs: Sunday Service Replay, Bible Study, Prayer Hour, Worship Music, Morning Devotion, Custom
- Drag to resize duration
- Saves to radio_schedule table
- "Sync to AzuraCast" button pushes schedule via AzuraCast API

### Page 4 — Radio: Listener Stats
- Total listener-hours (weekly/monthly)
- Peak concurrent listeners (with date/time)
- Geographic breakdown — top 10 countries (diaspora visibility)
- Platform breakdown (web vs app)
- All data pulled from AzuraCast API

### Page 5 — Sermons: All Sermons
- Table: title, series, date, plays, downloads, status
- Search + filter by series, speaker, date range
- Inline status toggle (published/draft)
- Edit / Delete actions per row
- Sort by plays, date, downloads

### Page 6 — Sermons: Upload Sermon
Form fields:
- Title (required)
- Series (dropdown — existing series or create new)
- Speaker (default: "Pastor Robert Talemwa")
- Date preached
- Scripture reference
- Description
- YouTube URL (paste → auto-fetches thumbnail + duration via YouTube oEmbed)
- OR upload MP3 file (drag-and-drop, shows upload progress bar)
- Tags (comma-separated)
- Thumbnail (auto from YouTube or manual upload)
- Status: Draft / Publish immediately
- ☑ Send push notification on publish (default checked)

On submit:
1. Save to SQLite
2. If "send push" checked → FCM broadcast: "🎙️ New Sermon: {title}"
3. Redirect to sermon list with success message

### Page 7 — Sermons: Series Manager
- List all series with sermon count
- Create new series (name + optional description + cover image)
- Rename series
- Merge two series into one
- Archive series (hides from app browse, keeps sermons)

### Page 8 — App: Installs & Users
- Total installs card (Android / iOS split)
- Daily install chart (30 days) — Chart.js line
- Weekly install chart (12 weeks)
- Country breakdown table (top 20 — diaspora visibility)
- App version breakdown (for knowing when to prompt updates)
- Active devices (tokens in device_tokens table)

### Page 9 — App: Send Notification
Form:
- Title
- Message body
- Target: All users / Android only / iOS only
- Data payload type: sermon | live | event | general
- If sermon: dropdown to select sermon
- If event: dropdown to select event
- Schedule: Send now / Schedule for later (datetime picker)
- Preview (shows how it looks on phone)
- Send button

### Page 10 — App: Notification History
- Table: title, message, target, sent, delivered, opened, date
- Open rate percentage column
- Re-send button (for important ones)

### Page 11 — Giving: Records
- Table: date, donor name, amount, currency, type, status
- Filter by: date range, currency, giving type, status
- Search by donor name or reference
- Export to CSV button
- Total at bottom of filtered results
- Click row → full giving detail modal

### Page 12 — Giving: Campaigns
- List active campaigns with progress bars (raised vs goal)
- Create campaign: title, description, goal amount, currency, deadline
- Share campaign (generates shareable link for WhatsApp/social)
- Close/archive campaign
- Campaign giving breakdown

### Page 13 — Giving: Reports
- Monthly summary table: month, total UGX, total USD, total GBP, transaction count
- By giving type: tithe vs offering vs project (pie chart)
- Export full report as CSV
- Currency conversion note (display only, not stored)

### Page 14 — Prayer Requests
- Table: date, message preview, contact, status (unread/read/prayed)
- Unread count badge in sidebar
- Urgent flag (red badge)
- Click row → full prayer detail panel
- Mark as prayed button
- Mark urgent button
- Optional: email reply link (opens mailto: with pre-filled subject)
- Filter: all / unread / urgent / prayed

### Page 15 — Events
- List upcoming + past events
- Create event: title, description, date, time, location, online toggle, stream URL
- Edit / Delete
- Send push notification for event (button per event)

### Page 16 — Admin Users (super_admin only)
- List all admin accounts: name, email, role, last login
- Create new admin (name, email, password, role)
- Edit role
- Suspend / delete account
- Role definitions shown: Super Admin (full access), Media Team (sermons + radio + events), Viewer (stats only)

### Page 17 — Settings
Sections:
- **Ministry identity**: name, tagline, logo upload, about text
- **Social links**: YouTube channel URL, Facebook page, TikTok handle
- **App store links**: Google Play URL, App Store URL
- **AzuraCast**: stream URL, station ID, API key (test connection button)
- **Firebase**: FCM server key (masked, update only)
- **Flutterwave**: public key, secret key (masked)
- **PayPal**: client ID (masked)
- **Maintenance mode**: toggle (shows maintenance page on website + message in app)
- **Default currency**: UGX / USD / GBP / EUR
- **Change password** (current admin account)

---

## Flutter App ("Talemwa") — Full Specification

### App Identity
```
Name:        Talemwa
Tagline:     Healing to the Nations
Colors:      Deep Navy #0A1628 (primary)
             Gold #C9A84C (accent)
             White #FFFFFF (background)
             Soft Grey #F5F5F7 (surface)
Font:        Inter (all weights)
Dark mode:   Fully supported (ThemeMode.system)
```

### Navigation Structure
Bottom navigation bar — 5 tabs:
```
Home  |  Sermons  |  Radio  |  Give  |  More
```

### Screen: Home
- Live banner (red, full-width) — shows only when is_live=true
  - Tapping opens Live screen
  - Animated pulsing red dot
- Featured sermon card (latest uploaded)
- Radio mini-player (always visible if radio is online)
  - Shows now playing title + artist
  - Play/pause button
  - Tap → expands to full Radio screen
- Upcoming events strip (horizontal scroll)
- Quick actions row: Sermons · Give · Prayer · Events
- Polls /api/live every 60 seconds

### Screen: Live
- YouTube player (full-width, 16:9)
- Service title below player
- "Switch to Audio Only" toggle → connects to radio stream instead (for slow connections)
- Share button (share YouTube link)
- Live chat placeholder (future Phase 2)
- "Previous Services" section below (links to sermon archive)

### Screen: Sermons
- Search bar (calls /api/sermons?search=)
- Series filter chips (horizontal scroll)
- Sermon list (infinite scroll pagination)
- Each sermon card: thumbnail, title, series, date, duration, play count
- Tap → Sermon Detail screen

### Screen: Sermon Detail
- Thumbnail hero image
- Title, series badge, date, scripture
- Description (expandable)
- Audio player bar (just_audio):
  - Play/pause, seek slider, speed control (0.75x, 1x, 1.25x, 1.5x)
  - Background playback (audio_service)
  - Lock screen controls
- Download for offline button (Dio → local storage)
- Share button
- "More from this series" section below

### Screen: Radio
- Full-screen player
- Ministry logo / now-playing album art (large)
- Now playing title + artist (updates every 30s)
- Play/pause button (large)
- Volume slider
- Schedule tab — weekly program grid
- Listener count ("312 listening now")
- Share stream button

### Screen: Give
- Giving type selector: Tithe / Offering / Project / Campaign
- Amount input + currency selector (UGX / USD / GBP / EUR)
- Active campaigns (if any) — shows progress bar
- Donor name + email (optional)
- "Give Now" button → Flutterwave WebView checkout
- PayPal button (for diaspora)
- Giving history (this session only)
- Scripture card: "Give and it shall be given unto you" (rotating)

### Screen: More
- Prayer request (form → POST /api/prayer)
- Events (list → detail)
- About Pastor Robert (bio, photo, mission statement)
- Social links (YouTube, Facebook, TikTok — open in browser)
- App info (version, website link)
- Notification preferences (toggle sermon alerts, live alerts, event alerts)
- Settings (dark mode toggle, language — future)

### Persistent Audio Mini-Player
- Shown at bottom (above nav bar) whenever a sermon is playing
- Sermon thumbnail (small), title, play/pause, close
- Tap → expands to Sermon Detail screen
- Survives navigation between tabs

### Push Notification Handling
```
Payload type "live"    → open Live screen, pass youtube_id
Payload type "sermon"  → open Sermon Detail screen, pass sermon id
Payload type "event"   → open Event Detail screen, pass event id
Payload type "general" → show notification only
```

### Offline Support
- Hive caches: last sermon list, last events list, last radio status
- Downloaded sermons stored in app documents directory
- Offline indicator shown when no internet
- Downloaded sermons playable fully offline

### Deep Links
```
talemwa://live              → Live screen
talemwa://sermon/{id}       → Sermon Detail
talemwa://give              → Give screen
talemwa://event/{id}        → Event Detail
```

---

## Ministry Website — roberttalemwa.online

**Stack**: PHP templates + Alpine.js + Tailwind CDN

### Pages
```
/                   Home
/sermons            Sermon archive (search + filter)
/sermons/{id}       Single sermon (player + details)
/live               Live stream page
/radio              Radio player + schedule
/events             Upcoming events
/give               Online giving (Flutterwave)
/prayer             Prayer request form
/about              About Pastor Robert
/contact            Contact + location
```

### Home Page Sections
1. Hero — "Healing to the Nations" + Live banner (if live) + CTA: Watch Live / Listen to Radio
2. Featured sermon (latest)
3. About Pastor Robert (brief + photo)
4. Embedded radio player (always visible)
5. Upcoming events (next 3)
6. App download banner (Google Play + App Store badges)
7. Giving CTA
8. Footer (social links, copyright, privacy policy)

### Embedded Radio Player (all pages)
- Sticky bottom bar on website
- Shows now-playing, play/pause
- Persists across page navigation

---

## Environment Variables

```env
# Security
JWT_SECRET=                    # Strong 64-char random string

# Firebase
FCM_SERVER_KEY=                # Firebase Console → Project Settings → Cloud Messaging

# Radio (AzuraCast)
STREAM_URL=https://radio.roberttalemwa.online/stream
AZURACAST_URL=https://radio.roberttalemwa.online
AZURACAST_STATION=1
AZURACAST_API_KEY=             # AzuraCast Admin → API Keys

# Payments
FLUTTERWAVE_PUBLIC_KEY=        # Flutterwave Dashboard
FLUTTERWAVE_SECRET_KEY=
FLUTTERWAVE_ENCRYPTION_KEY=
PAYPAL_CLIENT_ID=
PAYPAL_CLIENT_SECRET=
PAYPAL_MODE=live               # sandbox | live

# App
APP_NAME=Talemwa
APP_URL=https://roberttalemwa.online
ADMIN_URL=https://admin.roberttalemwa.online
API_URL=https://api.roberttalemwa.online

# Upload paths
UPLOAD_PATH=/var/www/api/uploads/sermons/
MAX_UPLOAD_MB=200
```

---

## Project File Structure

```
talemwa-platform/
│
├── CLAUDE.md                          ← You are here
│
├── backend/                           ← PHP API (api.roberttalemwa.online)
│   ├── index.php                      ← Router + bootstrap (BUILT)
│   ├── .htaccess                      ← Clean URL rewrites (BUILT)
│   ├── .env.example                   ← Environment template (BUILT)
│   ├── core/
│   │   ├── Database.php               ← SQLite + auto-migrations (BUILT — UPDATE SCHEMA)
│   │   ├── Router.php                 ← Path router (BUILT)
│   │   ├── Auth.php                   ← JWT auth (BUILT)
│   │   ├── Response.php               ← JSON responses (BUILT)
│   │   └── Notify.php                 ← FCM broadcast (BUILT)
│   ├── controllers/
│   │   ├── SermonController.php       ← BUILT
│   │   ├── LiveController.php         ← BUILT
│   │   ├── RadioController.php        ← BUILT
│   │   ├── EventController.php        ← BUILT
│   │   ├── GivingController.php       ← BUILT (needs Flutterwave API integration)
│   │   ├── PrayerController.php       ← BUILT
│   │   ├── AuthController.php         ← BUILT
│   │   ├── DeviceController.php       ← BUILT
│   │   ├── CampaignController.php     ← TODO
│   │   ├── StatsController.php        ← TODO
│   │   ├── NotificationController.php ← TODO
│   │   └── AdminController.php        ← TODO (admin user management)
│   └── uploads/
│       └── sermons/                   ← MP3 upload storage
│
├── dashboard/                         ← Admin (admin.roberttalemwa.online)
│   ├── index.php                      ← Login page
│   ├── .htaccess
│   ├── auth/
│   │   ├── login.php
│   │   └── logout.php
│   ├── pages/
│   │   ├── overview.php               ← Dashboard home + stats
│   │   ├── radio-live.php             ← Live control panel
│   │   ├── radio-schedule.php
│   │   ├── radio-stats.php
│   │   ├── sermons-list.php
│   │   ├── sermons-upload.php
│   │   ├── sermons-series.php
│   │   ├── app-installs.php
│   │   ├── app-notifications.php
│   │   ├── app-notify-history.php
│   │   ├── giving-records.php
│   │   ├── giving-campaigns.php
│   │   ├── giving-reports.php
│   │   ├── prayers.php
│   │   ├── events.php
│   │   ├── admin-users.php
│   │   └── settings.php
│   ├── partials/
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   └── footer.php
│   └── assets/
│       └── (no build — all CDN)
│
├── website/                           ← Public site (roberttalemwa.online)
│   ├── index.php
│   ├── .htaccess
│   └── pages/
│       ├── sermons.php
│       ├── live.php
│       ├── radio.php
│       ├── events.php
│       ├── give.php
│       ├── prayer.php
│       ├── about.php
│       └── contact.php
│
└── flutter-app/                       ← "Talemwa" app
    ├── pubspec.yaml                   ← BUILT
    ├── STRUCTURE.md                   ← BUILT
    └── lib/
        ├── main.dart                  ← BUILT
        ├── core/
        │   ├── router.dart            ← TODO
        │   ├── theme.dart             ← TODO
        │   └── constants.dart         ← TODO
        ├── models/
        │   ├── sermon.dart            ← TODO
        │   ├── event.dart             ← TODO
        │   ├── live_status.dart       ← TODO
        │   ├── radio_status.dart      ← TODO
        │   ├── campaign.dart          ← TODO
        │   └── prayer.dart            ← TODO
        ├── services/
        │   ├── api_service.dart       ← BUILT (needs campaign + stats endpoints)
        │   ├── audio_service.dart     ← TODO
        │   └── notification_service.dart ← TODO
        ├── providers/
        │   ├── sermon_provider.dart   ← TODO
        │   ├── radio_provider.dart    ← TODO
        │   ├── live_provider.dart     ← TODO
        │   └── event_provider.dart    ← TODO
        └── screens/
            ├── home/
            ├── live/
            ├── sermons/
            ├── radio/
            ├── give/
            └── more/
```

---

## Build Order (Recommended)

### Phase 1 — Backend Foundation
1. Update `Database.php` with full schema (all new tables)
2. Update `index.php` routes (add campaign, stats, notification, admin routes)
3. Build `CampaignController.php`
4. Build `StatsController.php`
5. Build `NotificationController.php`
6. Build `AdminController.php`
7. Complete `GivingController.php` with Flutterwave API integration
8. Test all endpoints with Postman/curl

### Phase 2 — Admin Dashboard
1. Login page + JWT session management
2. Shared layout (sidebar + header + footer partials)
3. Overview page (stat cards + charts via Chart.js)
4. Radio live control page (most critical — needed for Sunday)
5. Sermon upload + list pages
6. Prayer requests page
7. Giving records page
8. Events page
9. Push notification composer
10. App install stats page
11. Settings page
12. Admin users page (super_admin only)

### Phase 3 — Website
1. Home page (hero + radio player + app download CTA)
2. Sermons archive page
3. Live stream page
4. Giving page (Flutterwave embed)
5. Prayer + Contact + About pages
6. Sticky radio player bar

### Phase 4 — Flutter App
1. `core/theme.dart` (Navy + Gold, Inter font, light/dark)
2. `core/router.dart` (GoRouter, bottom nav, deep links)
3. All models (fromJson constructors)
4. All Riverpod providers
5. `services/audio_service.dart` (just_audio + background playback)
6. `services/notification_service.dart` (FCM init + token registration)
7. Home screen (live banner + sermon card + radio mini-player)
8. Sermons screen + Sermon Detail screen + audio player
9. Radio screen (full player + schedule)
10. Live screen (YouTube embed + audio fallback)
11. Give screen (Flutterwave WebView + PayPal)
12. More screen (prayer + events + about + settings)
13. Persistent audio mini-player bar
14. Offline caching (Hive)
15. Push notification deep linking

### Phase 5 — Deployment
1. Point roberttalemwa.online → VPS
2. Point api.roberttalemwa.online → /backend
3. Point admin.roberttalemwa.online → /dashboard
4. Point radio.roberttalemwa.online → AzuraCast (port 8080)
5. SSL certificates (Certbot for all subdomains)
6. Set all .env values in production
7. Change default admin password
8. AzuraCast initial setup (create station, upload intro audio)
9. Firebase project setup (Android + iOS app registered)
10. Google Play Console setup (com.thirdsan.talemwa)
11. Apple Developer setup (com.thirdsan.talemwa)
12. Submit app for review

---

## Do Not
- Do not add Laravel, Symfony, or any PHP framework
- Do not add npm, Vite, webpack, or any JS build pipeline
- Do not replace SQLite with MySQL/PostgreSQL (until explicitly asked)
- Do not add Redis, queues, or caching layers
- Do not add Docker (except AzuraCast which requires it)
- Do not add additional Flutter state management beyond Riverpod
- Do not add GraphQL
- Do not add a separate CMS (the admin dashboard IS the CMS)
- Do not add third-party analytics SDKs to the Flutter app (track installs ourselves)

## Preferred Patterns
- PHP: raw PDO with named parameters (:param style)
- PHP: early returns over nested conditionals
- PHP: each controller method should be readable in under 30 seconds
- Flutter: const constructors everywhere possible
- Flutter: functional widgets over class widgets where simple
- Flutter: named routes via GoRouter only
- Flutter: all API calls go through ApiService — never call Dio directly from a screen
- Dashboard: Alpine.js x-data on the component that needs it, not globally
- Dashboard: Chart.js — initialize inside Alpine.js x-init
- All API responses: { "status": "success/error", "data": {...} } envelope always

---

## Key Business Logic Notes

### Live Stream Flow
1. Admin opens Radio → Live Control in dashboard
2. Hits "GO LIVE" button → modal asks for YouTube Video ID + title
3. Dashboard calls PUT /api/admin/live with {is_live:1, youtube_id, title}
4. API updates live_status table
5. API fires FCM broadcast: "🔴 We Are Live! — {title} — Join us now"
6. All app users get push notification
7. App home screen polls /api/live every 60s → detects is_live=true → shows red banner
8. Tapping banner opens Live screen with YouTube player embedded
9. After service: admin hits "END STREAM" → is_live=0, no FCM sent

### Sermon Upload + Auto-Notify Flow
1. Admin fills sermon form, pastes YouTube URL
2. Dashboard fetches YouTube oEmbed to get thumbnail + duration automatically
3. On submit → POST /api/admin/sermons
4. If "notify on publish" checked → FCM: "🎙️ New Sermon: {title}"
5. App sermon list refreshes on next open (Hive cache invalidated by version header)

### Giving Flow
1. User selects giving type + amount + currency in app
2. App calls POST /api/give/initiate → gets reference + Flutterwave config
3. App opens Flutterwave inline checkout in WebView
4. On payment success → Flutterwave calls POST /api/give/webhook
5. API marks giving as completed
6. Dashboard giving records update in real time (polling every 60s)

### Prayer Request Flow
1. Member submits prayer in app → POST /api/prayer
2. Dashboard shows unread badge count in sidebar
3. Pastor/admin opens prayer, reads, marks as prayed
4. Urgent flag for prayers needing immediate attention
5. Optional: click email link to respond directly to member

---

*Built by Thirdsan Enterprises Ltd — Kampala, Uganda*
*Developer: Saviour | saviour@thirdsan.com*
*Client: Pastor Robert Talemwa | roberttalemwa.online*
*Stack philosophy: Simple things that survive production.*
