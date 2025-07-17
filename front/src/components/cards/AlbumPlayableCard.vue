<script setup lang="ts">
import { defineProps } from "vue";
import { useRouter } from "vue-router";
import AlbumPlayableCover from "@/components/albums/AlbumPlayableCover.vue";
import defaultCover from "@/assets/images/default-cover.png";
import type { Album } from "@/utils/types";

const props = defineProps({
  album: {
    type: Object as () => Album,
    required: true,
  },
  releaseYear: {
    type: Number,
    required: true,
  },
  isPlayable: {
    type: Boolean,
    default: true,
  },
  artistName: {
    type: String,
    default: "",
  },
});

const router = useRouter();

const handleCardClick = (event: Event) => {
  // Vérifier si le clic provient du bouton de lecture
  const target = event.target as HTMLElement;
  const isPlayButton = target.closest("[data-play-button]");

  if (!isPlayButton) {
    router.push({ name: "Album", params: { id: props.album.id.toString() } });
  }
};
</script>

<template>
  <div
    class="flex flex-wrap min-w-[160px] w-full max-w-[160px] my-4 cursor-pointer transition-transform duration-200"
    @click="handleCardClick"
  >
    <AlbumPlayableCover
      :albumCover="album.cover ? album.cover : defaultCover"
      :album="album"
      :isPlayable="isPlayable"
    />
    <div class="w-full mt-2">
      <h4 class="font-medium text-sm truncate">{{ album.title }}</h4>
      <p class="text-xs text-white dark:text-gray-400 truncate">
        {{ artistName }} &middot; {{ releaseYear }}
      </p>
    </div>
  </div>
</template>
