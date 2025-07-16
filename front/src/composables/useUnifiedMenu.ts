import { useRouter } from "vue-router";
import type { Music, Album, Playlist } from "@/utils/types";

// Icônes
import dotsLight from "@/assets/icons/dots-light.svg";
import addToFavActive from "@/assets/icons/add-to-fav-active-light.svg";
import addToFavInactive from "@/assets/icons/add-to-fav-inactive-light.svg";
import playlistLight from "@/assets/icons/playlist-light.svg";
import queueLight from "@/assets/icons/queue-light.svg";
import micLight from "@/assets/icons/mic-light.svg";
import discLight from "@/assets/icons/disc-light.svg";
import addLight from "@/assets/icons/add-light.svg";
import removelight from "@/assets/icons/remove-light.svg";
import editLight from "@/assets/icons/edit-light.svg";

// Types des menus
export type MenuType = "album" | "albumTitle" | "playlist" | "playlistTitle" | "queue";

// Types des éléments pouvant être passés au menu
export type MenuElement = Music | Album | Playlist;

// Interface pour une action de menu
export interface MenuAction {
  action: string;
  icon: string | ((props: any) => string);
  label: string | ((props: any) => string);
  condition?: (props: any) => boolean;
  separator?: boolean;
}

// Liste des actions possibles avec icone, label, action et des conditions d'affichage
export const menuConfig: Record<MenuType, MenuAction[]> = {
  album: [
    {
      action: "addToLibrary",
      icon: addLight,
      label: "Ajouter à la bibliothèque",
      condition: (props: any) => !props.isInLibrary,
    },
    { action: "addToPlaylist", icon: playlistLight, label: "Ajouter à une playlist" },
    { action: "addToQueue", icon: queueLight, label: "Ajouter à la file d'attente" },
  ],

  albumTitle: [
    {
      action: "addToFavorites",
      icon: (props: any) => (props.isFavorite ? addToFavActive : addToFavInactive),
      label: (props: any) => (props.isFavorite ? "Supprimer des favoris" : "Ajouter aux favoris"),
    },
    { action: "addToPlaylist", icon: playlistLight, label: "Ajouter à une playlist" },
    { action: "addToQueue", icon: queueLight, label: "Ajouter à la file d'attente" },
    { action: "goToArtist", icon: micLight, label: "Aller à l'artiste", separator: true },
  ],

  playlist: [
    { action: "addToQueue", icon: queueLight, label: "Ajouter à la file d'attente" },
    {
      action: "deletePlaylist",
      icon: removelight,
      label: "Supprimer la playlist",
      separator: true,
      condition: (props: any) => props.isUserPlaylist,
    },
    {
      action: "editPlaylist",
      icon: editLight,
      label: "Modifier la playlist",
      condition: (props: any) => props.isUserPlaylist,
    },
    // De base fait pour la visibilité des playlist utilisateur mais pas implémenté pour le moment
    // {
    //   action: "togglePrivacy",
    //   icon: (props: any) => (props.isPublic ? lockLight : unlockLight),
    //   label: (props: any) => (props.isPublic ? "Rendre privé" : "Rendre public"),
    //   condition: (props: any) => props.isUserPlaylist,
    // },
    {
      action: "addToLibrary",
      icon: addLight,
      label: "Ajouter à la bibliothèque",
      separator: true,
      condition: (props: any) => !props.isUserPlaylist,
    },
  ],

  playlistTitle: [
    {
      action: "addToFavorites",
      icon: (props: any) => (props.isFavorite ? addToFavActive : addToFavInactive),
      label: (props: any) => (props.isFavorite ? "Supprimer des favoris" : "Ajouter aux favoris"),
    },
    { action: "addToPlaylist", icon: playlistLight, label: "Ajouter à une playlist" },
    { action: "addToQueue", icon: queueLight, label: "Ajouter à la file d'attente" },
    { action: "goToArtist", icon: micLight, label: "Aller à l'artiste", separator: true },
    { action: "goToAlbum", icon: discLight, label: "Aller à l'album" },
  ],

  queue: [
    {
      action: "addToFavorites",
      icon: (props: any) => (props.isFavorite ? addToFavActive : addToFavInactive),
      label: (props: any) => (props.isFavorite ? "Supprimer des favoris" : "Ajouter aux favoris"),
    },
    { action: "addToPlaylist", icon: playlistLight, label: "Ajouter à une playlist" },
    { action: "goToArtist", icon: micLight, label: "Aller à l'artiste", separator: true },
    { action: "goToAlbum", icon: discLight, label: "Aller à l'album" },
  ],
};

// Composable pour la gestion des événements du menu
export function useUnifiedMenu() {
  const router = useRouter();

  // Gestionnaires d'événements centralisés
  const menuHandlers = {
    addToFavorites: (element: MenuElement, isFavorite: boolean) => {
      // if (isFavorite) {
      //   await apiClient.favorites.remove(element.id);
      // } else {
      //   await apiClient.favorites.add(element.id);
      // }
    },

    addToPlaylist: (element: MenuElement) => {
      // Ouvrir modal de sélection de playlist
      // await openPlaylistSelector(element);
    },

    addToQueue: (element: MenuElement) => {
      // Appel API pour ajouter à la file d'attente
      // await apiClient.queue.add(element, shouldBeNext);
    },

    goToArtist: (element: Music) => {
      if (element.mainArtist?.id) {
        router.push(`/artiste/${element.mainArtist.id}`);
      }
    },

    goToAlbum: (element: Music) => {
      // Récupérer l'album depuis le type music de l'élément séléctionné
      // router.push(`/album/${album.id}`);
    },

    addToLibrary: (element: Album | Playlist) => {
      // A voir comment gérer l'ajout en bibliothèque
      // Avec le favorite ?
      // await apiClient.library.add(element.id, element.type);
    },

    deletePlaylist: (element: Playlist) => {
      // Afficher confirmation et supprimer
      // if (await confirmDelete(element.title)) {
      //   await apiClient.playlists.delete(element.id);
      // }
    },

    editPlaylist: (element: Playlist) => {
      // Ouvrir modal d'édition
      // await openPlaylistEditor(element);
    },
  };

  // Fonction pour créer les gestionnaires d'événements configurés pour un élément spécifique
  const createMenuHandlers = (element: MenuElement, props: any = {}) => {
    return {
      handleAddToFavorites: () => menuHandlers.addToFavorites(element, props.isFavorite || false),
      handleAddToPlaylist: () => menuHandlers.addToPlaylist(element),
      handleAddToQueue: () => menuHandlers.addToQueue(element),
      handleGoToArtist: () => menuHandlers.goToArtist(element as Music),
      handleGoToAlbum: () => menuHandlers.goToAlbum(element as Music),
      handleAddToLibrary: () => menuHandlers.addToLibrary(element as Album | Playlist),
      handleDeletePlaylist: () => menuHandlers.deletePlaylist(element as Playlist),
      handleEditPlaylist: () => menuHandlers.editPlaylist(element as Playlist),
    };
  };

  return {
    menuHandlers,
    createMenuHandlers,
  };
}

export { dotsLight };
