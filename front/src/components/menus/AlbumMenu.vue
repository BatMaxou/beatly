<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";
import dotsLight from "@/assets/icons/dots-light.svg";
import addLight from "@/assets/icons/add-light.svg";
import playlistLight from "@/assets/icons/playlist-light.svg";
import playListLight from "@/assets/icons/play-list-light.svg";

// Faire passer l'id de l'élément dans les props pour pouvoir l'utiliser dans le menu
const props = defineProps({
  showMenu: {
    type: Boolean,
    default: false,
  },
  position: {
    type: String as () => "top-left" | "top-right" | "bottom-left" | "bottom-right",
    default: "bottom-left",
    validator: (value: string) => ["top-left", "top-right", "bottom-left", "bottom-right"].includes(value),
  },
  isInLibrary: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["addToLibrary", "addToPlaylist", "addToQueue", "closeMenu"]);

const menuVisible = ref(props.showMenu);
const menuRef = ref<HTMLElement | null>(null);

const toggleMenu = () => {
  menuVisible.value = !menuVisible.value;
  if (!menuVisible.value) {
    emit("closeMenu");
  }
};

const handleAction = (action: "addToLibrary" | "addToPlaylist" | "addToQueue") => {
  emit(action);
  toggleMenu();
};

const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement;
  if (menuRef.value && !menuRef.value.contains(target) && menuVisible.value) {
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
      class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-700/30 transition-colors duration-200 focus:outline-none"
      aria-label="Menu options d'album"
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
          v-if="!props.isInLibrary"
          @click="handleAction('addToLibrary')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-gray-700/50 transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône ajouter à la bibliothèque -->
          <img :src="addLight" alt="Ajouter" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Ajouter à la bibliothèque</span>
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
      </div>
    </div>
  </div>
</template>
