<script setup lang="ts">
import { defineProps, ref } from "vue";
import MusicListItem from "@/components/lists/MusicListItem.vue";
import PlaylistMusicListItem from "@/components/lists/PlaylistMusicListItem.vue";

interface Music {
  id: number;
  title: string;
  cover: string;
  position: number;
  [key: string]: string | number | undefined;
}

const props = defineProps({
  musicList: {
    type: Array as () => Music[],
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
const orderedMusicList = [...musicList].sort((a: Music, b: Music) => {
  const positionA = a.position ?? 0;
  const positionB = b.position ?? 0;
  return positionA - positionB;
});

const currentPlayingId = ref<number | null>(null);

const togglePlay = (musicId: number) => {
  if (currentPlayingId.value === musicId) {
    currentPlayingId.value = null;
  } else {
    currentPlayingId.value = musicId;
  }
};
</script>

<template>
  <div
    v-if="orderedMusicList.length > 0 && origin === 'album'"
    class="flex flex-col"
    :style="props.customStyles.musicList"
  >
    <MusicListItem
      v-for="(music, index) in orderedMusicList"
      :key="music.position"
      :music="music"
      :index="index"
      :isPlaying="currentPlayingId === music.position"
      :customStyles="props.customStyles"
      :theme="props.theme"
      @toggle-play="togglePlay"
    />
  </div>
  <div v-if="orderedMusicList.length > 0 && origin === 'playlist'" class="flex flex-col" :style="props.customStyles.musicList">
    <PlaylistMusicListItem
      v-for="(music, index) in orderedMusicList"
      :key="music.id"
      :music="music"
      :index="index"
      :isPlaying="currentPlayingId === music.id"
      :customStyles="props.customStyles"
      :theme="props.theme"
      @toggle-play="togglePlay"
    />
  </div>

  <div
    v-if="orderedMusicList.length === 0"
    class="flex flex-col items-center justify-center py-8 text-gray-500 dark:text-gray-400"
    :style="props.customStyles.emptyMusicList"
  >
    <p>Aucune musique disponible</p>
  </div>
</template>
