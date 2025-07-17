<script setup lang="ts">
import { computed, defineProps } from "vue";
import { useRouter } from "vue-router";
import AlbumPlayableCover from "@/components/albums/AlbumPlayableCover.vue";
import defaultCover from "@/assets/images/default-cover.png";
import type { Album, Music } from "@/utils/types";

const props = defineProps({
  album: {
    type: Object as () => Album,
    default: null,
  },
  music: {
    type: Object as () => Music,
    default: null,
  },
  type: {
    type: String,
    default: "album",
  },
  releaseYear: {
    type: Number,
    default: null,
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
const ressourceUrl = import.meta.env.VITE_API_RESSOURCES_URL;

const handleCardClick = (event: Event) => {
  // Vérifier si le clic provient du bouton de lecture
  const target = event.target as HTMLElement;
  const isPlayButton = target.closest("[data-play-button]");

  if (!isPlayButton) {
    if (props.music) {
      router.push({ name: "Album", params: { id: props.music.album.id.toString() } });
    } else {
      router.push({ name: "Album", params: { id: props.album.id.toString() } });
    }
  }
};

const musicCover = computed(() => {
  if (props.music.album.cover && props.music.album.cover.startsWith("/uploads")) {
    return ressourceUrl + props.music.album.cover;
  } else if (props.music.album.cover) {
    return props.music.album.cover;
  } else {
    return defaultCover;
  }
});

const albumCover = computed(() => {
  if (props.album.cover && props.album.cover.startsWith("/uploads")) {
    return ressourceUrl + props.album.cover;
  } else if (props.album.cover) {
    return props.album.cover;
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
    <AlbumPlayableCover
      v-if="album"
      :albumCover="albumCover"
      :album="album"
      :isPlayable="isPlayable"
      :type="type"
    />
    <AlbumPlayableCover
      v-else-if="music"
      :albumCover="musicCover"
      :album="music.album"
      :isPlayable="isPlayable"
      :type="type"
      :music="music"
    />
    <div class="w-full mt-2">
      <h4 class="font-medium text-sm truncate text-bold">
        {{ album ? album.title : music.title }}
      </h4>
      <p class="text-xs text-white dark:text-gray-400 truncate">
        {{ artistName }}
        {{
          album
            ? `&middot; ${new Date(album.releaseDate).getFullYear()}`
            : music
              ? `&middot; ${music.album.title}`
              : ""
        }}
      </p>
    </div>
  </div>
</template>
