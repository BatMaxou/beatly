import type { Music } from "@/utils/types";
import { defineStore } from "pinia";

export const usePlayerStore = defineStore("player", {
  state: () => ({
    currentMusic: null as Music | null,
    musicFile: "" as string,
    volume: 50 as number,
    muted: false as boolean,
    isPlay: false as boolean,
    isPlayerActive: false as boolean,
    audioPlayer: null as HTMLAudioElement | null,
    duration: 0 as number,
    isPlayerInteraction: false as boolean,
    isVolumeInteraction: false as boolean,
  }),
  actions: {
    // Actions de stockage
    setCurrentMusic(music: Music | null) {
      this.currentMusic = music;
    },
    setMusicFile(music: string) {
      this.musicFile = music;
      if (this.audioPlayer) {
        this.audioPlayer.src = music || "";
      }
    },
    setIsPlay(isPlay: boolean) {
      this.isPlay = isPlay;
    },
    setIsPlayerActive(isPlayerActive: boolean) {
      this.isPlayerActive = isPlayerActive;
    },
    setAudioPlayer(audio: HTMLAudioElement | null) {
      this.audioPlayer = audio;
      this.setVolume(50);
    },
    setDuration(duration: number) {
      this.duration = duration;
    },
    setIsPlayerInteraction(isPlayerInteraction: boolean) {
      this.isPlayerInteraction = isPlayerInteraction;
    },
    setIsVolumeInteraction(isVolumeInteraction: boolean) {
      this.isVolumeInteraction = isVolumeInteraction;
    },

    // Actions de contrôle du lecteur
    setVolume(volume: number) {
      if (this.muted) {
        this.setMuted(false);
      }
      this.volume = volume / 100;
      if (this.audioPlayer) {
        this.audioPlayer.volume = volume / 100;
      }

      this.volume = volume;
    },
    setMuted(muted: boolean) {
      this.muted = muted;
      if (this.audioPlayer) {
        this.audioPlayer.volume = muted ? 0 : this.volume / 100;
      }
    },
    playNextSong() {},
    playPreviousSong() {},
    setPause() {
      this.audioPlayer?.pause();
      this.setIsPlay(false);
    },

    setPlay() {
      this.audioPlayer?.play();
      this.setIsPlay(true);
    },
    setListen(music: Music, musicFile: string) {
      this.setIsPlayerActive(true);
      this.setCurrentMusic(music);
      this.setMusicFile(musicFile);
      this.setPlay();
    },
    setChangeCurrentTime(number: number) {
      if (this.audioPlayer) {
        this.audioPlayer.currentTime = number;
      }
    },
  },
});
