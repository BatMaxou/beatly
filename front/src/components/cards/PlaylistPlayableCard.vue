<script setup lang="ts">
import { defineProps } from "vue";
import PlaylistPlayableCover from "@/components/playlist/PlaylistPlayableCover.vue";

defineProps({
  playlistCover: {
    type: String,
    required: true,
  },
  playlistName: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <div class="flex flex-wrap max-w-[160px] my-4">
    <PlaylistPlayableCover :playlistCover="playlistCover" :playlistName="playlistName" />
    <div class="w-full mt-2">
      <h4 class="font-medium text-sm truncate">{{ playlistName }}</h4>
    </div>
  </div>
</template>
