<script setup lang="ts">
import { defineProps, defineEmits, ref, computed } from "vue";
import { convertDurationInMinutes } from "@/sharedFunctions.ts";
import MusicPlayButton from "@/components/lists/MusicPlayButton.vue";
import type { Music } from "@/utils/types";
import AlbumTitleMenu from "../menus/AlbumTitleMenu.vue";

const props = defineProps<{
  music: Music;
  index: number;
  isPlaying?: boolean;
  theme?: string;
  position: number;
}>();

const {
  isPlaying = false,
  theme = "dark"
} = props;

const emit = defineEmits(["toggle-play"]);

const isHovered = ref(false);

const handleMouseEnter = () => {
  isHovered.value = true;
};

const handleMouseLeave = () => {
  isHovered.value = false;
};

const textColor = computed(() => {
  return props.theme === "dark" ? "text-black" : "text-white";
});
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
        <MusicPlayButton
          v-else
          :musicId="position"
          :isPlaying="isPlaying"
        />
      </div>
      <span class="text-white/30 mx-2"> | </span>
      <span class="font-medium" :class="textColor">{{ music.title }}</span>
    </div>
    <div class="text-gray-500 font-normal">
      <AlbumTitleMenu position="bottom-right"/>
    </div>
  </div>
</template>
