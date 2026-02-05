<script setup lang="ts">
import { computed } from "vue";
import { usePlayerStore } from "@/stores/player";
import defaultCover from "@/assets/images/default-cover.png";
import { ressourceUrl } from "@/utils/tools";

const playerStore = usePlayerStore();

const currentMusic = computed(
  () =>
    playerStore.currentMusic || {
      id: 0,
      cover: "",
      title: "Aucune musique",
      artists: [],
      categories: [],
      file: "",
      listeningsNumber: 0,
      mainArtist: {
        name: "",
      },
    },
);
</script>

<template>
  <div class="flex flex-row items-center justify-start gap-4">
    <img
      :src="currentMusic.cover ? ressourceUrl + currentMusic.cover : defaultCover"
      class="w-[40px] h-[40px]"
    />
    <div class="flex flex-col">
      <span class="text-md font-500">{{ currentMusic.title }}</span>
      <span class="text-sm text-gray-300">{{
        currentMusic.mainArtist.name || currentMusic.artists.map((artist) => artist.name).join(", ")
      }}</span>
    </div>
  </div>
</template>
