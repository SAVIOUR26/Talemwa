import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/sermon.dart';
import '../services/audio_service.dart';

class AudioState {
  final Sermon? currentSermon;
  final bool    isRadio;
  final bool    isPlaying;

  const AudioState({this.currentSermon, this.isRadio = false, this.isPlaying = false});

  AudioState copyWith({Sermon? currentSermon, bool? isRadio, bool? isPlaying}) =>
      AudioState(
        currentSermon: currentSermon ?? this.currentSermon,
        isRadio:       isRadio       ?? this.isRadio,
        isPlaying:     isPlaying     ?? this.isPlaying,
      );

  AudioState cleared() => const AudioState();
}

class AudioNotifier extends StateNotifier<AudioState> {
  AudioNotifier() : super(const AudioState()) {
    audioHandler.playbackState.listen((ps) {
      state = state.copyWith(isPlaying: ps.playing);
    });
  }

  Future<void> playSermon(Sermon sermon) async {
    await audioHandler.playSermon(sermon);
    state = AudioState(currentSermon: sermon, isRadio: false, isPlaying: true);
  }

  Future<void> playRadio(String streamUrl, {String title = 'Ministry Radio', String artist = ''}) async {
    await audioHandler.playRadio(streamUrl, title: title, artist: artist);
    state = AudioState(isRadio: true, isPlaying: true);
  }

  Future<void> togglePlay() async {
    if (state.isPlaying) {
      await audioHandler.pause();
    } else {
      await audioHandler.play();
    }
  }

  Future<void> stop() async {
    await audioHandler.stop();
    state = const AudioState();
  }

  Future<void> seek(Duration position) => audioHandler.seek(position);
  Future<void> setSpeed(double speed)  => audioHandler.setSpeed(speed);
}

final audioProvider =
    StateNotifierProvider<AudioNotifier, AudioState>((_) => AudioNotifier());
