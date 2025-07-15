<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";
import dotsLight from "@/assets/icons/dots-light.svg";
import queueLight from "@/assets/icons/queue-light.svg";
import removelight from "@/assets/icons/remove-light.svg";
import editLight from "@/assets/icons/edit-light.svg";
import lockLight from "@/assets/icons/lock-light.svg";
import unlockLight from "@/assets/icons/unlock-light.svg";
import addLight from "@/assets/icons/add-light.svg";

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
  isPublic: {
    type: Boolean,
    default: false,
  },
  isUserPlaylist: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  "addToQueue",
  "deletePlaylist",
  "editPlaylist",
  "togglePrivacy",
  "addToLibrary",
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
  action:
    | "addToQueue"
    | "deletePlaylist"
    | "editPlaylist"
    | "togglePrivacy"
    | "addToLibrary"
    | "closeMenu",
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
          @click="handleAction('addToQueue')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-[#440a50] transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône file d'attente -->
          <img :src="queueLight" alt="File d'attente" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Ajouter à la file d'attente</span>
        </button>

        <button
          v-if="!props.isUserPlaylist"
          @click="handleAction('addToLibrary')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-[#440a50] transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône ajouter à la bibliothèque -->
          <img :src="addLight" alt="Ajouter" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Ajouter à la bibliothèque</span>
        </button>

        <div class="border-t border-[#440a50] my-1 dark:border-gray-600"></div>

        <button
          @click="handleAction('editPlaylist')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-[#440a50] transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône édition -->
          <img :src="editLight" alt="Éditer" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Modifier les informations</span>
        </button>

        <button
          @click="handleAction('togglePrivacy')"
          class="flex items-center w-full px-4 py-3 text-white hover:bg-[#440a50] transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône verrouillage -->
          <img
            :src="props.isPublic ? lockLight : unlockLight"
            alt="Visibilité"
            class="w-5 h-5 flex-shrink-0"
          />
          <span class="ml-3 whitespace-nowrap">{{
            props.isPublic ? "Rendre privée" : "Rendre publique"
          }}</span>
        </button>

        <div class="border-t border-[#440a50] my-1 dark:border-gray-600"></div>

        <button
          @click="handleAction('deletePlaylist')"
          class="flex items-center w-full px-4 py-3 text-red-400 hover:bg-[#440a50] transition-colors duration-200 dark:hover:bg-gray-800/70"
        >
          <!-- Icône suppression -->
          <img :src="removelight" alt="Supprimer" class="w-5 h-5 flex-shrink-0" />
          <span class="ml-3 whitespace-nowrap">Supprimer la playlist</span>
        </button>
      </div>
    </div>
  </div>
</template>
