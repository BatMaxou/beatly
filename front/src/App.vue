<script setup lang="ts">
import { onBeforeUnmount, onMounted } from "vue";
import AudioPlayer from "./components/audio/AudioPlayer.vue";
import { usePlayerStore } from "./stores/player";

const playerStore = usePlayerStore();
const handleBeforeUnload = () => {
  playerStore.clearQueue();
};

onMounted(() => {
  window.addEventListener("beforeunload", handleBeforeUnload);
});

onBeforeUnmount(() => {
  window.removeEventListener("beforeunload", handleBeforeUnload);
});
</script>

<template>
  <AudioPlayer />

  <router-view />
</template>
