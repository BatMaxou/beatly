<script setup lang="ts">
import { defineProps } from "vue";
import AlbumPlayableCover from "@/components/albums/AlbumPlayableCover.vue";

defineProps({
  albumCover: {
    type: String,
    required: true,
  },
  albumName: {
    type: String,
    required: true,
  },
  artistName: {
    type: String,
    required: true,
  },
  releaseYear: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <div class="flex flex-wrap max-w-[160px] my-4">
    <AlbumPlayableCover :albumCover="albumCover" :albumName="albumName" />
    <div class="w-full mt-2">
      <h4 class="font-medium text-sm truncate">{{ albumName }}</h4>
      <p class="text-xs text-white dark:text-gray-400 truncate">
        {{ artistName }} &middot; {{ releaseYear }}
      </p>
    </div>
  </div>
</template>
