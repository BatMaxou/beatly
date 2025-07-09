import { usePlayerStore } from "@/stores/player";
import type { Music } from "@/utils/types";


export function usePlayer() {
  const playerStore = usePlayerStore();

  const setAudioPlayer = (audio: HTMLAudioElement | null) => {
    playerStore.setAudioPlayer(audio);
  }

  const setListen = (music: Music) => {
    playerStore.setIsPlayerActive(true);
    playerStore.setCurrentMusic(music);
    setPlay();
  };

  const setMute = (muted: boolean) => {
    playerStore.setMuted(muted);
    if (playerStore.audioPlayer) {
      playerStore.audioPlayer.volume = muted ? 0 : playerStore.volume / 100;
    }
  };

  const setVolume = (volume: number) => {
    const isMuted = playerStore.muted;
    if (isMuted) {
      playerStore.setMuted(false);
    }
    playerStore.setVolume(volume);
    if (playerStore.audioPlayer) {
      playerStore.audioPlayer.volume = volume / 100;
    }
  };

  const setPause = () => {
    playerStore.audioPlayer?.pause();
    playerStore.setIsPlay(false);
  };

  const setPlay = () => {
    playerStore.audioPlayer?.play();
    playerStore.setIsPlay(true);
  };

  const playNextSong = () => {
  };

  const playPreviousSong = () => {
  };

  const setCurrentTime = (time: number) => {
    playerStore.audioPlayer!.currentTime = time;
  };

  return {
    setListen,
    setMute,
    setVolume,
    setAudioPlayer,
    setPause,
    setPlay,
    playNextSong,
    playPreviousSong
  };
}
