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
        <component v-if="currentComponent" :is="currentComponent" />
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

provide("isSearchProvided", true);

// Définition des statuts d'onglets
const activeComponent = ref<string>("music");
const activeComponentTitle = ref<string>("Musique");
const isEmptyComponent = ref<boolean>(false);

// Changement d'onglets
const selectComponent = (componentId: string) => {
  isEmptyComponent.value = false;
  activeComponent.value = componentId;
  updateComponentTitle(componentId);
};

const selectComponentTitle = (componentTitle: string) => {
  activeComponentTitle.value = componentTitle;
};

const updateComponentTitle = (componentId: string) => {
  switch (componentId) {
    case "buttons":
      activeComponentTitle.value = "Boutons";
      break;
    case "forms":
      activeComponentTitle.value = "Formulaires";
      break;
    case "menus":
      activeComponentTitle.value = "Menus";
      break;
    case "music":
      activeComponentTitle.value = "Musique";
      break;
    case "search":
      activeComponentTitle.value = "Recherche";
      break;
    default:
      isEmptyComponent.value = true;
      break;
  }
};

const currentComponent = computed(() => {
  switch (activeComponent.value) {
    case "buttons":
      return UiButtons;
    case "forms":
      return UiForms;
    case "menus":
      return UiMenu;
    case "music":
      return UiMusic;
    default:
      return null;
  }
});
</script>
