<script setup lang="ts">
import { defineProps, ref } from "vue";
import FastPlayButton from "../buttons/FastPlayButton.vue";
import type { Playlist } from "@/utils/types";

defineProps({
  playlist: {
    type: Object as () => Playlist | { title: string; "@id": string },
    required: true,
  },
  playlistCover: {
    type: String,
    required: true,
  },
  isPlayable: {
    type: Boolean,
    default: true,
  },
});
const isClickedToPlay = ref(false);

const handlePlaySong = () => {
  isClickedToPlay.value = true;
};

const handleResetClickedToPlay = () => {
  isClickedToPlay.value = false;
};
</script>

<template>
  <div class="relative w-full overflow-hidden group">
    <img
      :src="playlistCover"
      :alt="playlist.title"
      class="w-full block transition-transform duration-300 ease-in-out w-[160px] h-[160px] object-cover group-hover:scale-110"
    />
    <div v-if="isPlayable">
      <div
        class="absolute bottom-2 right-2 p-4 w-[50px] h-[50px] bg-black/80 rounded-full flex justify-center items-center opacity-0 transition-all duration-300 ease-in-out hover:bg-black group-hover:opacity-100"
        data-play-button
        @click="handlePlaySong"
      >
        <FastPlayButton
          origin="playlist"
          :parentId="playlist['@id']"
          :isClickedToPlay="isClickedToPlay"
          @update:isClickedToPlay="handleResetClickedToPlay"
        />
      </div>
    </div>
  </div>
</template>
