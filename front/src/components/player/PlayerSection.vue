<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { usePlayerStore } from "@/stores/player";
import { useTimeFormat } from "@/composables/useTimeFormat";
import PlayerInformations from "./PlayerInformations.vue";
import PlayerModificators from "./PlayerModificators.vue";
import PlayerControls from "./PlayerControls.vue";

const playerStore = usePlayerStore();
const isPlayerActive = computed(() => playerStore.isPlayerActive);
const playerElement = ref<HTMLAudioElement | null>(null);
const currentTimeValue = ref<number>(0);
const duration = computed(() => playerStore.duration);
const rangeValue = ref(0);
const pendingSeekTime = ref<number | null>(null);
const { formattedCurrentTime, formattedDuration } = useTimeFormat(currentTimeValue, duration);

const handleAudioTimeUpdate = (event: CustomEvent) => {
  if (!playerStore.isPlayerInteraction) {
    currentTimeValue.value = event.detail.currentTime;
  }
};

// Synchroniser le range avec le currentTime quand l'utilisateur n'interagit pas
watch(currentTimeValue, (newTime) => {
  if (!playerStore.isPlayerInteraction) {
    rangeValue.value = newTime;
  }
});

// Stocker la valeur pendant l'interaction, mais ne pas l'appliquer immédiatement
watch(rangeValue, (newValue) => {
  if (playerStore.isPlayerInteraction) {
    pendingSeekTime.value = newValue;
  }
});

// Mise à jour de l'audio à la fin de l'interaction
watch(
  () => playerStore.isPlayerInteraction,
  (isInteracting) => {
    if (!isInteracting && pendingSeekTime.value !== null) {
      if (playerStore.audioPlayer) {
        playerStore.audioPlayer.currentTime = pendingSeekTime.value;
        currentTimeValue.value = pendingSeekTime.value;
      }
      pendingSeekTime.value = null;
    }
  },
);

const rangeBackground = computed(() => {
  const percentage = duration.value > 0 ? (rangeValue.value / duration.value) * 100 : 0;
  const activeColor = playerStore.isPlayerInteraction ? "#ffffffcc" : "#ffffff";
  return `linear-gradient(90deg, ${activeColor} ${percentage}%, #ffffff2a ${percentage}%)`;
});

onMounted(() => {
  if (playerElement.value) {
    playerStore.setAudioPlayer(playerElement.value);
  }
});
</script>

<template>
  <div
    :class="
      isPlayerActive
        ? 'relative w-full h-[80px] bg-black/40 text-white py-2 px-5 transition-all duration-300 ease'
        : 'h-0 transition-all duration-300 ease'
    "
  >
    <div class="h-[64px] grid grid-cols-5 items-center">
      <div class="justify-self-start">
        <PlayerInformations />
      </div>
      <div class="justify-self-center col-span-3">
        <audio
          ref="playerElement"
          v-audio
          class="hidden pointer-events-none"
          src="https://cdn.freesound.org/previews/815/815526_5674468-lq.mp3"
          controls
          preload="auto"
          @audio-timeupdate="handleAudioTimeUpdate"
        />
        <PlayerControls />
        <div class="w-full flex flex-row justify-center items-center gap-4 pt-2">
          <span id="currentTime" class="text-xs text-white/80 font-mono min-w-[3rem] text-right">{{
            formattedCurrentTime
          }}</span>
          <input
            type="range"
            v-model="rangeValue"
            name="audioProgress"
            min="0"
            :max="playerStore.duration || 100"
            step="0.01"
            id="audioProgressRange"
            class="lg:min-w-[450px] md:min-w-[250px] h-1.5 rounded-full cursor-pointer appearance-none border-none outline-none inputRange"
            :style="{ background: rangeBackground }"
            :aria-label="`Progression audio: ${formattedCurrentTime} sur ${formattedDuration}`"
          />
          <span class="text-xs text-white/80 font-mono min-w-[3rem] text-left">{{
            formattedDuration
          }}</span>
        </div>
      </div>
      <div class="justify-self-end">
        <PlayerModificators />
      </div>
    </div>
  </div>
</template>

<style scoped>
.inputRange::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 0px;
  height: 0px;
  border-radius: 50%;
  background: transparent;
  cursor: pointer;
  transition: all 0.2s ease;
}

.inputRange::-moz-range-thumb {
  appearance: none;
  width: 0px;
  height: 0px;
  border: none;
  border-radius: 50%;
  background: transparent;
  cursor: pointer;
  transition: all 0.2s ease;
}
</style>
