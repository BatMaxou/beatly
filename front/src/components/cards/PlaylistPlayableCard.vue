<script setup lang="ts">
import { computed, defineProps } from "vue";
import { useRouter } from "vue-router";
import PlaylistPlayableCover from "@/components/playlist/PlaylistPlayableCover.vue";
import defaultCover from "@/assets/images/default-cover.png";
import type { Playlist } from "@/utils/types";
import { ressourceUrl } from "@/utils/tools";

const props = defineProps({
  playlist: {
    type: Object as () =>
      | Playlist
      | { title: string; origin: string; "@id": string; cover: string },
    required: true,
  },
});
const router = useRouter();
const handleCardClick = (event: Event) => {
  // Vérifier si le clic provient du bouton de lecture
  const target = event.target as HTMLElement;
  const isPlayButton = target.closest("[data-play-button]");

  if (!isPlayButton) {
    if (props.playlist.title === "Titres likés") {
      router.push({ name: "PlaylistFavorite" });
      return;
    }
    const playlist = props.playlist as Playlist;
    if (playlist.id) {
      router.push({ name: "Playlist", params: { id: playlist.id.toString() } });
    }
  }
};
const playlistCover = computed(() => {
  if (props.playlist.cover && props.playlist.cover.startsWith("/uploads")) {
    return ressourceUrl + props.playlist.cover;
  } else if (props.playlist.cover) {
    return props.playlist.cover;
  } else {
    return defaultCover;
  }
});
</script>

<template>
  <div
    class="flex flex-wrap min-w-[160px] w-full max-w-[160px] my-4 cursor-pointer transition-transform duration-200"
    @click="handleCardClick"
  >
    <PlaylistPlayableCover :playlist="playlist" :playlistCover="playlistCover" />
    <div class="w-full mt-2">
      <h4 class="font-medium text-sm truncate">{{ playlist.title }}</h4>
    </div>
  </div>
</template>
