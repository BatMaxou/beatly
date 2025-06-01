<script setup>
import { defineProps, defineEmits, ref, computed } from 'vue';
import { convertDurationInMinutes } from '@/sharedFunctions.vue';
import MusicPlayButton from '@/components/lists/MusicPlayButton.vue';

const props = defineProps({
  music: {
    type: Object,
    required: true
  },
  index: {
    type: Number,
    required: true
  },
  isPlaying: {
    type: Boolean,
    default: false
  },
  customStyles: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['toggle-play']);

const isHovered = ref(false);

const formattedDuration = computed(() => {
  return convertDurationInMinutes(props.music.duration);
});

const handleMouseEnter = () => {
  isHovered.value = true;
};

const handleMouseLeave = () => {
  isHovered.value = false;
};

const handleRowClick = () => {
  emit('toggle-play', props.music.position);
};
</script>

<template>
  <div 
    class="music-element" 
    :class="{ 'playing': isPlaying }"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
    @click="handleRowClick"
    :style="customStyles.musicElement"
  >
    <div class="music-header" :style="customStyles.musicHeader">
      <div class="position-container" :style="customStyles.positionContainer">
        <span v-if="!isHovered && !isPlaying" class="position-number" :style="customStyles.positionNumber">
          {{ index + 1 }}
        </span>
        <MusicPlayButton 
          v-else
          :musicId="music.position"
          :isPlaying="isPlaying"
          @toggle-play="$emit('toggle-play', music.position)"
        />
      </div>
      <span :style="customStyles.dividerSpan"> | </span>
      <span :style="customStyles.titleSpan">{{ music.title }}</span>
    </div>
    <div class="music-duration" :style="customStyles.musicDuration">
      <h4 :style="customStyles.durationText">{{ formattedDuration }}</h4>
    </div>
  </div>
</template>

<style scoped>
.music-header {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0.5rem;
}

.music-element {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  height: 2.5rem;
  padding: 0 1rem;
  transition: .2s ease;
  cursor: pointer;
}

.music-element:hover {
  background-color: #000000cc;
  transition: .2s ease;
  color: white;
}

.playing {
  background-color: rgba(0, 0, 0, 0.8);
  color: white;
}

.position-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  text-align: end;
}
</style>
