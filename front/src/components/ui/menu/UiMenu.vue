<template>
  <div class="w-full p-4 transition-all duration-300 ease-in-out">
    <h2 class="text-white text-xl font-bold dark:text-white flex items-center">Menus</h2>
    <div class="flex flex-row flex-wrap justify-start gap-20 p-4 mb-8">
      <div class="mb-60">
        <h3 class="text-lg font-semibold mb-2">Menu titre playlist</h3>
        <div class="flex flex-row gap-4">
          <UnifiedMenu type="playlistTitle" :isFavorite="true" />
        </div>
      </div>
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Menu playlist</h3>
        <UnifiedMenu type="playlist" />
      </div>
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Menu album</h3>
        <UnifiedMenu type="album" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// @ts-expect-error Aucune idée de pourquoi ts le détecte en module
import UnifiedMenu from "../../menus/UnifiedMenu.vue";
</script>
