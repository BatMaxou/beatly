<script setup>
import { defineProps } from 'vue';
import PlayPauseButton from '@/components/ui/buttons/UniqPlayButton.vue';

const props = defineProps({
  albumCover: {
    type: String,
    required: true
  },
  albumName: {
    type: String,
    required: true
  },
  artistName: {
    type: String,
    required: true
  },
  releaseYear: {
    type: String,
    required: true
  }
});
</script>

<template>
  <div class="album-row">
    <div class="album-container">
      <img :src="albumCover" :alt="albumName" class="album-cover">
      <div class="album-overlay">
        <PlayPauseButton />
      </div>
    </div>
    <div class="album-informations">
      <h4>{{ albumName }}</h4>
      <p>{{ artistName }} &middot; {{ releaseYear }}</p>
    </div>
  </div>
</template>

<style scoped>
.ui-music-showcase {
  display: flex; 
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: flex-start;
  gap: 5rem;
  padding: 1rem;
}

.album-section {
  margin-bottom: 2rem;
}

.album-row, .artist-row {
  display: flex;
  flex-wrap: wrap;
  gap: .5rem;
  margin: 1rem 0;
  max-width: 160px;
}

.album-container, .artist-container {
  position: relative;
  width: 100%;
  overflow: hidden;
}

.album-cover, .artist-cover {
  width: 100%;
  display: block;
  transition: transform 0.3s ease;
}

.artist-cover {
  border-radius: 100%;
}

.album-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.play-icon {
  width: 30px;
  height: 30px;
  padding: 4rem;
  box-sizing: content-box;
}

.album-container:hover .album-overlay {
  opacity: 1;
}
</style>
