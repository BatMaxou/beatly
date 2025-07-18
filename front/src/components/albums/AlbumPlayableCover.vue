<script setup lang="ts">
import { defineProps, watch, ref, computed } from "vue";
import { usePlayerStore } from "@/stores/player";
import type { Album, Music } from "@/utils/types";
import FastPlayButton from "../buttons/FastPlayButton.vue";
import loadingIcon from "@/assets/icons/loading-light.svg";

const props = defineProps({
  album: {
    type: Object as () => Album,
    required: true,
  },
  albumCover: {
    type: String,
    required: true,
  },
  isPlayable: {
    type: Boolean,
    default: true,
  },
  type: {
    type: String,
    default: "album",
  },
  music: {
    type: Object as () => Music,
    default: null,
  },
});

const playerStore = usePlayerStore();
const isClickedToPlay = ref(false);
const isLoading = ref(true);

const isCurrentParent = computed(() => {
  if (props.type === "single" && props.music) {
    return playerStore.currentMusic?.id === props.music.id;
  } else {
    return playerStore.queueParent === props.album["@id"];
  }
});

const shouldShowButton = computed(() => {
  return isClickedToPlay.value || isCurrentParent.value;
});

const handlePlaySong = () => {
  isClickedToPlay.value = true;
};

const handleResetClickedToPlay = () => {
  isClickedToPlay.value = false;
};

watch(
  () => props.album,
  () => {
    if (props.album) {
      isLoading.value = false;
    }
  },
  { immediate: true },
);
</script>

<template>
  <div class="relative w-full overflow-hidden group">
    <img
      :src="albumCover"
      :alt="album?.title"
      class="block transition-transform duration-300 ease-in-out w-[160px] h-[160px] object-cover group-hover:scale-110"
    />
    <div v-if="isPlayable">
      <div
        class="absolute bottom-2 right-2 p-4 w-[50px] h-[50px] bg-black/80 rounded-full flex justify-center items-center transition-all duration-300 ease-in-out hover:bg-black"
        :class="shouldShowButton ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
        data-play-button
        @click="handlePlaySong"
        v-if="!isLoading"
      >
        <FastPlayButton
          v-if="type === 'single' && music"
          :musicId="album?.id"
          origin="album"
          :parentId="album['@id']"
          :albumPosition="music.albumPosition"
          class="w-6 h-6 lg:w-6 lg:h-6"
          :isClickedToPlay="isClickedToPlay"
          @update:isClickedToPlay="handleResetClickedToPlay"
        />
        <FastPlayButton
          v-else
          :musicId="album?.id"
          origin="album"
          :parentId="album['@id']"
          class="w-6 h-6 lg:w-6 lg:h-6"
          :isClickedToPlay="isClickedToPlay"
          @update:isClickedToPlay="handleResetClickedToPlay"
        />
      </div>

      <div v-else class="absolute inset-0 flex flex-col items-center justify-center">
        <img :src="loadingIcon" alt="Chargement" class="h-12 w-12 animate-spin mb-4" />
      </div>
    </div>
  </div>
</template>
