<script setup lang="ts">
import searchIcon from "@/assets/icons/search-dark.svg";
import SearchResults from "./SearchResults.vue";
import SearchMirror from "./SearchMirror.vue";
import { useSearch } from "@/providers";

// Récupérer le contexte de recherche
const { searchQuery, updateSearchQuery, setSearchFocus, isSearchFocused } = useSearch();

const clearSearch = () => {
  updateSearchQuery("");
};

const handleFocus = () => {
  setSearchFocus(true);
};

const handleBlur = () => {
  setSearchFocus(false);
};
</script>

<template>
  <div class="w-full p-4 transition-all duration-300 ease-in-out">
    <h2 class="text-white text-xl font-bold dark:text-white flex items-center">
      Recherche
      <span
        v-if="isSearchFocused"
        class="inline-block bg-blue-500 text-white text-xs py-0.5 px-2 rounded-full ml-2 font-normal animate-pulse"
      >
        Mode saisie
      </span>
    </h2>

    <div class="flex flex-row gap-8 my-6">
      <div
        class="flex-1 flex items-center transition-all duration-300 ease-in-out"
        :class="{ 'transform scale-102': isSearchFocused }"
      >
        <div class="relative w-full">
          <input
            type="text"
            v-model="searchQuery"
            @input="updateSearchQuery(searchQuery)"
            @focus="handleFocus"
            @blur="handleBlur"
            placeholder="Trouvez votre vibe..."
            class="w-full py-3 px-4 pr-12 text-base border border-gray-300 dark:border-gray-700 rounded-lg outline-none transition-colors duration-200 ease-in-out dark:bg-gray-800 dark:text-white"
            :class="{ 'border-gray-600 dark:border-gray-500 shadow-sm': isSearchFocused }"
          />
          <div
            class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center justify-center w-6 h-6 pointer-events-auto"
          >
            <span
              v-if="searchQuery"
              @click="clearSearch"
              class="text-2xl text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer flex items-center justify-center w-6 h-6"
              title="Effacer"
            >
              ×
            </span>
            <img v-else class="w-5 h-5 opacity-60 dark:invert" alt="Rechercher" :src="searchIcon" />
          </div>
        </div>
      </div>

      <div
        class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-lg transition-all duration-300 ease-in-out"
      >
        <SearchMirror />
      </div>
    </div>

    <div class="mt-6 transition-all duration-300 ease-in-out">
      <SearchResults />
    </div>
  </div>
</template>
