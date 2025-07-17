import { useRouter } from "vue-router";
import type { Music, Album, Playlist, Artist } from "@/utils/types";

// Icônes
import dotsLight from "@/assets/icons/dots-light.svg";
import addToFavActive from "@/assets/icons/add-to-fav-active-light.svg";
import addToFavInactive from "@/assets/icons/add-to-fav-inactive-light.svg";
import playlistLight from "@/assets/icons/playlist-light.svg";
import queueLight from "@/assets/icons/queue-light.svg";
import queueNextLight from "@/assets/icons/queue-next-light.svg";
import micLight from "@/assets/icons/mic-light.svg";
import discLight from "@/assets/icons/disc-light.svg";
import addLight from "@/assets/icons/add-light.svg";
import removeLight from "@/assets/icons/remove-light.svg";
import editLight from "@/assets/icons/edit-light.svg";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "./useToast";
import { usePlayerPreparation } from "./usePlayerPreparation";
import { usePlayerStore } from "@/stores/player";
import { useModalsStore } from "@/stores/modals";
import { useFavoritesStore } from "@/stores/favorites";

// Types des menus
export type MenuType =
  | "album"
  | "albumTitle"
  | "playlist"
  | "playlistTitle"
  | "queue"
  | "favorites";

// Types des éléments pouvant être passés au menu
export type MenuElement = Music | Album | Playlist;

// Type pour les handlers de menu
export type MenuHandler = () => void;
export type MenuHandlers = Record<string, MenuHandler>;

// Interface pour les props du menu (utilisées dans les fonctions icon/label/condition)
export interface MenuProps {
  isFavorite?: boolean;
  isInLibrary?: boolean;
  isPublic?: boolean;
  isUserPlaylist?: boolean;
  type?: MenuType;
  element?: MenuElement;
}

// Interface pour une action de menu
export interface MenuAction {
  action: string;
  icon: string | ((props: MenuProps) => string);
  label: string | ((props: MenuProps) => string);
  condition?: (props: MenuProps) => boolean | undefined;
  separator?: boolean;
}

// Liste des actions possibles avec icone, label, action et des conditions d'affichage
export const menuConfig: Record<MenuType, MenuAction[]> = {
  album: [
    {
      action: "addToLibrary",
      icon: (props: MenuProps) => (props.isFavorite ? removeLight : addLight),
      label: (props: MenuProps) =>
        props.isFavorite ? "Supprimer de la bibliothèque" : "Ajouter à la bibliothèque",
    },
    {
      action: "addToPlaylist",
      icon: playlistLight,
      label: "Ajouter à une playlist",
    },
    {
      action: "addToQueue",
      icon: queueLight,
      label: "Ajouter à la file d'attente",
      separator: true,
    },
    { action: "playNext", icon: queueNextLight, label: "Lire ensuite" },
  ],

  albumTitle: [
    {
      action: "addToFavorites",
      icon: (props: MenuProps) => (props.isFavorite ? addToFavActive : addToFavInactive),
      label: (props: MenuProps) =>
        props.isFavorite ? "Supprimer des favoris" : "Ajouter aux favoris",
    },
    { action: "addToPlaylist", icon: playlistLight, label: "Ajouter à une playlist" },
    {
      action: "addToQueue",
      icon: queueLight,
      label: "Ajouter à la file d'attente",
      separator: true,
    },
    { action: "playNext", icon: queueNextLight, label: "Lire ensuite" },
    { action: "goToArtist", icon: micLight, label: "Aller à l'artiste", separator: true },
  ],

  playlist: [
    {
      action: "addToLibrary",
      icon: (props: MenuProps) => (props.isFavorite ? removeLight : addLight),
      label: (props: MenuProps) =>
        props.isFavorite ? "Supprimer de la bibliothèque" : "Ajouter à la bibliothèque",
    },
    {
      action: "addToQueue",
      icon: queueLight,
      label: "Ajouter à la file d'attente",
      separator: true,
    },
    { action: "playNext", icon: queueNextLight, label: "Lire ensuite" },
    {
      action: "deletePlaylist",
      icon: removeLight,
      label: "Supprimer la playlist",
      separator: true,
      condition: (props: MenuProps) => props.isUserPlaylist,
    },
    {
      action: "editPlaylist",
      icon: editLight,
      label: "Modifier la playlist",
      condition: (props: MenuProps) => props.isUserPlaylist,
    },
    // De base fait pour la visibilité des playlist utilisateur mais pas implémenté pour le moment
    // {
    //   action: "togglePrivacy",
    //   icon: (props: any) => (props.isPublic ? lockLight : unlockLight),
    //   label: (props: any) => (props.isPublic ? "Rendre privé" : "Rendre public"),
    //   condition: (props: any) => props.isUserPlaylist,
    // },
  ],

  playlistTitle: [
    {
      action: "addToFavorites",
      icon: (props: MenuProps) => (props.isFavorite ? addToFavActive : addToFavInactive),
      label: (props: MenuProps) =>
        props.isFavorite ? "Supprimer des favoris" : "Ajouter aux favoris",
    },
    { action: "addToPlaylist", icon: playlistLight, label: "Ajouter à une playlist" },
    {
      action: "addToQueue",
      icon: queueLight,
      label: "Ajouter à la file d'attente",
      separator: true,
    },
    { action: "playNext", icon: queueNextLight, label: "Lire ensuite" },
    { action: "goToArtist", icon: micLight, label: "Aller à l'artiste", separator: true },
    { action: "goToAlbum", icon: discLight, label: "Aller à l'album" },
  ],

  queue: [
    {
      action: "addToFavorites",
      icon: (props: MenuProps) => (props.isFavorite ? addToFavActive : addToFavInactive),
      label: (props: MenuProps) =>
        props.isFavorite ? "Supprimer des favoris" : "Ajouter aux favoris",
    },
    { action: "addToPlaylist", icon: playlistLight, label: "Ajouter à une playlist" },
    {
      action: "addToQueue",
      icon: queueLight,
      label: "Ajouter à la file d'attente",
      separator: true,
    },
    { action: "playNext", icon: queueNextLight, label: "Lire ensuite" },
    { action: "goToArtist", icon: micLight, label: "Aller à l'artiste", separator: true },
    { action: "goToAlbum", icon: discLight, label: "Aller à l'album" },
  ],

  favorites: [
    {
      action: "addToFavorites",
      icon: removeLight,
      label: "Supprimer des favoris",
    },
    { action: "addToPlaylist", icon: playlistLight, label: "Ajouter à une playlist" },
    {
      action: "addToQueue",
      icon: queueLight,
      label: "Ajouter à la file d'attente",
      separator: true,
    },
    { action: "playNext", icon: queueNextLight, label: "Lire ensuite" },
    { action: "goToArtist", icon: micLight, label: "Aller à l'artiste", separator: true },
    { action: "goToAlbum", icon: discLight, label: "Aller à l'album" },
  ],
};

