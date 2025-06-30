<template>
  <div class="w-full p-4 transition-all duration-300 ease-in-out">
    <h2 class="text-white text-xl font-bold dark:text-white flex items-center">Boutons</h2>
    <div class="flex flex-col flex-wrap justify-start gap-20 p-4 mb-8">
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Ajout au favoris</h3>
        <div class="flex flex-row gap-4">
          <AddToFavoriteButton :isFavorite="true" />
          <AddToFavoriteButton :isFavorite="false" />
        </div>
      </div>
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Bouton d'écoute</h3>
        <div class="flex flex-row gap-4">
          <ListenPlaylistButton :isActive="true" />
          <ListenPlaylistButton :isActive="false" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AddToFavoriteButton from "../../buttons/AddToFavoriteButton.vue";
import ListenPlaylistButton from "../../buttons/ListenPlaylistButton.vue";
</script>
