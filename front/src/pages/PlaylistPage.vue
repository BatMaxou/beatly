<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import type { Playlist } from "@/utils/types";
import MusicList from "@/components/lists/MusicList.vue";
import LandingButton from "@/components/buttons/LandingButton.vue";
import arrowLeft from "@/assets/icons/arrow-left-light.svg";
import defaultWallpaper from "@/assets/images/default-wallpaper-playlist.jpg";
import defaultCover from "@/assets/images/default-cover.png";

const ressourceUrl = import.meta.env.VITE_API_RESSOURCES_URL;
const router = useRouter();
const route = useRoute();
const { apiClient } = useApiClient();
const playlist = ref<Playlist | null>(null);
const loading = ref(false);

const playlistId = route.params.id as string;

const handleBack = () => {
  router.go(-1);
};

onMounted(async () => {
  if (playlistId) {
    loading.value = true;
    try {
      const response = await apiClient.playlist.get(playlistId);
      if (response.id) {
        playlist.value = response;
      } else {
        playlist.value = null;
      }
      loading.value = false;
    } catch (error) {
      console.error('Erreur lors du chargement de la playlist:', error);
      loading.value = false;
    }
  }
});
</script>

<template>
  <InAppLayout :loading="loading" padding="p-0">
    <div v-if="playlist">
      <div :style="{ backgroundImage: playlist.wallpaper ? `url(${ressourceUrl + playlist.wallpaper})` : `url(${defaultWallpaper})` }" class="bg-cover bg-center object-cover relative playlistBackground z-2">
        <div class="relative flex flex-row justify-start items-between gap-4 pt-24 ps-16 mb-6 z-10">
          <img :src="playlist.cover ? ressourceUrl + playlist.cover : defaultCover" alt="Playlist Cover" class="w-[200px] h-[200px] object-cover" />
          <div class="flex flex-col items-start justify-end mb-4">
              <span class="mb-8">Playlist</span>
              <p class="text-white text-4xl font-bold">{{ playlist?.title }}</p>
              <p class="text-md font-bold">
                <span class="font-bold">{{ playlist['@type'] === "Playlist" ? 'Beatly' : 'Vous' }}</span>
                <span class="text-lg front-bold "> • </span>
                <span class="">{{ playlist.musics.length }} titre{{ playlist.musics.length > 1 ? 's' : '' }}</span>
              </p>
            </div>
        </div>
      </div>      
      <div v-if="playlist" class="text-white px-10">
        <div class="space-y-2">
          <MusicList :musicList="playlist.musics" :origin="`playlist`" :theme="`light`" />
        </div>
      </div>
      
      <div v-else class="text-white">
        <p class="text-lg">Chargement de la playlist...</p>
        <p class="text-gray-400">ID: {{ playlistId }}</p>
      </div>
    </div>
    <div v-else class="absolute inset-0 flex flex-col justify-center items-center gap-2 text-white text-center">
      <p class="text-2xl">Oups...</p>
      <p class="text-lg">Nous ne parvenons pas à trouver cette playlist.</p>
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
.playlistBackground::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.1));
  z-index: 2;
}
</style>