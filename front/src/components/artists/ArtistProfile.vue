<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  artistCover: {
    type: String,
    required: true
  },
  artistName: {
    type: String,
    required: true
  }
});
</script>

<template>
  <div class="artist-row">
    <div class="artist-container">
      <img :src="artistCover" :alt="artistName" class="artist-cover">
    </div>
    <div class="artist-informations">
      <h4>{{ artistName }}</h4>
    </div>
  </div>
</template>

<style scoped>
.artist-row {
  display: flex;
  flex-wrap: wrap;
  gap: .5rem;
  margin: 1rem 0;
  max-width: 160px;
}

.artist-container {
  position: relative;
  width: 100%;
  overflow: hidden;
}

.artist-cover {
  width: 100%;
  display: block;
  transition: transform 0.3s ease;
  border-radius: 100%;
}
</style>
