<template>
  <!-- Un provider n'a pas besoin de contenu visible -->
  <slot></slot>
</template>

<script setup lang="ts">
import { ref, provide, inject } from 'vue';
import { SearchSymbol } from './searchHooks';

// État de recherche
const searchQuery = ref('');
const searchResults = ref([]);
const isSearchFocused = ref(false); // Variable pour suivre l'état du focus

// Mise a jour du texte de recherche
const updateSearchQuery = (query) => {
  searchQuery.value = query;

  // Ajouter le fetch des résultats quand le lien sera fait
  if (query) {
    searchResults.value = [
    {
          id: 1,
          title: `Album ${query} 1`,
          type: 'album',
          cover: '/src/assets/music/cover1.jpg',
          artist: 'Artiste A',
          year: '2023'
        },
        {
          id: 2,
          title: `Artiste ${query}`,
          type: 'artist',
          cover: '/src/assets/music/artist1.jpeg'
        },
        {
          id: 3,
          title: `Chanson ${query} 1`,
          type: 'song',
          artist: 'Artiste B',
          duration: '140',
          cover: '/src/assets/music/cover1.jpg',
        },
        {
          id: 4,
          title: `Album ${query} 2`,
          type: 'album',
          cover: '/src/assets/music/cover1.jpg',
          artist: 'Artiste C',
          year: '2022'
        },
        {
          id: 5,
          title: `Chanson ${query} 2`,
          type: 'song',
          artist: 'Artiste D',
          duration: '162',
          cover: '/src/assets/music/cover1.jpg',
        },
        {
          id: 6,
          title: `Album ${query} 3`,
          type: 'album',
          cover: '/src/assets/music/cover1.jpg',
          artist: 'Artiste E',
          year: '2024'
        },
        {
          id: 7,
          title: `Artiste ${query} 2`,
          type: 'artist',
          cover: '/src/assets/music/artist1.jpeg'
        },
        {
          id: 8,
          title: `Chanson ${query} 3`,
          type: 'song',
          artist: 'Artiste F',
          duration: '180',
          cover: '/src/assets/music/cover1.jpg',
        }
    ];
  } else {
    searchResults.value = [];
  }
};

// Fonctions pour gérer le focus du champ de recherche
const setSearchFocus = (isFocused) => {
  isSearchFocused.value = isFocused;
};

// Exposition des valeurs et méthodes
provide(SearchSymbol, {
  searchQuery,
  searchResults,
  isSearchFocused,
  updateSearchQuery,
  setSearchFocus
});
</script>
