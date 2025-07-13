<script setup lang="ts">
import { defineProps, ref } from "vue";
import UniqPlayPauseButton from "../buttons/UniqPlayButton.vue";
import type { Playlist } from "@/utils/types";

defineProps({
  playlist: {
    type: Object as () => Playlist,
    required: true,
  },
  playlistId: {
    type: Number,
    required: true,
  },
  playlistCover: {
    type: String,
    required: true,
  },
  playlistName: {
    type: String,
    required: true,
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
      :alt="playlistName"
      class="w-full block transition-transform duration-300 ease-in-out w-[160px] h-[160px] object-cover group-hover:scale-110"
    />
    <div
      class="absolute bottom-2 right-2 p-4 w-[50px] h-[50px] bg-black/80 rounded-full flex justify-center items-center opacity-0 transition-all duration-300 ease-in-out hover:bg-black group-hover:opacity-100"
      data-play-button
      @click="handlePlaySong"
    >
      <UniqPlayPauseButton
        origin="playlist"
        :elementId="playlistId"
        :isClickedToPlay="isClickedToPlay"
        @update:isClickedToPlay="handleResetClickedToPlay"
      />
    </div>
  </div>
</template>
