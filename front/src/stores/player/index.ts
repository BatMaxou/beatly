import type { Music } from '@/utils/types'
import { defineStore } from 'pinia'

export const usePlayerStore = defineStore('player', {
  state: () => ({
    currentMusic: null as Music | null,
    volume: 100 as number,
    muted: false as boolean,
    isPlay: false as boolean,
    isPlayerActive: false as boolean,
    audioPlayer: null as HTMLAudioElement | null,
    duration: 0 as number,
    isPlayerInteraction: false as boolean,
    isVolumeInteraction: false as boolean,
  }),
  actions: {
    setCurrentMusic(music: Music | null) {
      this.currentMusic = music
    },
    setVolume(volume: number) {
      this.volume = volume
    },
    setMuted(muted: boolean) {
      this.muted = muted
    },
    setIsPlay(isPlay: boolean) {
      this.isPlay = isPlay
    },
    setIsPlayerActive(isPlayerActive: boolean) {
      this.isPlayerActive = isPlayerActive
    },
    setAudioPlayer(audio: HTMLAudioElement | null) {
      this.audioPlayer = audio
    },
    setDuration(duration: number) {
      this.duration = duration
    },
    setIsPlayerInteraction(isPlayerInteraction: boolean) {
      this.isPlayerInteraction = isPlayerInteraction
    },
    setIsVolumeInteraction(isVolumeInteraction: boolean) {
      this.isVolumeInteraction = isVolumeInteraction
    },
  }
})
