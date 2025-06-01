<script setup>
import { ref } from 'vue';
import playLight from '@/assets/icons/play-light.svg';

const isRotating = ref(false);

const handleClick = () => {
  isRotating.value = true;
  
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
  <div class="play-button-container" @click="handleClick">
    <img 
      :src="playLight" 
      alt="Play" 
      class="play-icon"
      :class="{ 'rotating': isRotating }"
    >
  </div>
</template>

<style scoped>
.play-button-container {
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

.play-icon {
  width: 80px;
  height: 80px;
  padding: 4rem;
  box-sizing: content-box;
  transition: transform 1s ease;
}

.rotating {
  animation: rotate 1s ease;
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
