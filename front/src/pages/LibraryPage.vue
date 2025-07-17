<script setup lang="ts">
import { onBeforeMount, ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useApiClient } from "@/stores/api-client";
import AlbumPlayableCard from "@/components/cards/AlbumPlayableCard.vue";
import PlaylistPlayableCard from "@/components/cards/PlaylistPlayableCard.vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import HorizontalScroller from "@/components/ui/HorizontalScroller.vue";
import favoriteCover from "@/assets/images/favorites-cover.png";
import type { Favorites } from "@/utils/types";

const { apiClient } = useApiClient();
const router = useRouter();

const recentlyListened = ref([]);
const favoriteCollection = ref<Favorites | null>(null);
const recommandations = ref<null>(null);
const loading = ref(false);
const isClickedToPlay = ref(false);

const handlePlayAlbum = (event: Event) => {
  const target = event.target as HTMLElement;
  const isButtonPlayClick = target.closest("[data-play]");

  if (!isButtonPlayClick) {
    if (target.closest("[data-album]")) {
      const albumElement = target.closest("[data-album]");
      const albumId = albumElement?.getAttribute("data-album-id");
      if (albumId) {
        router.push({ name: "Album", params: { id: albumId } });
      }
    }
  } else {
    isClickedToPlay.value = true;
  }
};
// Ajout de la playlist titre likés par défaut
const allFavoritePlaylists = computed(() => {
  const playlists = [];
  playlists.push({
    title: "Titres likés",
    origin: "favorite",
    "@id": "/api/favorite_playlists",
    cover: favoriteCover,
  });
  if (favoriteCollection.value?.playlists && favoriteCollection.value.playlists.length > 0) {
    playlists.push(...favoriteCollection.value.playlists.map((playlist) => playlist.target));
  }

  return playlists;
});

onBeforeMount(async () => {
  loading.value = true;
  try {
    const favoriteResponse = await apiClient.favorite.getAll();
    favoriteCollection.value = favoriteResponse;
  } catch (error) {
    console.error("Erreur lors de la récupération des playlists:", error);
    favoriteCollection.value = null;
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <InAppLayout :loading="loading" padding="pt-10">
    <h2 class="ps-10 text-white text-5xl font-bold md:mb-[50px] mb-[100px]">Bibliothèque</h2>

    <!-- Écoutes récentes si il y en a -->
    <div v-if="recentlyListened.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Écoutes récentes</h2>
    </div>

    <!-- Les playlist favorites  -->
    <div>
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Vos playlists préférées</h2>
      <HorizontalScroller :gap="64" :scroll-amount="3">
        <PlaylistPlayableCard
          v-for="(playlist, index) in allFavoritePlaylists"
          :key="playlist['@id'] || index"
          :playlist="playlist"
        />
      </HorizontalScroller>
    </div>

    <!-- Recommandations -->
    <div v-if="recommandations">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Selon votre beat</h2>
    </div>

    <!-- Les albums favoris  -->
    <div v-if="favoriteCollection?.albums && favoriteCollection.albums.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Albums favoris</h2>
      <HorizontalScroller :gap="32" :scroll-amount="4">
        <AlbumPlayableCard
          v-for="(album, index) in favoriteCollection.albums"
          :key="album.addedAt"
          :index="index"
          :album="album.target"
          :artistName="album.target.artist?.name"
          :releaseYear="new Date(album.target.releaseDate).getFullYear()"
          @click="handlePlayAlbum"
          data-album
        />
      </HorizontalScroller>
    </div>
  </InAppLayout>
</template>
