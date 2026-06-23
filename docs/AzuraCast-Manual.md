# Miracles Now Radio — AzuraCast Operating Manual

**Station:** Miracles Now Radio
**Dashboard:** https://radio.roberttalemwa.online
**Prepared for:** Pastor Robert Talemwa & Ministry Media Team
**Prepared by:** Thirdsan Enterprises Ltd

---

## Table of Contents

1. [Logging In](#1-logging-in)
2. [Understanding the Dashboard Layout](#2-understanding-the-dashboard-layout)
3. [Uploading Music & Sermons](#3-uploading-music--sermons)
4. [Playlists — The Heart of Your Station](#4-playlists--the-heart-of-your-station)
5. [Running a Live Program (Web DJ)](#5-running-a-live-program-web-dj)
6. [Scheduling Programs (Weekly Timetable)](#6-scheduling-programs-weekly-timetable)
7. [Background Music Between Talks](#7-background-music-between-talks)
8. [Running Adverts / Announcements](#8-running-adverts--announcements)
9. [Making Programs Visible on the Website & App](#9-making-programs-visible-on-the-website--app)
10. [Volume, Normalization & Sound Quality](#10-volume-normalization--sound-quality)
11. [Monitoring Listeners](#11-monitoring-listeners)
12. [Starting / Stopping / Restarting the Station](#12-starting--stopping--restarting-the-station)
13. [Troubleshooting](#13-troubleshooting)
14. [Quick Reference Cheat Sheet](#14-quick-reference-cheat-sheet)

---

## 1. Logging In

1. Open your browser and go to: **https://radio.roberttalemwa.online**
2. Enter your admin email and password.
3. You will land on the **Stations** list. Click **MANAGE** next to "Miracles Now Radio" to enter the station's control room.

> 💡 Everything described in this manual happens *inside* the Miracles Now Radio station (after clicking MANAGE), unless stated otherwise.

---

## 2. Understanding the Dashboard Layout

The left sidebar inside a station has these key sections:

| Menu Item | What it's for |
|---|---|
| **Overview** | Start/stop the station, see what's playing right now, see if Icecast & AutoDJ are running |
| **Media → Music Files** | Upload and manage all your audio files |
| **Media → Playlists** | Group songs/sermons into rotations and schedules |
| **Media → Podcasts** | (Optional) publish sermon series as a podcast feed |
| **Streamers / DJs** | Create login accounts for live broadcasting (Web DJ or external software) |
| **Web Hooks** | Advanced — connects events to other systems (not needed for daily use) |
| **Reports** | Listener statistics, song history |
| **Broadcasting** | Shows Icecast/AutoDJ technical status and stream credentials |
| **Logs** | Technical logs — only needed when something breaks |

---

## 3. Uploading Music & Sermons

1. Go to **Media → Music Files**.
2. Click **SELECT FILE** (or drag-and-drop files directly onto the page).
3. Supported formats: MP3, AAC/M4A, OGG, FLAC. MP3 is recommended for compatibility.
4. Wait for the upload + processing to finish — AzuraCast automatically reads the song title, artist, and duration from the file.
5. To fix incorrect title/artist info, tick the checkbox next to the file and click **EDIT**, or use the **EDIT** button in its row.

**Naming tip:** Name your files clearly before uploading, e.g. `Sunday-Service-2026-06-14.mp3` or `Worship-Medley-01.mp3` — this becomes the default title and makes the library easier to manage later.

**Bulk uploads:** Use **Media → Bulk Media Import/Export** if you ever need to upload dozens of files at once via SFTP (ask Thirdsan for SFTP credentials if needed).

---

## 4. Playlists — The Heart of Your Station

A **playlist** is a group of songs with rules about *when* and *how often* they play. You will use a mix of playlist types:

| Playlist Type | Use it for |
|---|---|
| **General Rotation** | Default background music that plays whenever nothing else is scheduled (24/7 filler) |
| **Once Per X Songs** | Adverts/jingles inserted every few songs (see Section 8) |
| **Scheduled** | A specific program that should play only at a specific day/time (see Section 6) |

### Creating a Playlist

1. Go to **Media → Playlists → ADD PLAYLIST**.
2. Give it a clear name, e.g. `Worship Rotation`, `Sunday Service Replay`, `Adverts`.
3. Choose the **Type**:
   - **General Rotation** — plays automatically whenever the schedule is free.
   - **Scheduled** — only plays during the day/time window you set (a calendar picker will appear).
   - **Once per X Songs** — inserts itself periodically inside other rotations (used for ads/jingles).
4. Set the **Weight** — if you have multiple General Rotation playlists, a higher weight means it plays more often relative to the others.
5. Click **Save**.

### Adding Songs to a Playlist

1. Go to **Media → Music Files**.
2. Tick the checkbox next to the song(s) you want to add.
3. Click **PLAYLISTS** (top toolbar) → select the playlist(s) to add them to.
4. A song can belong to multiple playlists at once (e.g. a worship song can be in both "Worship Rotation" and a "Sunday Special" scheduled playlist).

### Reordering / Shuffling

- By default playlists shuffle randomly. To force a specific play order, open the playlist → **MORE** → **Reshuffle off** and drag songs into order (only works well for small playlists).

---

## 5. Running a Live Program (Web DJ)

This lets Pastor Robert (or anyone on the media team) speak live over the air, with AzuraCast automatically switching back to AutoDJ when he's done.

### One-Time Setup (Media Team / Admin)

1. Go to **Streamers / DJs** in the sidebar.
2. Click **ADD STREAMER**.
3. Set a **username** and **password** (e.g. `pastor_robert` / a strong password) — this is the live broadcaster's login, separate from the admin dashboard login.
4. Save.

### Going Live (Pastor / Presenter)

**Option A — Web DJ (easiest, no software needed):**
1. From the station's **Overview** page, find the **Streamers/DJs** panel and click **WEB DJ**.
2. Log in with the streamer username/password created above.
3. Allow microphone access when the browser asks.
4. Click the on-air button in the Web DJ tool to go live.
5. AzuraCast automatically detects the live connection and switches the public stream to the live mic feed.
6. Click stop when finished — the station automatically falls back to AutoDJ playlists.

**Option B — Broadcasting software (for mixing music + mic, more professional):**
1. Install a free tool like **BUTT** (Broadcast Using This Tool) or **Mixxx** on a laptop.
2. Go to **Broadcasting** in the sidebar → note the **Source** username and password (e.g. `source` / a generated password).
3. In the software, enter:
   - Server: `radio.roberttalemwa.online`
   - Port: `8000` (the source port shown in Broadcasting)
   - Username: `source`
   - Password: (from Broadcasting page)
   - Mount point: usually `/radio.mp3` (check the exact mount shown in your station's Broadcasting page)
4. Press connect/go live in the software.
5. Disconnect when done — AutoDJ resumes automatically.

> ⚠️ Only one live source can broadcast at a time. If a second person tries to connect while someone is already live, AzuraCast will reject the second connection.

---

## 6. Scheduling Programs (Weekly Timetable)

Use this so specific programs (e.g. "Sunday Service Replay," "Wednesday Bible Study") automatically play at the right time without anyone manually starting them.

1. Go to **Media → Playlists → ADD PLAYLIST**.
2. Set Type to **Scheduled**.
3. A scheduling panel appears — choose:
   - Day(s) of the week
   - Start time and end time
   - Whether it repeats weekly
4. Add the relevant audio files to this playlist (same method as Section 4).
5. Save — AzuraCast will automatically switch to this playlist during its scheduled window, then return to General Rotation afterward.

**Example schedule for Miracles Now Radio:**

| Program | Day | Time |
|---|---|---|
| Morning Devotion | Daily | 6:00 AM – 6:30 AM |
| Sunday Service Replay | Sunday | 10:00 AM – 12:00 PM |
| Bible Study | Wednesday | 7:00 PM – 8:00 PM |
| Prayer Hour | Friday | 8:00 PM – 9:00 PM |
| Worship Rotation (default filler) | All other times | General Rotation |

> 📌 To see your current schedule at a glance, go to **Media → Playlists → Schedule View** tab.

---

## 7. Background Music Between Talks

This is simply your **General Rotation** playlist(s) — they fill all the "empty" time when no live broadcast or scheduled program is active.

Recommendations:
- Create a playlist called `Worship Background` (General Rotation type) and load it with instrumental worship/gospel tracks.
- Keep at least 15–20 songs in it so it doesn't feel repetitive — AzuraCast will shuffle them.
- If you want sermon audio mixed into the rotation too (not just music), you can add sermon MP3s to the same playlist, or create a separate `Sermon Replays` playlist and give both playlists a **Weight** to balance how often each type plays (e.g. Music weight 5, Sermons weight 2 → music plays more often, sermons occasionally).

---

## 8. Running Adverts / Announcements

Use the **"Once per X Songs"** playlist type for short ads, sponsor messages, or station IDs ("You're listening to Miracles Now Radio...").

1. Record/prepare a short audio clip (5–30 seconds is typical).
2. Go to **Media → Music Files** and upload it.
3. Go to **Media → Playlists → ADD PLAYLIST**.
4. Name it e.g. `Station ID` or `Adverts`.
5. Set **Type** = **Once per X Songs**.
6. Set the number, e.g. `5` — this means the ad/jingle plays after every 5 songs from other rotations.
7. Add your ad clip(s) to this playlist.
8. Save.

This works automatically in the background — no manual triggering needed, and it also plays correctly during scheduled programs and General Rotation alike.

> 💡 For sponsor/paid adverts in future, you can create multiple "Once per X Songs" playlists with different intervals (e.g. Ad Set A every 6 songs, Ad Set B every 10 songs) to control how often each runs.

---

## 9. Making Programs Visible on the Website & App

The Talemwa website and mobile app pull two separate things from AzuraCast automatically — you don't need to manually update the site itself:

1. **"Now Playing"** — the title/artist currently airing (live or AutoDJ) shows automatically on `roberttalemwa.online/radio` and the sticky player bar across the site.
2. **Weekly Schedule** — this is **not** pulled from AzuraCast directly. It comes from a separate schedule table managed in the **Talemwa Admin Dashboard** (`admin.roberttalemwa.online` → Radio → Schedule), which mirrors what you've set up in AzuraCast.

**Important:** Whenever you add or change a scheduled program in AzuraCast (Section 6), also update the same entry in `admin.roberttalemwa.online` → Radio → Schedule, so the website's "Programme Schedule" section shown to visitors stays accurate. These two systems are independent — AzuraCast controls what actually plays; the admin dashboard controls what's *displayed* to website/app visitors as the published timetable.

---

## 10. Volume, Normalization & Sound Quality

If some songs sound much louder/quieter than others:

1. Go to your station's **Profile** (gear icon) → **Audio Processing** settings.
2. Enable **"Enable Replaygain Metadata"** / loudness normalization if available in your AzuraCast version — this automatically evens out volume differences between tracks.
3. For uploaded files, AzuraCast can also auto-analyze loudness on upload (check Profile settings for this toggle).

If a single specific file is too loud/quiet, it's best to fix the audio file itself before uploading (e.g. using free software like Audacity to normalize it) rather than relying solely on AzuraCast.

---

## 11. Monitoring Listeners

- **Overview** page → top "On the Air" panel shows current listener count in real time.
- **Reports** (sidebar) → see listener history over time, peak listener counts, geographic breakdown (which countries are tuning in — useful for tracking diaspora reach), and song play history.

---

## 12. Starting / Stopping / Restarting the Station

- **Overview** page → if the station isn't running, you'll see a green **"Start Station"** box in the sidebar — click it.
- **Reload Configuration** (Edit Station Settings → Restart Broadcasting page) — use this after changing settings like AutoDJ behavior; it does **not** disconnect listeners.
- **Restart Broadcasting** — use only if something is stuck/broken; this briefly disconnects all listeners while services restart.

---

## 13. Troubleshooting

| Problem | Likely Cause | Fix |
|---|---|---|
| "Station Offline" shown, no sound | AutoDJ (Liquidsoap) not running | Go to Broadcasting → check AutoDJ Service status → click Start/Restart |
| Website player won't play, browser console shows 404 | Stream URL changed or wrong | Confirm the real stream URL via Overview → Streams section, then update `STREAM_URL` in the backend `.env` file |
| One person can't go live ("already broadcasting") | Someone else's live source is still connected | Check Streamers/DJs — disconnect any stuck/ghost session, or wait for it to time out |
| New playlist not playing at all | Playlist not enabled, or has no songs | Open the playlist → confirm songs are attached and it isn't disabled |
| Scheduled program played at wrong time | Server timezone mismatch | Check station Profile → Timezone setting matches East Africa Time (Africa/Kampala) |
| Audio sounds distorted/clipping | Source file volume too hot | Re-export the file at a lower gain, or enable loudness normalization (Section 10) |

---

## 14. Quick Reference Cheat Sheet

**To add a new song:**
Media → Music Files → Select File → wait for processing → add to a Playlist

**To create background rotation:**
Media → Playlists → Add Playlist → Type: General Rotation → add songs

**To schedule a weekly program:**
Media → Playlists → Add Playlist → Type: Scheduled → set day/time → add songs → *also update admin.roberttalemwa.online → Radio → Schedule*

**To insert ads/jingles automatically:**
Media → Playlists → Add Playlist → Type: Once per X Songs → set interval → add ad clips

**To go live (mic):**
Overview → Streamers/DJs panel → Web DJ → log in → go on air

**To check who's listening:**
Overview (live count) or Reports (history)

**To start/stop the whole station:**
Overview → green Start Station box, or Broadcasting → Stop/Restart

---

*Document prepared by Thirdsan Enterprises Ltd for Talemwa Ministry — keep this as a reference for the media team. For technical issues beyond this guide (server errors, DNS, SSL), contact your developer.*
