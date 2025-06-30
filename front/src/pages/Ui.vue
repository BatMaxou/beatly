<template>
  <div class="max-w-[1400px] mx-auto p-8">
    <h1 class="text-2xl text-black font-bold mb-2">Bibliothèque de composants UI</h1>
    <p class="text-gray-600 mb-8">
      Cette page présente tous les composants UI disponibles dans l'application.
    </p>

    <UiNavigation
      :active-item="activeComponent"
      @selectedId="selectComponent"
      @selectedTitle="selectComponentTitle"
    />

    <div
      class="relative bg-[url('@/assets/images/dancing-concert-hands-up.jpg')] bg-cover bg-center rounded-lg shadow-md mt-4 overflow-hidden"
    >
      <div class="absolute inset-0 bg-black/60"></div>
      <div class="relative z-10 p-4">
        <component v-if="!isEmptyComponent" :is="currentComponent" />
        <div v-else class="text-center py-12 px-4 text-white">
          <p class="my-2 text-lg">Aucun composant trouvé pour "{{ activeComponentTitle }}"</p>
          <p class="text-sm text-gray-300">Nous travaillons à l'ajouter prochainement.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, provide } from "vue";
import UiNavigation from "../components/ui/UiNavigation.vue";
import UiButtons from "../components/ui/buttons/UiButtons.vue";
import UiForms from "../components/ui/forms/UiForms.vue";
import UiMusic from "../components/ui/music/UiMusic.vue";
import UiMenu from "../components/ui/menu/UiMenu.vue";
import UiSearch from "../components/ui/search/UiSearch.vue";

provide("isSearchProvided", true);

// Définition des statuts d'onglets
const activeComponent = ref("music");
const activeComponentTitle = ref("Musique");
const isEmptyComponent = ref(false);

// Changement d'onglets
const selectComponent = (componentId) => {
  console.log(componentId);
  isEmptyComponent.value = false;
  activeComponent.value = componentId;
};

const selectComponentTitle = (componentTitle) => {
  activeComponentTitle.value = componentTitle;
};

// Composant à afficher en fonction de la sélection
const currentComponent = computed(() => {
  switch (activeComponent.value) {
    case "buttons":
      activeComponentTitle.value = "Boutons";
      return UiButtons;
    case "forms":
      activeComponentTitle.value = "Formulaires";
      return UiForms;
    case "menus":
      activeComponentTitle.value = "Menus";
      return UiMenu;
    case "music":
      activeComponentTitle.value = "Musique";
      return UiMusic;
    case "search":
      activeComponentTitle.value = "Recherche";
      return UiSearch;
    default:
      isEmptyComponent.value = true;
      console.log(isEmptyComponent);
      return {};
  }
});
</script>
