import type { Favorite, Music } from "@/utils/types";
import { defineStore } from "pinia";

export const useFavoritesStore = defineStore("favorites", {
  state: () => ({
    favorites: [] as Favorite<Music>[],
  }),
  actions: {
    setFavorites(favorites: Favorite<Music>[]) {
      this.favorites = favorites;
    },
    addFavorite(item: Music) {
      this.favorites.unshift({ addedAt: new Date().toLocaleString(), target: item });
    },
    removeFavorite(item: Music) {
      this.favorites = this.favorites.filter((fav) => fav.target.id !== item.id);
    },
  },
});
