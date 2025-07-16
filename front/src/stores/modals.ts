import { defineStore } from "pinia";
import { ref } from "vue";
import type { MenuElement } from "@/composables/useUnifiedMenu";

export const useModalsStore = defineStore("modals", () => {
  const isPlaylistSelectorVisible = ref(false);
  const playlistSelectorElement = ref<MenuElement | null>(null);

  const openPlaylistSelector = (element: MenuElement) => {
    playlistSelectorElement.value = element;
    isPlaylistSelectorVisible.value = true;
  };

  const closePlaylistSelector = () => {
    isPlaylistSelectorVisible.value = false;
    playlistSelectorElement.value = null;
  };

  return {
    isPlaylistSelectorVisible,
    playlistSelectorElement,
    openPlaylistSelector,
    closePlaylistSelector,
  };
});
