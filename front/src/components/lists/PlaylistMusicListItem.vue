<script setup lang="ts">
import { defineProps, defineEmits, ref, computed } from "vue";
import MusicPlayButton from "@/components/lists/MusicPlayButton.vue";
import PlaylistTitleMenu from "../menus/PlaylistTitleMenu.vue";
import defaultCover from "@/assets/images/default-cover.png";

const ressourceUrl = import.meta.env.VITE_API_RESSOURCES_URL;
const props = defineProps({
  music: {
    type: Object,
    required: true,
  },
  index: {
    type: Number,
    required: true,
  },
  isPlaying: {
    type: Boolean,
    default: false,
  },
  customStyles: {
    type: Object,
    default: () => ({}),
  },
  theme: {
    type: String,
    default: "dark",
  },
});

const emit = defineEmits(["toggle-play"]);

const musicInformations = computed(() => {
  return {
    title: props.music.music.title,
    cover: props.music.music.cover ? ressourceUrl + props.music.music.cover : defaultCover,
    position: props.music.position,
    albums: props.music.music.albums || [],
    artists: props.music.music.artists || [],
    id: props.music.music.id,
  };
});
const isHovered = ref(false);

const handleMouseEnter = () => {
  isHovered.value = true;
};

const handleMouseLeave = () => {
  isHovered.value = false;
};

const handleRowClick = () => {
  emit("toggle-play", props.music.position);
};

const textColor = computed(() => {
  return props.theme === "dark" ? "text-black" : "text-white";
});
</script>

<template>
  <div
    class="flex flex-row justify-between items-center h-14 px-4 transition-colors duration-200 ease-in-out cursor-pointer border-b border-gray-200/20 hover:bg-black/80 hover:text-white"
    :class="{ 'bg-black/80 text-white': isPlaying }"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
  >
    <div class="flex flex-row items-center w-full gap-2">
      <div class="relative flex items-center justify-center w-12 min-w-12">
        <img :src="musicInformations.cover" class="w-12 h-12 rounded" />
        <div
          v-if="isHovered"
          class="absolute inset-0 flex items-center justify-center bg-black/50 rounded"
        >
          <MusicPlayButton
            :musicId="musicInformations.position"
            :isPlaying="isPlaying"
            @toggle-play="$emit('toggle-play', musicInformations.position)"
          />
        </div>
      </div>
      <div class="flex flex-col">
        <span class="font-medium" :class="textColor">{{ musicInformations.title }}</span>
        <p class="font-medium" :class="textColor">
          <span
            v-for="artist in musicInformations.artists"
            :key="artist.id"
            class="text-white/70 text-sm"
          >
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
