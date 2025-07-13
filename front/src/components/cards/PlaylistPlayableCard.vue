<script setup lang="ts">
import { defineProps } from "vue";
import { useRouter } from "vue-router";
import PlaylistPlayableCover from "@/components/playlist/PlaylistPlayableCover.vue";
import type { Playlist } from "@/utils/types";

const props = defineProps({
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
const router = useRouter();

const handleCardClick = (event: Event) => {
  // Vérifier si le clic provient du bouton de lecture
  const target = event.target as HTMLElement;
  const isPlayButton = target.closest("[data-play-button]");

  if (!isPlayButton) {
    router.push({ name: "Playlist", params: { id: props.playlistId.toString() } });
  }
};
</script>

<template>
  <div
    class="flex flex-wrap min-w-[160px] w-full max-w-[160px] my-4 cursor-pointer transition-transform duration-200"
    @click="handleCardClick"
  >
    <PlaylistPlayableCover
      :playlist="playlist"
      :playlistCover="playlistCover"
      :playlistName="playlistName"
      :playlistId="playlistId"
    />
    <div class="w-full mt-2">
      <h4 class="font-medium text-sm truncate">{{ playlistName }}</h4>
    </div>
  </div>
</template>
