<script setup lang="ts">
import { computed } from "vue";
import { usePlayerStore } from '@/stores/player'

const playerStore = usePlayerStore();

const currentMusic = computed(() => playerStore.currentMusic || { 
  id: 0, 
  cover: '', 
  title: 'Aucune musique', 
  artists: [], 
  categories: [], 
  file: '', 
  listeningsNumber: 0 
})

</script>

<template>
  <div class="flex flex-row items-center justify-start gap-4">
    <img :src="currentMusic.cover ? currentMusic.cover : ''" class="w-[40px] h-[40px]"/>
    <div class="flex flex-col">
      <span class="text-md font-500">{{ currentMusic.title }}</span>
      <span class="text-sm text-gray-300">{{ currentMusic.artists.map(artist => artist.name).join(', ') }}</span>
    </div>
  </div>
</template>