// Composable pour la gestion des événements du menu
export function useUnifiedMenu() {
  const router = useRouter();
  const { apiClient } = useApiClient();
  const { showSuccess, showError } = useToast();
  const { addToQueue } = usePlayerPreparation();
  const modalsStore = useModalsStore();
  const playerStore = usePlayerStore();
  const favoritesStore = useFavoritesStore();
  // Gestionnaires d'événements centralisés
  const menuHandlers = {
    addToFavorites: async (element: MenuElement, menuType?: MenuType) => {
      const targetType =
        element["@id"].split("/")[2] === "albums"
          ? "album"
          : element["@id"].split("/")[2] === "playlists" ||
              element["@id"].split("/")[2] === "plateform_playlists"
            ? "playlist"
            : element["@id"].split("/")[2];

      try {
        const isCurrentlyFavorite = menuType === "favorites" ? true : element.isFavorite;

        if (isCurrentlyFavorite) {
          await apiClient.favorite.remove({ [targetType]: element["@id"] });
          if (targetType === "music") {
            favoritesStore.removeFavorite(element as Music);
          }
          showSuccess("Titre supprimé des favoris");
        } else {
          await apiClient.favorite.add({ [targetType]: element["@id"] });
          if (targetType === "music") {
            favoritesStore.addFavorite(element as Music);
          }
          showSuccess("Titre ajouté aux favoris");
        }
        element.isFavorite = !element.isFavorite;
      } catch (error) {
        console.error("Erreur lors de la mise à jour des favoris:", error);
        showError("Erreur lors de la mise à jour des favoris");
      }
    },

    addToPlaylist: (element: MenuElement) => {
      modalsStore.openPlaylistSelector(element);
    },

    addToQueue: (element: MenuElement, shouldBeNext: boolean = false) => {
      const targetType =
        element["@id"].split("/")[2] === "albums"
          ? "album"
          : element["@id"].split("/")[2] === "playlists" ||
              element["@id"].split("/")[2] === "plateform_playlists"
            ? "playlist"
            : element["@id"].split("/")[2];

      addToQueue({
        [targetType]: element["@id"],
        currentPosition: playerStore.position,
        shouldBeNext,
      });
    },

    goToArtist: (element: Music) => {
      if (element.mainArtist && (element.mainArtist as Artist).id) {
        router.push(`/artiste/${(element.mainArtist as Artist).id}`);
      } else if ((element.mainArtist as string).startsWith("/api/artists/")) {
        const mainArtistId = (element.mainArtist as string).split("/").pop();
        router.push(`/artiste/${mainArtistId}`);
      } else {
        showError("Nous ne parvenons pas à trouver l'artiste...");
      }
    },

    goToAlbum: (element: Music) => {
      if (element.album && (element.album as Album).id) {
        router.push(`/album/${(element.album as Album).id}`);
      } else if ((element.album as string).startsWith("/api/albums/")) {
        const albumId = (element.album as string).split("/").pop();
        router.push(`/album/${albumId}`);
      } else {
        showError("Nous ne parvenons pas à trouver l'album...");
      }
    },

    // deletePlaylist: (element: Playlist) => {
    //   // Afficher confirmation et supprimer
    //   // if (await confirmDelete(element.title)) {
    //   //   await apiClient.playlists.delete(element.id);
    //   // }
    // },

    // editPlaylist: (element: Playlist) => {
    //   // Ouvrir modal d'édition
    //   // await openPlaylistEditor(element);
    // },
  };

  // Fonction pour créer les gestionnaires d'événements configurés pour un élément spécifique
  const createMenuHandlers = (element: MenuElement, menuType?: MenuType): MenuHandlers => {
    return {
      handleAddToFavorites: () => menuHandlers.addToFavorites(element as Music, menuType),
      handleAddToPlaylist: () => menuHandlers.addToPlaylist(element),
      handleAddToQueue: () => menuHandlers.addToQueue(element),
      handlePlayNext: () => menuHandlers.addToQueue(element, true),
      handleGoToArtist: () => menuHandlers.goToArtist(element as Music),
      handleGoToAlbum: () => menuHandlers.goToAlbum(element as Music),
      handleAddToLibrary: () => menuHandlers.addToFavorites(element as Album | Playlist, menuType),
      // handleDeletePlaylist: () => menuHandlers.deletePlaylist(element as Playlist),
      // handleEditPlaylist: () => menuHandlers.editPlaylist(element as Playlist),
    };
  };

  return {
    menuHandlers,
    createMenuHandlers,
  };
}

export { dotsLight };
