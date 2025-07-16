import { ref, readonly } from "vue";

/**
 *
 * Composable qui gère l'état global de tous les menus de l'application.
 * Il assure qu'un seul menu soit ouvert à la fois et fournit des méthodes pour :
 * - Enregistrer/désenregistrer des instances de menus
 * - Ouvrir/fermer des menus
 * - Positionner les menus dynamiquement
 * - Générer des identifiants uniques pour identifier les menus
 *
 */

// Identifiant du menu actuellement ouvert (null si aucun menu ouvert)
const currentOpenMenu = ref<string | null>(null);

//Map contenant toutes les instances de menus enregistrées
const menuInstances = ref<
  Map<
    string,
    {
      close: () => void;
      setPosition?: (x: number, y: number) => void;
    }
  >
>(new Map());

export function useMenuManager() {
  // Enregistre une instance de menu
  const registerMenu = (
    menuId: string,
    closeFunction: () => void,
    setPositionFunction?: (x: number, y: number) => void,
  ) => {
    menuInstances.value.set(menuId, { close: closeFunction, setPosition: setPositionFunction });
  };

  //Désenregistre un menu
  const unregisterMenu = (menuId: string) => {
    menuInstances.value.delete(menuId);
    // Nettoyer l'état si ce menu était ouvert
    if (currentOpenMenu.value === menuId) {
      currentOpenMenu.value = null;
    }
  };

  // Ouvre un menu spécifique et ferme automatiquement tous les autres
  const openMenu = (menuId: string, x?: number, y?: number) => {
    // Fermer le menu actuellement ouvert s'il est différent
    if (currentOpenMenu.value && currentOpenMenu.value !== menuId) {
      const currentMenu = menuInstances.value.get(currentOpenMenu.value);
      if (currentMenu) {
        currentMenu.close();
      }
    }

    // Définir la position si fournie et si le menu supporte le repositionnement
    const menu = menuInstances.value.get(menuId);
    if (menu && menu.setPosition && x !== undefined && y !== undefined) {
      menu.setPosition(x, y);
    }

    currentOpenMenu.value = menuId;
  };

  // Ferme un menu spécifique
  const closeMenu = (menuId: string) => {
    if (currentOpenMenu.value === menuId) {
      currentOpenMenu.value = null;
    }
  };

  // Génère un identifiant unique pour un menu
  const generateMenuId = () => {
    const randomId = Math.floor(Math.random() * 9000) + 1000;
    return `${Date.now()}_${randomId}`;
  };

  return {
    currentOpenMenu: readonly(currentOpenMenu),
    registerMenu,
    unregisterMenu,
    openMenu,
    closeMenu,
    generateMenuId,
  };
}
