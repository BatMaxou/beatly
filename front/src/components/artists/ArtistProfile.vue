<script setup>
import { defineProps } from "vue";

const props = defineProps({
  artistCover: {
    type: String,
    required: true,
  },
  artistName: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <div class="flex flex-wrap my-4 max-w-[160px]">
    <div class="relative w-full overflow-hidden">
      <img
        :src="artistCover"
        :alt="artistName"
        class="w-full block transition-transform duration-300 ease-in-out rounded-full"
      />
    </div>
    <div class="w-full mt-2">
      <h4>{{ artistName }}</h4>
    </div>
  </div>
</template>
