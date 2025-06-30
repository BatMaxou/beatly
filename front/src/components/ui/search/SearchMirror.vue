<template>
  <div
    class="p-4 h-full min-h-[50px] flex flex-col justify-center w-full transition-colors duration-300 ease-in-out"
    :class="{ 'bg-gray-100 dark:bg-gray-800 border-l-3 border-blue-500': isSearchFocused }"
  >
    <div v-if="searchQuery" class="text-center">
      <div class="font-bold text-xl my-1">{{ searchQuery }}</div>
      <div class="text-gray-600 dark:text-gray-400 text-xs mt-2 italic">
        {{ isSearchFocused ? "En cours de saisie" : "En attente" }}
      </div>
    </div>
    <div v-else class="text-gray-500 dark:text-gray-400 italic text-center">
      {{ isSearchFocused ? "Saisissez votre recherche..." : "Aucune recherche active" }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { useSearch } from "@/providers";

// Récupérer le contexte de recherche
const { searchQuery, isSearchFocused } = useSearch();
</script>
