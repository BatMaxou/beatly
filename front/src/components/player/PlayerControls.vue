<script setup lang="ts">
import PlayIcon from "@/assets/icons/play-light.svg";
import PauseIcon from "@/assets/icons/pause-light.svg";
import NextIcon from "@/assets/icons/play-next-light.svg";
import PreviousIcon from "@/assets/icons/play-prev-light.svg";
import { usePlayerStore } from "@/stores/player";

const playerStore = usePlayerStore();
</script>

<template>
  <div class="flex items-center justify-center gap-4">
    <button @click="playerStore.playPreviousSong()">
      <img :src="PreviousIcon" alt="Previous" class="w-8 h-8 cursor-pointer" />
    </button>
    <button @click="playerStore.isPlay ? playerStore.setPause() : playerStore.setPlay()">
      <img
        :src="playerStore.isPlay ? PauseIcon : PlayIcon"
        alt="Play/Pause"
        class="w-7 h-7 cursor-pointer"
      />
    </button>
    <button @click="playerStore.playNextSong()">
      <img :src="NextIcon" alt="Next" class="w-8 h-8 cursor-pointer" />
    </button>
  </div>
</template>
