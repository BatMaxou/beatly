<script setup>
import { defineProps } from "vue";
import PlayPauseButton from "@/components/ui/buttons/UniqPlayButton.vue";

const props = defineProps({
  albumCover: {
    type: String,
    required: true,
  },
  albumName: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <div class="relative w-full overflow-hidden group">
    <img
      :src="albumCover"
      :alt="albumName"
      class="w-full block transition-transform duration-300 ease-in-out"
    />
    <div
      class="absolute inset-0 bg-black/50 flex justify-center items-center opacity-0 transition-opacity duration-300 ease-in-out group-hover:opacity-100"
    >
      <PlayPauseButton />
    </div>
  </div>
</template>
