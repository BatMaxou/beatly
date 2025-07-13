<script setup lang="ts">
import { defineProps, ref, computed, watch } from "vue";
import MusicPlayButton from "@/components/lists/MusicPlayButton.vue";
import PlaylistTitleMenu from "../menus/PlaylistTitleMenu.vue";
import defaultCover from "@/assets/images/default-cover.png";
import { usePlayerStore } from "@/stores/player";
import type { Music } from "@/utils/types";

const ressourceUrl = import.meta.env.VITE_API_RESSOURCES_URL;
const playerStore = usePlayerStore();
const props = defineProps({
  music: {
    type: Object as () => Music,
    required: true,
  },
  index: {
    type: Number,
    required: true,
  },
  theme: {
    type: String,
    default: "dark",
  },
});
const isClickedToPlay = ref(false);
const isCurrentSongPlaying = ref(false);

const isHovered = ref(false);

const handleMouseEnter = () => {
  isHovered.value = true;
};

const handleMouseLeave = () => {
  isHovered.value = false;
};

const handlePlaySong = (event: Event) => {
  const target = event.target as HTMLElement;

  const isMenuClick = target.closest(".album-title-menu") || target.closest("[data-menu]");

  if (isMenuClick) {
    return;
  }

  isClickedToPlay.value = true;
};

const handlePlayStateChange = (newState: boolean) => {
  isClickedToPlay.value = newState;
};
watch(
  () => playerStore.currentMusic,
  (newVal: any, oldVal: any) => {
    if (newVal !== oldVal) {
      if (props.music.id === newVal.id) {
        isCurrentSongPlaying.value = true;
      } else {
        isCurrentSongPlaying.value = false;
      }
    }
  },
);
const textColor = computed(() => {
  return props.theme === "dark" ? "text-black" : "text-white";
});
</script>

<template>
  <div
    class="flex flex-row justify-between items-center h-14 px-4 transition-colors duration-200 ease-in-out cursor-pointer border-b border-gray-200/20 hover:bg-black/80 hover:text-white"
    :class="isCurrentSongPlaying ? 'bg-black/80 text-white' : ''"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
    @click="handlePlaySong"
  >
    <div class="flex flex-row items-center w-full gap-2">
      <div class="relative flex items-center justify-center w-12 min-w-12">
        <img
          :src="music.cover ? ressourceUrl + music.cover : defaultCover"
          class="w-12 h-12 rounded"
        />
        <div
          v-if="isHovered"
          class="absolute inset-0 flex items-center justify-center bg-black/50 rounded"
        >
          <MusicPlayButton
            :music="music"
            :isClickedToPlay="isClickedToPlay"
            @update:isClickedToPlay="handlePlayStateChange"
          />
        </div>
      </div>
      <div class="flex flex-col">
        <span class="font-medium" :class="textColor">{{ music.title }}</span>
        <p class="font-medium" :class="textColor">
          <span v-for="artist in music.artists" :key="artist.id" class="text-white/70 text-sm">
            {{ artist.name }}
          </span>
        </p>
      </div>
    </div>
    <div class="text-gray-500 font-normal">
      <PlaylistTitleMenu :isFavorite="false" position="bottom-right" />
    </div>
  </div>
</template>
