import 'package:audio_service/audio_service.dart';
import 'package:just_audio/just_audio.dart';
import '../models/sermon.dart';

class TalemwaAudioHandler extends BaseAudioHandler with QueueHandler, SeekHandler {
  final AudioPlayer _player = AudioPlayer();

  TalemwaAudioHandler() {
    _player.playbackEventStream.map(_transformEvent).pipe(playbackState);
    _player.currentIndexStream.listen((_) => _broadcastState());
  }

  Future<void> playSermon(Sermon sermon) async {
    final url = sermon.mp3Url;
    if (url == null) return;

    mediaItem.add(MediaItem(
      id:       url,
      title:    sermon.title,
      artist:   sermon.speaker,
      artUri:   sermon.thumbnailUrl != null ? Uri.parse(sermon.thumbnailUrl!) : null,
      duration: sermon.durationSeconds != null
          ? Duration(seconds: sermon.durationSeconds!)
          : null,
      extras: {'sermon_id': sermon.id},
    ));

    await _player.setUrl(url);
    play();
  }

  Future<void> playRadio(String streamUrl, {String title = 'Miracles Now Radio', String artist = ''}) async {
    mediaItem.add(MediaItem(
      id:     streamUrl,
      title:  title,
      artist: artist,
      extras: {'is_radio': true},
    ));
    await _player.setUrl(streamUrl);
    play();
  }

  @override Future<void> play()          => _player.play();
  @override Future<void> pause()         => _player.pause();
  @override Future<void> stop()          async { await _player.stop(); await super.stop(); }
  @override Future<void> seek(Duration p) => _player.seek(p);

  @override
  Future<void> skipToNext() async {
    await _player.seekToNext();
  }

  @override
  Future<void> skipToPrevious() async {
    await _player.seekToPrevious();
  }

  @override
  Future<void> setSpeed(double speed) async {
    await _player.setSpeed(speed);
    super.setSpeed(speed);
  }

  void _broadcastState() {
    final item = mediaItem.value;
    if (item != null) mediaItem.add(item);
  }

  PlaybackState _transformEvent(PlaybackEvent event) {
    final playing = _player.playing;
    return PlaybackState(
      controls: [
        MediaControl.skipToPrevious,
        playing ? MediaControl.pause : MediaControl.play,
        MediaControl.skipToNext,
        MediaControl.stop,
      ],
      systemActions: const {
        MediaAction.seek,
        MediaAction.seekForward,
        MediaAction.seekBackward,
      },
      androidCompactActionIndices: const [0, 1, 2],
      processingState: const {
        ProcessingState.idle:        AudioProcessingState.idle,
        ProcessingState.loading:     AudioProcessingState.loading,
        ProcessingState.buffering:   AudioProcessingState.buffering,
        ProcessingState.ready:       AudioProcessingState.ready,
        ProcessingState.completed:   AudioProcessingState.completed,
      }[_player.processingState]!,
      playing:  playing,
      updatePosition: _player.position,
      bufferedPosition: _player.bufferedPosition,
      speed:    _player.speed,
      queueIndex: event.currentIndex,
    );
  }

  AudioPlayer get player => _player;
}

TalemwaAudioHandler? _globalHandler;

Future<TalemwaAudioHandler> initAudioService() async {
  _globalHandler = await AudioService.init(
    builder: () => TalemwaAudioHandler(),
    config: const AudioServiceConfig(
      androidNotificationChannelId:   'com.thirdsan.talemwa.audio',
      androidNotificationChannelName: 'Talemwa Audio',
      androidNotificationOngoing:     true,
      androidStopForegroundOnPause:   true,
    ),
  );
  return _globalHandler!;
}

TalemwaAudioHandler get audioHandler {
  assert(_globalHandler != null, 'Call initAudioService() first');
  return _globalHandler!;
}
