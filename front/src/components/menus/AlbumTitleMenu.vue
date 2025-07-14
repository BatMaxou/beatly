<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";
import dotsLight from "@/assets/icons/dots-light.svg";
import addToFavActive from "@/assets/icons/add-to-fav-active-light.svg";
import addToFavInactive from "@/assets/icons/add-to-fav-inactive-light.svg";
import playlistLight from "@/assets/icons/playlist-light.svg";
import queueLight from "@/assets/icons/queue-light.svg";
import micLight from "@/assets/icons/mic-light.svg";

// Faire passer l'id de l'élément dans les props pour pouvoir l'utiliser dans le menu
const props = defineProps({
  showMenu: {
    type: Boolean,
    default: false,
  },
  position: {
    type: String as () => "top-left" | "top-right" | "bottom-left" | "bottom-right",
    default: "bottom-left",
    validator: (value: string) =>
      ["top-left", "top-right", "bottom-left", "bottom-right"].includes(value),
  },
  isFavorite: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  "addToFavorites",
  "addToPlaylist",
  "addToQueue",
  "goToArtist",
  "closeMenu",
]);

const menuVisible = ref(props.showMenu);
const menuRef = ref<HTMLElement | null>(null);

const toggleMenu = () => {
  menuVisible.value = !menuVisible.value;
  if (!menuVisible.value) {
    emit("closeMenu");
  }
};

const handleAction = (
  action: "addToFavorites" | "addToPlaylist" | "addToQueue" | "goToArtist" | "closeMenu",
) => {
  emit(action);
  toggleMenu();
};

const handleClickOutside = (event: Event) => {
  if (menuRef.value && !menuRef.value.contains(event.target as HTMLElement) && menuVisible.value) {
    menuVisible.value = false;
    emit("closeMenu");
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
  <div class="relative" ref="menuRef">
    <!-- Bouton pour ouvrir le menu -->
    <button
      @click.stop="toggleMenu"
      class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-[#440a50] transition-colors duration-200 focus:outline-none"
      aria-label="Menu options"
    >
      <img :src="dotsLight" alt="Options" class="w-5 h-5 dark:invert" />
    </button>

    <!-- Menu contextuel -->
    <div
      v-if="menuVisible"
      :class="[
        'absolute z-50 bg-[#1a0725] backdrop-blur-md border border-[#440a50] rounded-xl shadow-xl overflow-hidden transform transition-all duration-200 dark:bg-gray-900/90 dark:border-gray-700 whitespace-nowrap',
        {
          'top-full mt-2': props.position.startsWith('bottom'),
          'bottom-full mb-2': props.position.startsWith('top'),
          'left-0': props.position.endsWith('left'),
          'right-0': props.position.endsWith('right'),
        },
      ]"
    >
      <div class="py-1 text-sm min-w-max">
        <button
          @click="handleAction('addToFavorites')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-[#440a50] transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône favoris -->
          <img
            :src="props.isFavorite ? addToFavActive : addToFavInactive"
            alt="Favoris"
            class="w-5 h-5 flex-shrink-0"
          />
          <span class="ml-3 whitespace-nowrap">{{
            props.isFavorite ? "Supprimer des favoris" : "Ajouter aux favoris"
          }}</span>
        </button>

        <button
          @click="handleAction('addToPlaylist')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-[#440a50] transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône playlist -->
          <img :src="playlistLight" alt="Playlist" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Ajouter à une playlist</span>
        </button>

        <button
          @click="handleAction('addToQueue')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-[#440a50] transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône file d'attente -->
          <img :src="queueLight" alt="File d'attente" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Ajouter à la file d'attente</span>
        </button>

        <div class="border-t border-[#440a50] my-1 dark:border-gray-600"></div>

        <button
          @click="handleAction('goToArtist')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-[#440a50] transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône artiste -->
          <img :src="micLight" alt="Artiste" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Aller à l'artiste</span>
        </button>
      </div>
    </div>
  </div>
</template>
