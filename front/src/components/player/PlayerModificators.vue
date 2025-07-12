<script setup lang="ts">
import { ref, watch, computed } from "vue";
import volumeIcon from "@/assets/icons/volume-light.svg";
import volumeOffIcon from "@/assets/icons/volume-off-light.svg";
import { usePlayerStore } from "@/stores/player";

const playerStore = usePlayerStore();

// Fonctionnalités du volume
const volumeValue = ref(playerStore.volume);

watch(
  () => playerStore.volume,
  (newVolume) => {
    if (!playerStore.isVolumeInteraction) {
      volumeValue.value = newVolume;
    }
  },
);

watch(volumeValue, (newValue) => {
  if (playerStore.isVolumeInteraction) {
    playerStore.setVolume(newValue);
  }
});

const handleMute = () => {
  playerStore.setMuted(!playerStore.muted);
};

const volumeBackground = computed(() => {
  const percentage = volumeValue.value;
  const activeColor = playerStore.isVolumeInteraction ? "#ffffffcc" : "#ffffff";
  return `linear-gradient(90deg, ${activeColor} ${percentage}%, #ffffff2a ${percentage}%)`;
});
</script>

<template>
  <div v-if="!playerStore.muted" class="volume-controls flex items-center gap-2">
    <img :src="volumeIcon" alt="Volume Icon" class="w-8 h-8 cursor-pointer" @click="handleMute" />
    <input
      type="range"
      v-model="volumeValue"
      v-volume-range
      name="volumeRange"
      min="0"
      max="100"
      id="volumeInputRange"
      class="inputRange cursor-pointer"
      :style="{ background: volumeBackground }"
    />
  </div>
  <div v-else class="volume-controls">
    <img
      :src="volumeOffIcon"
      alt="Volume Icon"
      class="w-8 h-8 cursor-pointer"
      @click="handleMute"
    />
  </div>
</template>

<style scoped>
.volume-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transform: rotate(180deg);
}

.volume-controls input[type="range"] {
  width: 0px;
  transition: width 0.5s ease;
  appearance: none;
  -webkit-appearance: none;
  height: 12px;
  border-radius: 9999px;
  cursor: pointer;
}
.volume-controls input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 0px;
  height: 0px;
  border-radius: 50%;
  background: #ffffff;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.volume-controls input[type="range"]::-moz-range-thumb {
  appearance: none;
  width: 0px;
  height: 0px;
  border-radius: 50%;
  background: #ffffff;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.volume-controls:hover input[type="range"] {
  width: 100px;
  transition: width 0.5s ease;
}

.volume-controls:hover input[type="range"]::-webkit-slider-thumb {
  width: 0px;
  height: 0px;
}

.volume-controls:hover input[type="range"]::-moz-range-thumb {
  width: 0px;
  height: 0px;
}
</style>
