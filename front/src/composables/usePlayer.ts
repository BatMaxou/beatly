import { usePlayerStore } from "@/stores/player";
import type { Music } from "@/utils/types";


export function usePlayer() {
  const playerStore = usePlayerStore();


  const setListen = (music: Music) => {
    playerStore.setIsPlayerActive(true);
    playerStore.setCurrentMusic(music);

    setPlay();
  };

  const setMute = (muted: boolean) => {
    // Récupération de l'élément HTML audio depuis le store pour lui passer
    // les actions
    playerStore.setMuted(muted);
  };

  const setVolume = (volume: number) => {
    // Récupération de l'élément HTML audio depuis le store pour lui passer
    // les actions
    const isMuted = playerStore.muted;
    if (isMuted) {
      playerStore.setMuted(false);
    }
    playerStore.setVolume(volume);
  };

  const setAudioPlayer = (audio: HTMLAudioElement) => {
    // Récupération de l'élément HTML audio depuis le store pour lui passer
    // les actions
  }

  const setPause = () => {
    // Récupération de l'élément HTML audio depuis le store pour lui passer les actions
    playerStore.setIsPlay(false);
  };

  const setPlay = () => {
    // Récupération de l'élément HTML audio depuis le store pour lui passer les actions
    playerStore.setIsPlay(true);
  };

  const playNextSong = () => {
  };

  const playPreviousSong = () => {
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
