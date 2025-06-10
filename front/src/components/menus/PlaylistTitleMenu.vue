<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import dotsLight from "@/assets/icons/dots-light.svg";
import addToFavActive from "@/assets/icons/add-to-fav-active-light.svg";
import addToFavInactive from "@/assets/icons/add-to-fav-inactive-light.svg";
import playlistLight from "@/assets/icons/playlist-light.svg";
import playListLight from "@/assets/icons/play-list-light.svg";
import micLight from "@/assets/icons/mic-light.svg";
import discLight from "@/assets/icons/disc-light.svg";

const props = defineProps({
  showMenu: {
    type: Boolean,
    default: false,
  },
  position: {
    type: String,
    default: "bottom-left",
    validator: (value) => ["top-left", "top-right", "bottom-left", "bottom-right"].includes(value),
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
  "goToAlbum",
  "closeMenu",
]);

const menuVisible = ref(props.showMenu);
const menuRef = ref(null);

const toggleMenu = () => {
  menuVisible.value = !menuVisible.value;
  if (!menuVisible.value) {
    emit("closeMenu");
  }
};

const handleAction = (action) => {
  emit(action);
  toggleMenu();
};

const handleClickOutside = (event) => {
  if (menuRef.value && !menuRef.value.contains(event.target) && menuVisible.value) {
    menuVisible.value = false;
    emit("closeMenu");
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
  <div class="relative" ref="menuRef">
    <!-- Bouton pour ouvrir le menu -->
    <button
      @click.stop="toggleMenu"
      class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-700/30 transition-colors duration-200 focus:outline-none"
      aria-label="Menu options"
    >
      <img :src="dotsLight" alt="Options" class="w-5 h-5 dark:invert" />
    </button>

    <!-- Menu contextuel -->
    <div
      v-if="menuVisible"
      :class="[
        'absolute z-50 bg-gray-800/90 backdrop-blur-md border border-gray-700 rounded-xl shadow-xl overflow-hidden transform transition-all duration-200 dark:bg-gray-900/90 dark:border-gray-700 whitespace-nowrap',
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
          class="flex items-center w-full px-4 py-3 text-white hover:bg-gray-700/50 transition-colors duration-200 dark:hover:bg-gray-800/70"
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
          class="flex items-center w-full px-4 py-3 text-white hover:bg-gray-700/50 transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône playlist -->
          <img :src="playlistLight" alt="Playlist" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Ajouter à une playlist</span>
        </button>

        <button
          @click="handleAction('addToQueue')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-gray-700/50 transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône file d'attente -->
          <img :src="playListLight" alt="File d'attente" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Ajouter à la file d'attente</span>
        </button>

        <div class="border-t border-gray-700 my-1 dark:border-gray-600"></div>

        <button
          @click="handleAction('goToArtist')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-gray-700/50 transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône artiste -->
          <img :src="micLight" alt="Artiste" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Aller à l'artiste</span>
        </button>

        <button
          @click="handleAction('goToAlbum')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-gray-700/50 transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône album -->
          <img :src="discLight" alt="Album" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Aller à l'album</span>
        </button>
      </div>
    </div>
  </div>
</template>
