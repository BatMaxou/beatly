<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useHead } from "@unhead/vue";

import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import type { Album } from "@/utils/types";
import MusicList from "@/components/lists/MusicList.vue";
import LandingButton from "@/components/buttons/LandingButton.vue";
import arrowLeft from "@/assets/icons/arrow-left-light.svg";
import defaultWallpaper from "@/assets/images/default-wallpaper-playlist.jpg";
import defaultCover from "@/assets/images/default-cover.png";
import UnifiedMenu from "@/components/menus/UnifiedMenu.vue";
import { ressourceUrl } from "@/utils/tools";

const album = ref<Album | null>(null);
useHead({
  title: computed(() => `Beatly | Détail de ${album?.value?.title || "l'album"}`),
});

const router = useRouter();
const route = useRoute();
const { apiClient } = useApiClient();
const loading = ref(false);
const releaseYear = ref<number | null>(null);

const albumId = route.params.id as string;

const handleBack = () => {
  router.go(-1);
};

const goToArtist = () => {
  if (album.value?.artist) {
    router.push(`/artiste/${album.value.artist.id}`);
  }
};

onMounted(async () => {
  if (albumId) {
    loading.value = true;
    try {
      const response = await apiClient.album.get(albumId);
      if (response.id) {
        album.value = response;
        releaseYear.value = new Date(album.value.releaseDate).getFullYear();
      } else {
        album.value = null;
      }
      loading.value = false;
    } catch (error) {
      console.error("Erreur lors du chargement de l'album:", error);
      loading.value = false;
    }
  }
});
</script>

<template>
  <InAppLayout :loading="loading" padding="p-0">
    <div v-if="album">
      <div
        :style="{
          backgroundImage: album.wallpaper
            ? `url(${ressourceUrl + album.wallpaper})`
            : `url(${defaultWallpaper})`,
        }"
        class="bg-cover bg-center object-cover flex flex-row justify-between items-end relative albumBackground mb-6 z-2"
      >
        <div class="relative flex flex-row justify-start items-between gap-4 pt-24 ps-16 z-10">
          <img
            :src="album.cover ? ressourceUrl + album.cover : defaultCover"
            alt="Album Cover"
            class="w-[200px] h-[200px] object-cover"
          />
          <div class="flex flex-col items-start justify-end mb-4">
            <span class="mb-8">Album</span>
            <p class="text-white text-4xl font-bold">{{ album?.title }}</p>
            <p @click="goToArtist()" class="text-white text-md hover:underline cursor-pointer">
              {{ album?.artist.name }}
            </p>
            <p class="text-md">
              <span class="">{{ releaseYear }}</span>
              <span class="text-lg font-bold"> • </span>
              <span class=""
                >{{ album.musics.length }} titre{{ album.musics.length > 1 ? "s" : "" }}</span
              >
            </p>
          </div>
        </div>
        <UnifiedMenu
          type="album"
          :element="album"
          :albumId="album.id"
          class="me-16 mb-16 h-full z-10"
          :isFavorite="album.isFavorite"
        />
      </div>
      <div v-if="album" class="text-white px-10">
        <div class="space-y-2">
          <MusicList
            :musicList="album.musics"
            :origin="`album`"
            :parentId="album['@id']"
            :theme="`light`"
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
