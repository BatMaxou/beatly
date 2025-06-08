<script setup>
import { defineProps, defineEmits } from 'vue';
import playLight from '@/assets/icons/play-light.svg';
import pauseLight from '@/assets/icons/pause-light.svg';

const props = defineProps({
  isPlaying: {
    type: Boolean,
    default: false
  },
  musicId: {
    type: [Number, String],
    required: true
  }
});

const emit = defineEmits(['toggle-play']);

const handleTogglePlay = () => {
  emit('toggle-play', props.musicId);
};
</script>

<template>
  <div class="music-play-button" @click.stop="handleTogglePlay">
    <img 
      :src="isPlaying ? pauseLight : playLight" 
      :alt="isPlaying ? 'Pause' : 'Play'" 
      class="play-icon"
      :class="{ 'playing': isPlaying }"
    >
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
  width: 2rem;
  height: 2rem;
  transition: transform 0.3s ease;
}

.playing {
  opacity: 0.8;
}
</style>
