<script setup lang="ts">
import { defineProps, computed } from "vue";
import MusicListItem from "@/components/lists/MusicListItem.vue";
import PlaylistMusicListItem from "@/components/lists/PlaylistMusicListItem.vue";
import type { PlaylistMusic, AlbumMusic, Music } from "@/utils/types";

const props = defineProps({
  musicList: {
    type: Array as () => PlaylistMusic[] | AlbumMusic[] | Music[],
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

const musicList = props.musicList;

const extractedMusics = computed(() => {
  if (props.origin === "top-titles") {
    return musicList as { music: Music }[];
  }
  return null;
});

const albumMusics = computed(() => {
  if (props.origin === "album") {
    return musicList as AlbumMusic[];
  }
  return [];
});

const playlistMusics = computed(() => {
  if (props.origin === "playlist") {
    return musicList as PlaylistMusic[];
  }
  return [];
});

const topTitlesMusics = computed(() => {
  if (props.origin === "top-titles") {
    return musicList as PlaylistMusic[];
  }
  return [];
});

console.log(musicList);
console.log(topTitlesMusics);
// Quand la clé popularity sera en place, si l'origin est top-titles, on trie par popularité
// Quand le clé playlistIndex sera en place, si l'origin est playlist, on trie par index de la playlist
</script>

<template>
  <div
    v-if="albumMusics.length > 0 && origin === 'album'"
    class="flex flex-col"
    :style="props.customStyles.musicList"
  >
    <MusicListItem
      v-for="(music, index) in albumMusics"
      :key="music.position"
      :music="music.music"
      :position="music.position"
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
      :parentId="parentId"
      :origin="origin"
      :musics="extractedMusics"
      :customStyles="props.customStyles"
      :theme="props.theme"
    />
  </div>

  <div
    v-if="musicList.length === 0"
    class="flex flex-col items-center justify-center py-8 text-gray-500 dark:text-gray-400"
    :style="props.customStyles.emptyMusicList"
  >
    <p>Aucune musique disponible</p>
  </div>
</template>
