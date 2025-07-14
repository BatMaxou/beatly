import type { Music, Queue } from "@/utils/types";
import { defineStore } from "pinia";
import { useApiClient } from "../api-client";
import { usePlayerPreparation } from "@/composables/usePlayerPreparation";

export const usePlayerStore = defineStore("player", {
  state: () => ({
    currentMusic: null as Music | null,
    musicFile: "" as string,
    position: -1 as number,
    volume: 50 as number,
    muted: false as boolean,
    isPlay: false as boolean,
    isPlayerActive: false as boolean,
    audioPlayer: null as HTMLAudioElement | null,
    duration: 0 as number,
    currentTime: 0 as number,
    isPlayerInteraction: false as boolean,
    isVolumeInteraction: false as boolean,
    isRandomQueue: false as boolean,
    showQueue: false as boolean,
    queue: null as Queue | null,
    queueParent: null as string | null,
    queueFile: null as Array<{ file: string; musicId: number }> | null,
    randomQueue: null as Queue | null,
    nextMusic: null as Music | null,
    nextMusicFile: null as string | null,
    previousMusic: null as Music | null,
    previousMusicFile: null as string | null,
  }),
  actions: {
    // Actions de stockage
    setCurrentMusic(music: Music | null) {
      this.currentMusic = music;
    },
    setPosition(position: number) {
      this.position = position;
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
    setCurrentTime(currentTime: number) {
      this.currentTime = currentTime;
    },
    setIsPlayerInteraction(isPlayerInteraction: boolean) {
      this.isPlayerInteraction = isPlayerInteraction;
    },
    setIsVolumeInteraction(isVolumeInteraction: boolean) {
      this.isVolumeInteraction = isVolumeInteraction;
    },
    async setIsRandomQueue(isRandomQueue: boolean) {
      this.isRandomQueue = isRandomQueue;
      await this.clearRandomQueue();
      if (isRandomQueue) {
        const { loadRandomQueue } = usePlayerPreparation();
        await loadRandomQueue();
      }
    },
    setShowQueue(showQueue: boolean) {
      this.showQueue = showQueue;
    },
    setQueue(queue: Queue | null, parent: string) {
      this.queue = queue;
      this.queueParent = parent;
    },
    setRandomQueue(queue: Queue | null) {
      this.randomQueue = queue;
    },
    setNextMusic(nextMusic: Music | null) {
      this.nextMusic = nextMusic;
    },
    setNextMusicFile(nextMusicFile: string | null) {
      this.nextMusicFile = nextMusicFile;
    },
    setPreviousMusic(previousMusic: Music | null) {
      this.previousMusic = previousMusic;
    },
    setPreviousMusicFile(previousMusicFile: string | null) {
      this.previousMusicFile = previousMusicFile;
    },
    setQueueFile(files: Array<{ file: string; musicId: number }> | null) {
      this.queueFile = files;
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
    setPause() {
      this.audioPlayer?.pause();
      this.setIsPlay(false);
    },

    setPlay() {
      this.audioPlayer?.play();
      this.setIsPlay(true);
    },
    async setListen(music: Music, musicFile: string, position: number) {
      this.setIsPlayerActive(true);
      this.setCurrentMusic(music);
      this.setMusicFile(musicFile);
      this.setPosition(position);
      setTimeout(() => {
        this.setPlay();
      }, 100);
    },
    setChangeCurrentTime(number: number) {
      if (this.audioPlayer) {
        this.audioPlayer.currentTime = number;
      }
    },
    async clearRandomQueue() {
      const { apiClient } = useApiClient();
      const playerStore = usePlayerStore();
      const { storeAdjacentMusicInQueue } = usePlayerPreparation();

      const initialMusicPosition =
        playerStore.queue?.queueItems.find((item) => item.music.id === playerStore.currentMusic?.id)
          ?.position || 0;
      this.setPosition(initialMusicPosition);
      await apiClient.queue.clearRandom();
      this.randomQueue = null;
      await storeAdjacentMusicInQueue();
    },
    async clearQueue() {
      const { apiClient } = useApiClient();
      await apiClient.queue.reset();
      this.queue = null;
      this.queueParent = null;
    },
  },
});
