<script setup lang="ts">
import { onMounted, ref, computed, watch } from "vue";
import { useRouter } from "vue-router";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import MusicList from "@/components/lists/MusicList.vue";
import LandingButton from "@/components/buttons/LandingButton.vue";
import arrowLeft from "@/assets/icons/arrow-left-light.svg";
import defaultWallpaper from "@/assets/images/favorites-background.jpg";
import defaultCover from "@/assets/images/favorites-cover.png";
import { useFavoritesStore } from "@/stores/favorites";

const router = useRouter();
const { apiClient } = useApiClient();
const favoritesStore = useFavoritesStore();
const loading = ref(false);
const favoritesList = computed(() => {
  return favoritesStore.favorites;
});

const handleBack = () => {
  router.go(-1);
};

const loadFavoritesFromApi = async () => {
  loading.value = true;
  try {
    const response = await apiClient.favorite.getMusics();
    if (response && response.musics) {
      favoritesStore.setFavorites(response.musics);
    }
  } catch (error) {
    console.error("Erreur lors du chargement des favoris:", error);
  } finally {
    loading.value = false;
  }
};

watch(
  () => favoritesStore.hasLocalChanges,
  async (newValue) => {
    if (newValue) {
      const response = await apiClient.favorite.getMusics();
      if (response && response.musics) {
        favoritesStore.setFavorites(response.musics);
      }
    }
  },
);

onMounted(async () => {
  // Vérifier si on a des données dans le store et si elles sont à jour
  const shouldReload =
    favoritesStore.favorites.length === 0 || // Pas de données
    favoritesStore.hasLocalChanges || // Changements locaux depuis le dernier sync
    !favoritesStore.lastApiSync || // Jamais synchronisé
    new Date().getTime() - favoritesStore.lastApiSync.getTime() > 5 * 60 * 1000; // Plus de 5 minutes

  if (shouldReload) {
    await loadFavoritesFromApi();
  }
});
</script>

<template>
  <InAppLayout :loading="loading" padding="p-0">
    <div v-if="favoritesList.length > 0">
      <div
        :style="{
          backgroundImage: `url(${defaultWallpaper})`,
        }"
        class="bg-cover bg-center object-cover flex flex-row justify-between items-end relative albumBackground mb-6 z-2"
      >
        <div class="relative flex flex-row justify-start items-between gap-4 pt-24 ps-16 z-10">
          <img :src="defaultCover" alt="Album Cover" class="w-[200px] h-[200px] object-cover" />
          <div class="flex flex-col items-start justify-end mb-4">
            <span class="mb-8">Playlist</span>
            <p class="text-white text-4xl font-bold">Titres likés</p>
            <div class="flex items-center gap-2 text-white text-md">
              <span class="font-bold">Vous</span>
              <span class="font-bold">•</span>
              <span>
                <span class=""
                  >{{ favoritesList.length }} titre{{ favoritesList.length > 1 ? "s" : "" }}</span
                >
              </span>
            </div>
          </div>
        </div>
        <!-- <UnifiedMenu
          type="album"
          :element="favoriteList"
          class="me-16 mb-16 h-full z-10"
        /> -->
      </div>
      <div v-if="favoritesList.length > 0" class="text-white px-10">
        <div class="space-y-2">
          <MusicList
            :musicList="favoritesList"
            origin="favorites"
            parentId="/api/favorite_playlists/musics"
            :theme="`light`"
            isFavorite="true"
          />
        </div>
      </div>
    </div>
    <div
      v-else
      class="absolute inset-0 flex flex-col justify-center items-center gap-2 text-white text-center"
    >
      <p class="text-2xl">Oups...</p>
      <p class="text-lg">Nous ne parvenons pas à trouver cette album.</p>
      <LandingButton
        label="Revenir en arrière"
        :icon="arrowLeft"
        type="button"
        @click="handleBack"
      />
    </div>
  </InAppLayout>
</template>

<style scoped>
.albumBackground::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.1));
  z-index: 2;
}
</style>
