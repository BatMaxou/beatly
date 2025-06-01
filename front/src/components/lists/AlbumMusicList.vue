<script setup>
import { defineProps, ref } from 'vue';
import MusicListItem from '@/components/lists/MusicListItem.vue';

const props = defineProps({
  musicList: {
    type: Array,
    required: true
  },
  customStyles: {
    type: Object,
    default: () => ({})
  }
});

const musicList = props.musicList;
let orderedMusicList = musicList.sort((a, b) => a.position - b.position);

const currentPlayingId = ref(null);

const togglePlay = (musicId) => {
  if (currentPlayingId.value === musicId) {
    currentPlayingId.value = null;
  } else {
    currentPlayingId.value = musicId;
  }
};
</script>

<template>
  <div class="music-list" :style="props.customStyles.musicList">
    <MusicListItem
      v-for="(music, index) in orderedMusicList"
      :key="music.position"
      :music="music"
      :index="index"
      :isPlaying="currentPlayingId === music.position"
      :customStyles="props.customStyles"
      @toggle-play="togglePlay"
    />
  </div>
</template>

<style scoped>
.music-list {
  display: flex;
  flex-direction: column;
}
</style>
