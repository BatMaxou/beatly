<script setup lang="ts">
  const { url } = defineProps<{
    url: string;
  }>();
</script>

<template>
  <figure>
    <audio controls :src="url"></audio>
  </figure>
</template>
