<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed, nextTick, defineComponent } from "vue";
import { useMenuManager } from "@/composables/useMenuManager";
import {
  menuConfig,
  dotsLight,
  useUnifiedMenu,
  type MenuType,
  type MenuAction,
  type MenuElement,
} from "@/composables/useUnifiedMenu";

const props = withDefaults(
  defineProps<{
    type: MenuType;
    element?: MenuElement;
    showMenu?: boolean;
    showMenuIcon?: boolean;
    isFavorite?: boolean;
    isInLibrary?: boolean;
    isPublic?: boolean;
    isUserPlaylist?: boolean;
  }>(),
  {
    showMenu: false,
    showMenuIcon: true,
    isFavorite: false,
    isInLibrary: false,
    isPublic: false,
    isUserPlaylist: false,
  },
);

// Émissions de toutes les actions possibles
const emit = defineEmits<{
  closeMenu: [];
  openMenu: [];

  addToQueue: [];
  addToPlaylist: [];
  addToFavorites: [];

  goToArtist: [];
  goToAlbum: [];

  deletePlaylist: [];
  editPlaylist: [];
  togglePrivacy: [];
}>();

// useUnifiedMenu == Sert a gérer les évènements du menu
const { createMenuHandlers } = useUnifiedMenu();
// Création des évènements du menu depuis le composable
const handlers = computed(() => {
  if (!props.element) return {};
  return createMenuHandlers(props.element, props);
});

// useMenuManager == Sert a gérer l'id du menu ouvert
const {
  registerMenu,
  unregisterMenu,
  openMenu: openMenuGlobal,
  closeMenu: closeMenuGlobal,
  generateMenuId,
} = useMenuManager();

const menuId = generateMenuId();
const menuVisible = ref(props.showMenu);
const menuRef = ref<HTMLElement | null>(null);
const menuPosition = ref({ x: 0, y: 0, useCustomPosition: false });
const position = ref<string>("bottom-right");
// Configuration du menu basée sur le type
const currentMenuConfig = computed(() => menuConfig[props.type]);

// Actions filtrées selon les conditions
const availableActions = computed(() => {
  return currentMenuConfig.value.filter((action) => {
    if (action.condition) {
      return action.condition(props);
    }
    return true;
  });
});

const menuClasses = computed(() => {
  const baseClasses = [
    "z-50 bg-[#1a0725] backdrop-blur-md border border-[#440a50] rounded-xl shadow-xl overflow-hidden transform transition-all duration-200 dark:bg-gray-900/90 dark:border-gray-700 whitespace-nowrap",
    menuPosition.value.useCustomPosition ? "fixed" : "absolute",
  ];

  if (!menuPosition.value.useCustomPosition) {
    const fixedMenuPosition = getMenuPosition();

    if (fixedMenuPosition) {
      if (fixedMenuPosition.startsWith("bottom")) {
        baseClasses.push("top-full mt-2");
      }
      if (fixedMenuPosition.startsWith("top")) {
        baseClasses.push("bottom-full mb-2");
      }
      if (fixedMenuPosition.endsWith("left")) {
        baseClasses.push("left-0");
      }
      if (fixedMenuPosition.endsWith("right")) {
        baseClasses.push("right-0");
      }
    }
  }

  return baseClasses;
});

// Placeholder pour le positionnement manuel selon l'élément
const getMenuPosition = () => {
  let fixedMenuPosition;
  if ("albumTitle" === props.type || "playlistTitle" === props.type) {
    fixedMenuPosition = "top-right";
  }
  if ("album" === props.type || "playlist" === props.type || "queue" === props.type) {
    fixedMenuPosition = "bottom-right";
  }

  return fixedMenuPosition;
};

const closeMenu = () => {
  menuVisible.value = false;
  menuPosition.value.useCustomPosition = false;
  closeMenuGlobal(menuId);
  emit("closeMenu");
};

// S'execute seulement quand le clic provient du clic droit car position custom
const setPosition = (x: number, y: number) => {
  menuPosition.value = { x, y, useCustomPosition: true };
};

const toggleMenu = () => {
  if (menuVisible.value) {
    closeMenu();
  } else {
    openMenu();
  }
};

const openMenu = (x?: number, y?: number) => {
  closeMenu();
  if (x !== undefined && y !== undefined) {
    setPosition(x, y);
  }
  openMenuGlobal(menuId, x, y);
  menuVisible.value = true;
  emit("openMenu");
};

// Exposer la fonction openMenu pour les composants parents
defineExpose({
  openMenu,
  setCustomPosition: (x: number, y: number) => setPosition(x, y),
});

// Récupération du handler de l'élément choisi & execution avec handle() <= fonction dans useUnifiedMenu
const handleAction = (action: string) => {
  const handlerName = `handle${action.charAt(0).toUpperCase() + action.slice(1)}`;
  const handlerMap = handlers.value;

  if (handlerMap && typeof handlerMap === "object") {
    const handler = (handlerMap as Record<string, Function>)[handlerName];
    if (typeof handler === "function") {
      handler();
      closeMenu();
      return;
    }
  }
  closeMenu();
  return;
};

const handleClickOutside = (event: Event) => {
  if (menuRef.value && !menuRef.value.contains(event.target as HTMLElement) && menuVisible.value) {
    closeMenu();
  }
};

const getIcon = (action: MenuAction) => {
  if (typeof action.icon === "function") {
    return action.icon(props);
  }
  return action.icon;
};

const getLabel = (action: MenuAction) => {
  if (typeof action.label === "function") {
    return action.label(props);
  }
  return action.label;
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
  registerMenu(menuId, closeMenu, setPosition);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
  unregisterMenu(menuId);
});
</script>

<template>
  <div class="relative" ref="menuRef">
    <!-- Bouton pour ouvrir le menu -->
    <button
      v-if="props.showMenuIcon || menuVisible"
      @click.stop="toggleMenu"
      @contextmenu.stop.prevent
      :class="[
        'flex items-center justify-center w-8 h-8 rounded-full hover:bg-[#440a50] transition-colors duration-200 focus:outline-none',
        { 'bg-[#440a50]': menuVisible },
      ]"
      aria-label="Menu options"
    >
      <img :src="dotsLight" alt="Options" class="w-5 h-5 dark:invert" />
    </button>

    <!-- Menu contextuel -->
    <div
      v-if="menuVisible"
      @click.stop
      @contextmenu.prevent
      :class="menuClasses"
      :style="
        menuPosition.useCustomPosition
          ? {
              left: `${menuPosition.x}px`,
              top: `${menuPosition.y}px`,
            }
          : {}
      "
    >
      <div class="py-1 text-sm min-w-max">
        <template v-for="(action, index) in availableActions" :key="action.action">
          <!-- Séparateur -->
          <div
            v-if="action.separator && index > 0"
            class="border-t border-[#440a50] my-1 dark:border-gray-600"
          ></div>

          <!-- Bouton d'action -->
          <button
            @click.stop="handleAction(action.action)"
            @contextmenu.stop.prevent
            class="flex items-center w-full px-4 py-3 text-white hover:bg-[#440a50] transition-colors duration-200 dark:hover:bg-gray-800/70"
          >
            <img :src="getIcon(action)" :alt="getLabel(action)" class="w-5 h-5 flex-shrink-0" />
            <span class="ml-3 whitespace-nowrap">{{ getLabel(action) }}</span>
          </button>
        </template>
      </div>
    </div>
  </div>
</template>
