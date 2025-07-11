<script setup lang="ts">
import { defineProps, ref } from "vue";
import MusicPlayButton from "@/components/lists/MusicPlayButton.vue";
import type { Music } from "@/utils/types";
import AlbumTitleMenu from "../menus/AlbumTitleMenu.vue";

const props = defineProps<{
  music: Music;
  index: number;
  isPlaying?: boolean;
  position: number;
}>();

const { isPlaying = false } = props;

const isHovered = ref(false);

const handleMouseEnter = () => {
  isHovered.value = true;
};

const handleMouseLeave = () => {
  isHovered.value = false;
};
</script>

<template>
  <div
    class="flex flex-row justify-between items-center h-10 px-4 transition-colors duration-200 ease-in-out cursor-pointer border-b border-gray-200/20 hover:bg-black/80 hover:text-white"
    :class="{ 'bg-black/80 text-white': isPlaying }"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
  >
    <div class="flex flex-row items-center gap-2">
      <div class="flex items-center justify-center w-8 min-w-8">
        <span v-if="!isHovered && !isPlaying" class="text-white font-medium">
          {{ index + 1 }}
        </span>
        <MusicPlayButton v-else :musicId="position" :isPlaying="isPlaying" />
      </div>
      <span class="text-white/30 mx-2"> | </span>
      <span class="font-medium">{{ music.title }}</span>
    </div>
    <div class="text-gray-500 font-normal">
      <AlbumTitleMenu position="bottom-right" />
    </div>
  </div>
</template>
