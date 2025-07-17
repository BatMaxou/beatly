<script setup lang="ts">
import { defineProps, computed } from "vue";
import MusicListItem from "@/components/lists/MusicListItem.vue";
import PlaylistMusicListItem from "@/components/lists/PlaylistMusicListItem.vue";
import type { PlaylistMusic, Music, Favorite } from "@/utils/types";

const props = defineProps({
  musicList: {
    type: Array as () => PlaylistMusic[] | Music[] | Favorite<Music>[],
    required: true,
  },
  parentId: {
    type: String || null,
    default: null,
  },
  customStyles: {
    type: Object,
    default: () => ({}),
  },
  origin: {
    type: String,
    required: true,
  },
  theme: {
    type: String,
    default: "dark",
  },
});

const extractedMusics = computed(() => {
  return null;
});

const albumMusics = computed(() => {
  if (props.origin === "album") {
    return props.musicList as Music[];
  }
  return [];
});

const playlistMusics = computed(() => {
  if (props.origin === "playlist") {
    return props.musicList as PlaylistMusic[];
  }
  return [];
});

const topTitlesMusics = computed(() => {
  if (props.origin === "top-titles") {
    return props.musicList as PlaylistMusic[];
  }
  return [];
});

const favoriteMusics = computed(() => {
  if (props.origin === "favorites") {
    const favorites = props.musicList as Favorite<Music>[];
    const sorted = favorites.sort(
      (a, b) => new Date(b.addedAt).getTime() - new Date(a.addedAt).getTime(),
    );
    return sorted;
  }
  return [];
});
</script>

<template>
  <div
    v-if="albumMusics.length > 0 && origin === 'album'"
    class="flex flex-col"
    :style="props.customStyles.musicList"
  >
    <MusicListItem
      v-for="(music, index) in albumMusics"
      :key="music.albumPosition"
      :music="music"
      :position="music.albumPosition || index + 1"
      :index="index"
      :parentId="parentId"
      :origin="origin"
      :customStyles="props.customStyles"
      :theme="props.theme"
    />
  </div>

  <div
    v-if="playlistMusics.length > 0 && origin === 'playlist'"
    class="flex flex-col"
    :style="props.customStyles.musicList"
  >
    <PlaylistMusicListItem
      v-for="(music, index) in playlistMusics"
      :key="music.id"
      :music="music.music"
      :index="index"
      :position="index + 1"
      :parentId="parentId"
      :origin="origin"
      :musics="null"
      :customStyles="props.customStyles"
      :theme="props.theme"
    />
  </div>

  <div
    v-if="topTitlesMusics.length > 0 && origin === 'top-titles'"
    class="flex flex-col"
    :style="props.customStyles.musicList"
  >
    <PlaylistMusicListItem
      v-for="(music, index) in topTitlesMusics"
      :key="music.id"
      :music="music.music"
      :index="index"
      :position="index + 1"
      :origin="origin"
      :musics="extractedMusics"
      :customStyles="props.customStyles"
      :theme="props.theme"
    />
  </div>

  <div
    v-if="favoriteMusics.length > 0 && origin === 'favorites'"
    class="flex flex-col"
    :style="props.customStyles.musicList"
  >
    <PlaylistMusicListItem
      v-for="(music, index) in favoriteMusics"
      :key="music.target.id"
      :music="music.target"
      :index="index"
      :position="index + 1"
      :origin="origin"
      :musics="extractedMusics"
      :customStyles="props.customStyles"
      :theme="props.theme"
    />
  </div>

  <div
    v-if="props.musicList.length === 0"
    class="flex flex-col items-center justify-center py-8 text-gray-500 dark:text-gray-400"
    :style="props.customStyles.emptyMusicList"
  >
    <p>Aucune musique disponible</p>
  </div>
</template>
