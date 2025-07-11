<script setup lang="ts">
import { defineProps } from "vue";
import playLight from "@/assets/icons/play-light.svg";
import pauseLight from "@/assets/icons/pause-light.svg";

defineProps({
  isPlaying: {
    type: Boolean,
    default: false,
  },
  musicId: {
    type: [Number, String],
    required: true,
  },
});
</script>

<template>
  <div class="music-play-button">
    <img
      :src="isPlaying ? pauseLight : playLight"
      :alt="isPlaying ? 'Pause' : 'Play'"
      class="play-icon"
      :class="{ playing: isPlaying }"
    />
  </div>
</template>

<style scoped>
.music-play-button {
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

.play-icon {
  width: 0.7rem;
  height: 0.7rem;
  transition: transform 0.3s ease;
}

.playing {
  opacity: 0.8;
}
</style>
