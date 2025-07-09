<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { usePlayer } from '@/composables/usePlayer'
import { usePlayerStore } from '@/stores/player'
import { useTimeFormat } from '@/composables/useTimeFormat'
import { useAudioRange } from '@/composables/useAudioRange'
import PlayerInformations from './PlayerInformations.vue';
import PlayerModificators from './PlayerModificators.vue';
import PlayerControls from './PlayerControls.vue';

const playerStore = usePlayerStore();
const { setAudioPlayer } = usePlayer();
const isPlayerActive = computed(() => playerStore.isPlayerActive)
const playerElement = ref<HTMLAudioElement | null>(null);
const currentTimeValue = ref<number>(0);
const duration = computed(() => playerStore.duration);
const { formattedCurrentTime, formattedDuration } = useTimeFormat(currentTimeValue, duration);
const { rangeValue, rangeBackground } = useAudioRange(playerElement, currentTimeValue, duration);

const handleAudioTimeUpdate = (event: CustomEvent) => {
  if (!playerStore.isPlayerInteraction) {
    currentTimeValue.value = event.detail.currentTime;
  }
};

onMounted(() => {
  if (playerElement.value) {
    setAudioPlayer(playerElement.value)
  }
})

</script>


<template>
  <div :class="isPlayerActive ? 'relative w-full h-[80px] bg-black/40 text-white py-2 px-5 transition-all duration-300 ease' : 'h-0 transition-all duration-300 ease'">
    <div class="h-[64px] grid grid-cols-5 items-center">
      <div class="justify-self-start">
        <PlayerInformations/>
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
        <PlayerControls/>
        <div class="w-full flex flex-row justify-center items-center gap-4 pt-2">
          <span id="currentTime" class="text-xs text-white/80 font-mono min-w-[3rem] text-right">{{ formattedCurrentTime }}</span>
          <input 
            type="range" 
            v-model="rangeValue"
            name="audioProgress" 
            min="0" 
            :max="playerStore.duration || 100" 
            step="0.01"
            id="audioProgressRange" 
            class="inputRange"
            :style="{ background: rangeBackground }"
            :aria-label="`Progression audio: ${formattedCurrentTime} sur ${formattedDuration}`"
          />
          <span class="text-xs text-white/80 font-mono min-w-[3rem] text-left">{{ formattedDuration }}</span>
        </div>
      </div>
      <div class="justify-self-end">
        <PlayerModificators/>
      </div>
    </div>

  </div>
</template>

<style scoped>
.inputRange {
  width: 450px;
  height: 6px;
  border-radius: 9999px;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  border: none;
  outline: none;
}

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