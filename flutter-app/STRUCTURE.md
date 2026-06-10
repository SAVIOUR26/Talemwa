# Ministry App — Flutter Structure

lib/
├── main.dart
├── core/
│   ├── router.dart          # GoRouter — all named routes
│   ├── theme.dart           # AppTheme light/dark
│   └── constants.dart       # API url, colors, strings
│
├── models/
│   ├── sermon.dart
│   ├── event.dart
│   ├── live_status.dart
│   └── radio_status.dart
│
├── services/
│   ├── api_service.dart     # All HTTP calls (Dio)
│   ├── audio_service.dart   # just_audio + audio_service background playback
│   └── notification_service.dart  # Firebase FCM
│
├── providers/               # Riverpod providers
│   ├── sermon_provider.dart
│   ├── radio_provider.dart
│   ├── live_provider.dart
│   └── event_provider.dart
│
└── screens/
    ├── home/
    │   ├── home_screen.dart
    │   └── widgets/
    │       ├── live_banner.dart      # Red "We're Live" banner
    │       ├── featured_sermon.dart  # Latest sermon card
    │       └── radio_mini_player.dart
    │
    ├── live/
    │   └── live_screen.dart          # YouTube player + radio toggle
    │
    ├── sermons/
    │   ├── sermons_screen.dart       # Search + filter list
    │   ├── sermon_detail_screen.dart # Player + info
    │   └── widgets/
    │       ├── sermon_card.dart
    │       └── audio_player_bar.dart # Persistent bottom bar
    │
    ├── radio/
    │   └── radio_screen.dart         # Full-screen radio player
    │
    ├── events/
    │   ├── events_screen.dart
    │   └── event_detail_screen.dart
    │
    ├── give/
    │   └── give_screen.dart          # Giving types + Flutterwave WebView
    │
    ├── prayer/
    │   └── prayer_screen.dart        # Submit prayer request
    │
    └── settings/
        └── settings_screen.dart

# Key Design Decisions

## Audio
- just_audio handles both MP3 sermon playback and radio stream
- audio_service enables background playback with lock screen controls
- Persistent mini-player bar at bottom when sermon is playing

## Live Detection
- App polls /api/live every 60 seconds
- If is_live = true → show red LIVE banner on home
- Tapping banner → opens live_screen.dart with YouTube player
- FCM push also triggers immediately when pastor goes live

## Offline Support
- Hive caches sermon list locally
- Dio downloads MP3 to device storage for offline playback
- Cached images via cached_network_image

## Navigation
- GoRouter with bottom nav bar (Home, Sermons, Radio, Give, More)
- Deep links supported: ministry://sermon/123, ministry://live

## State
- Riverpod for all async data (clean, testable)
- No BLoC overhead for a project this size
