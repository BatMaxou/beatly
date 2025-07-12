<script setup lang="ts">
import { defineProps, ref } from "vue";
import MusicListItem from "@/components/lists/MusicListItem.vue";
import PlaylistMusicListItem from "@/components/lists/PlaylistMusicListItem.vue";
import type { PlaylistMusic, AlbumMusic } from "@/utils/types";

const props = defineProps({
  musicList: {
    type: Array as () => PlaylistMusic[] | AlbumMusic[],
    required: true,
  },
  customStyles: {
    type: Object,
    default: () => ({}),
  },
  origin: {
    type: String,
    default: null,
  },
  theme: {
    type: String,
    default: "dark",
  },
});

const musicList = props.musicList;
// Quand la clé popularity sera en place, si l'origin est top-titles, on trie par popularité
// Quand le clé playlistIndex sera en place, si l'origin est playlist, on trie par index de la playlist
</script>

<template>
  <div
    v-if="musicList.length > 0 && origin === 'album'"
    class="flex flex-col"
    :style="props.customStyles.musicList"
  >
    <MusicListItem
      v-for="(music, index) in musicList"
      :key="music?.position"
      :music="music.music"
      :position="music.position"
      :index="index"
      :customStyles="props.customStyles"
      :theme="props.theme"
    />
  </div>
  <div
    v-if="musicList.length > 0 && (origin === 'playlist' || origin === 'top-titles')"
    class="flex flex-col"
    :style="props.customStyles.musicList"
  >
    <PlaylistMusicListItem
      v-for="(music, index) in musicList"
      :key="music.id"
      :music="music"
      :index="index"
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
