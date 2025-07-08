<script setup lang="ts">
import { watch, ref, computed } from 'vue'
import { usePlayer } from '@/composables/usePlayer'
import { usePlayerStore } from '@/stores/player'
import PlayerInformations from './PlayerInformations.vue';
import PlayerModificators from './PlayerModificators.vue';
import PlayerControls from './PlayerControls.vue';

const playerStore = usePlayerStore();
const isPlayerActive = computed(() => playerStore.isPlayerActive)

</script>


<template>
  <div :class="isPlayerActive ? 'w-full h-[80px] bg-black/40 text-white py-5 px-5 transition-all duration-300 ease' : 'h-0 transition-all duration-300 ease'">

    <div class="grid grid-cols-3 items-center">
      <div class="justify-self-start">
        <PlayerInformations/>
      </div>
      <div class="justify-self-center">
        <PlayerControls/>
      </div>
      <div class="justify-self-end">
        <PlayerModificators/>
      </div>
    </div>
  </div>
</template>
