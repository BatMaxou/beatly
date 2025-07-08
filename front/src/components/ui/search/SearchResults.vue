<template>
  <div
    class="rounded-lg min-h-[200px] p-4 w-full transition-all duration-300 ease-in-out text-white"
  >
    <!-- Afficher les résultats uniquement si le champ est en focus -->
    <div>
      <!-- S'il y a des résultats -->
      <div v-if="searchResults.length > 0">
        <!-- Section meilleur résultat -->
        <div v-if="bestResult" class="mb-8">
          <h2
            class="text-xl font-semibold text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700"
          >
            Meilleur Résultat
          </h2>
          <SearchBestResult :result="bestResult" @click="selectResult(bestResult)" class="mb-2" />
        </div>

        <!-- Section titres (songs) -->
        <div v-if="songsResults.length > 0" class="mb-8">
          <h2
            class="text-xl font-semibold text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700"
          >
            Titres
          </h2>
          <MusicList
            v-if="songsResults.length > 0"
            :musicList="songsResults"
            :customStyles="{ musicList: { width: '100%' } }"
            origin="playlist"
            theme="light"
          />
        </div>

        <!-- Section albums -->
        <div v-if="albumsResults.length > 0" class="mb-8">
          <h2
            class="text-xl font-semibold text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700"
          >
            Albums
          </h2>
          <div
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7 gap-6 w-full"
          >
            <div
              v-for="result in albumsResults"
              :key="result.id"
              class="cursor-pointer flex flex-col h-full"
              @mousedown.prevent
              @click="selectResult(result)"
            >
              <AlbumPlayable
                :albumCover="result.cover"
                :albumName="result.title"
                :artistName="result.artist"
                :releaseYear="result.year"
                class="w-full h-full"
              />
            </div>
          </div>
        </div>

        <!-- Section artistes -->
        <div v-if="artistsResults.length > 0" class="mb-8">
          <h2
            class="text-xl font-semibold text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700"
          >
            Artistes
          </h2>
          <div
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7 gap-6 w-full"
          >
            <div
              v-for="result in artistsResults"
              :key="result.id"
              class="cursor-pointer flex flex-col items-center h-full"
              @mousedown.prevent
              @click="selectResult(result)"
            >
              <ArtistProfile
                :artistCover="result.cover"
                :artistName="result.title"
                class="w-full h-full"
              />
            </div>
          </div>
        </div>

        <!-- Autres types non catégorisés -->
        <div v-if="otherResults.length > 0" class="mb-8">
          <h2
            class="text-xl font-semibold text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700"
          >
            Autres
          </h2>
          <div
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-7 gap-6 w-full"
          >
            <div
              v-for="result in otherResults"
              :key="result.id"
              class="cursor-pointer flex flex-col h-full"
              @mousedown.prevent
              @click="selectResult(result)"
            >
              <div class="flex flex-col h-full">
                <div class="font-medium text-white text-lg mb-2">
                  {{ result.title }}
                </div>
                <div v-if="result.type" class="text-sm text-gray-500 dark:text-gray-400 mt-auto">
                  {{ result.type }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Si la recherche est active mais pas de résultats -->
      <div
        v-else-if="searchQuery"
        class="flex justify-center items-center h-[150px] text-gray-600 dark:text-gray-400 italic"
      >
        <p>Aucun résultat trouvé pour "{{ searchQuery }}"</p>
      </div>

      <!-- Si le champ est en focus mais pas de texte saisi -->
      <div
        v-else
        class="flex justify-center items-center h-[150px] text-blue-500 dark:text-blue-400 italic text-lg animate-fade-in"
      >
        <p>Lancez votre recherche</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useSearch } from "@/providers";
import MusicList from "@/components/lists/MusicList.vue";
import AlbumPlayable from "@/components/cards/AlbumPlayableCard.vue";
import ArtistProfile from "@/components/artists/ArtistProfile.vue";
import SearchBestResult from "@/components/cards/SearchBestResult.vue";

interface SearchResult {
  id: number;
  title: string;
  type: 'album' | 'artist' | 'song';
  cover?: string;
  artist?: string;
  year?: string;
  duration?: string;
}

// Récupérer le contexte de recherche
const { searchQuery, searchResults } = useSearch();

// Calculer le meilleur résultat (le premier de la liste)
const bestResult = computed(() => {
  return searchResults.value.length > 0 ? searchResults.value[0] : null;
});

// Filtrer les résultats par type
const songsResults = computed(() => {
  return searchResults.value.filter((result: SearchResult) => result.type === "song");
});

const albumsResults = computed(() => {
  return searchResults.value.filter((result: SearchResult) => result.type === "album");
});

const artistsResults = computed(() => {
  return searchResults.value.filter((result: SearchResult) => result.type === "artist");
});

const otherResults = computed(() => {
  return searchResults.value.filter(
    (result: SearchResult) => result.type !== "song" && result.type !== "album" && result.type !== "artist",
  );
});

// Fonction pour sélectionner un résultat
const selectResult = (result: SearchResult) => {
  // On pourrait ajouter ici la logique pour traiter le résultat sélectionné
  console.log("Résultat sélectionné:", result);

  // Empêcher la perte de focus lors du clic sur un résultat
  // Cette fonction est appelée après le mousedown.prevent qui empêche le blur
};
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease;
}

/* Styles spécifiques pour les composants imbriqués */
:deep(.album-informations),
:deep(.artist-informations) {
  @apply w-full text-center mt-2;
}

:deep(.album-informations h4),
:deep(.artist-informations h4) {
  @apply my-0.5 mx-0 text-base truncate text-black dark:text-white;
}

:deep(.album-informations p) {
  @apply m-0 text-sm text-gray-600 dark:text-gray-400 truncate;
}
</style>
