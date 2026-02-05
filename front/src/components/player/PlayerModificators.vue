<script setup lang="ts">
import { ref, watch, computed } from "vue";
import { usePlayerStore } from "@/stores/player";
import randomIcon from "@/assets/icons/random-light.svg";
import volumeIcon from "@/assets/icons/volume-light.svg";
import volumeOffIcon from "@/assets/icons/volume-off-light.svg";
import queueIcon from "@/assets/icons/queue-light.svg";
import repeatIcon from "@/assets/icons/repeat-light.svg";

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

const handleShuffle = () => {
  playerStore.setIsRandomQueue(!playerStore.isRandomQueue);
};

const handleRepeat = async () => {
  await playerStore.setIsRepeatQueue(!playerStore.isRepeatQueue);
};

const volumeBackground = computed(() => {
  const percentage = volumeValue.value;
  const activeColor = playerStore.isVolumeInteraction ? "#ffffffcc" : "#ffffff";
  return `linear-gradient(90deg, ${activeColor} ${percentage}%, #ffffff2a ${percentage}%)`;
});
</script>

<template>
  <div class="flex items-center gap-2">
    <!-- Contrôle de volume avec position relative pour contenir l'overlay -->
    <div class="relative volume-container">
      <button
        @click="handleMute"
        class="volume-button transition-colors p-2 hover:bg-[#440a50] rounded-full"
        title="Volume"
      >
        <img
          :src="playerStore.muted ? volumeOffIcon : volumeIcon"
          alt="Volume Icon"
          class="w-6 h-6 cursor-pointer"
        />
      </button>

      <!-- Range de volume qui apparaît au-dessus en absolue -->
      <div
        v-if="!playerStore.muted"
        class="absolute bottom-full left-1/2 transform min-h-[6px] p-3 mb-12 -rotate-90 origin-center -translate-x-1/2 bg-[#2E0B40] rounded-lg shadow-lg opacity-0 invisible hover:opacity-100 hover:visible volume-overlay transition-all duration-300"
      >
        <input
          type="range"
          v-model="volumeValue"
          v-volume-range
          name="volumeRange"
          min="0"
          max="100"
          id="volumeInputRange"
          class="volume-range-vertical"
          :style="{ background: volumeBackground }"
          orientation="vertical"
        />
      </div>
    </div>

    <!-- Bouton shuffle -->
    <div>
      <button
        @click="handleShuffle"
        class="transition-colors p-2 hover:bg-[#440a50] rounded-full"
        title="Lecture aléatoire"
      >
        <img
          :src="randomIcon"
          alt="Random Icon"
          :class="
            playerStore.isRandomQueue
              ? 'w-6 h-6 cursor-pointer opacity-100'
              : 'w-6 h-6 cursor-pointer opacity-50'
          "
        />
      </button>
    </div>
    <!-- Bouton repeat -->
    <div>
      <button
        @click="handleRepeat"
        class="transition-colors p-2 hover:bg-[#440a50] rounded-full"
        title="Lecture en boucle"
      >
        <img
          :src="repeatIcon"
          alt="Random Icon"
          :class="
            playerStore.isRepeatQueue
              ? 'w-6 h-6 cursor-pointer opacity-100'
              : 'w-6 h-6 cursor-pointer opacity-50'
          "
        />
      </button>
    </div>

    <!-- Bouton file d'attente -->
    <div>
      <button
        @click="playerStore.setShowQueue(!playerStore.showQueue)"
        class="transition-colors p-2 hover:bg-[#440a50] rounded-full"
        :class="{ 'bg-[#440a50]': playerStore.showQueue }"
        title="File d'attente"
      >
        <img :src="queueIcon" alt="Queue Icon" class="w-6 h-6 cursor-pointer" />
      </button>
    </div>
  </div>
</template>

<style scoped>
.volume-container:hover .volume-overlay {
  opacity: 1 !important;
  visibility: visible !important;
}

.volume-container:hover .volume-button {
  background-color: #440a50;
}

.volume-range-vertical {
  display: block;
  width: 100px;
  height: 6px;
  appearance: none;
  -webkit-appearance: none;
  border-radius: 9999px;
  cursor: pointer;
}

.volume-range-vertical::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 0px;
  height: 0px;
  border-radius: 50%;
  background: #ffffff;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.volume-range-vertical::-moz-range-thumb {
  appearance: none;
  width: 0px;
  height: 0px;
  border: none;
  border-radius: 50%;
  background: #ffffff;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
</style>
