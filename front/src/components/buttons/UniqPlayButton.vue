<script setup lang="ts">
import { ref } from "vue";
import playLight from "@/assets/icons/play-light.svg";
import { usePlayer } from "@/composables/usePlayer";

const isRotating = ref(false);
const { setListen } = usePlayer();

const handleClick = () => {
  isRotating.value = true;
  setListen({ id: 1, title: "Je suis à toi", artists: [{ id: 1, name: "Lomepal" }], cover: "https://placehold.co/80x80/png", categories: [{ id: 7, name: "Hip-Hop" }, { id: 9, name: "Rap" }], listeningsNumber: 1000 });

  // fetch (à implémenter plus tard)
  // fetch('/api/play', { method: 'POST' })
  //   .then(response => response.json())
  //   .then(data => {
  //     console.log('Success:', data);
  //   })
  //   .catch(error => {
  //     console.error('Error:', error);
  //   });

  setTimeout(() => {
    isRotating.value = false;
  }, 1000);
};
</script>

<template>
  <div class="flex justify-center items-center cursor-pointer" @click="handleClick">
    <img
      :src="playLight"
      alt="Play"
      class="w-20 h-20 box-content transition-transform duration-1000 ease-in-out"
      :class="{ 'animate-spin-once': isRotating }"
    />
  </div>
</template>

<style scoped>
@keyframes spin-once {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin-once {
  animation: spin-once 1s ease;
}
</style>
