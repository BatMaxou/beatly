<script setup lang="ts">
import { onBeforeMount, ref, onMounted } from "vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import PlaylistPlayable from "@/components/cards/PlaylistPlayableCard.vue";
import albumDefaultIcon from "@/assets/icons/disc-dark.svg";
import type { Playlist } from "@/utils/types";
import arrowLeft from "@/assets/icons/arrow-left-light.svg";
import arrowRight from "@/assets/icons/arrow-right-light.svg";

const { apiClient } = useApiClient();

const recentlyListened = ref([]);
const lastPlaylist = ref<Playlist[]>([]);
const recommendationList = ref([]);
const hitsList = ref([]);
const categoryList = ref([]);
const mostFavoriteList = ref([]);
const loading = ref(false);
const playlistSlider = ref<HTMLElement | null>(null);
const showNextButton = ref(true);
const showPrevButton = ref(false);

function scrollPlaylistSlider() {
  if (playlistSlider.value) {
    const elementWidth = playlistSlider.value.firstElementChild?.clientWidth || 0;
    const gap = 64;
    const scrollDistance = (elementWidth + gap) * 3;
    playlistSlider.value.scrollBy({ left: scrollDistance, behavior: "smooth" });
  }
}

function scrollPlaylistSliderBack() {
  if (playlistSlider.value) {
    const elementWidth = playlistSlider.value.firstElementChild?.clientWidth || 0;
    const gap = 64;
    const scrollDistance = (elementWidth + gap) * 3;
    playlistSlider.value.scrollBy({ left: -scrollDistance, behavior: "smooth" });
  }
}

function checkScrollEnd() {
  if (playlistSlider.value) {
    const { scrollLeft, scrollWidth, clientWidth } = playlistSlider.value;
    showNextButton.value = scrollLeft + clientWidth < scrollWidth - 10; // 10px de marge
    showPrevButton.value = scrollLeft > 10;
  }
}

onBeforeMount(async () => {
  loading.value = true;
  try {
    const response = await apiClient.playlist.getAll();

    const playlists: Playlist[] = response;

    lastPlaylist.value = playlists.slice(0, 10);
  } catch (error) {
    console.error("Erreur lors de la récupération des playlists:", error);
    lastPlaylist.value = [];
  } finally {
    loading.value = false;
  }
});

onMounted(() => {
  setTimeout(() => {
    checkScrollEnd();
  }, 100);
});
</script>

<template>
  <InAppLayout :loading="loading" padding="pt-10">
    <!-- Écoutes récentes si il y en a -->
    <div v-if="recentlyListened.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Écoutes récentes</h2>
    </div>

    <div v-if="lastPlaylist.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Dernières playlists</h2>
      <div class="relative">
        <div
          class="flex flex-row justify-start gap-16 overflow-x-auto scrollbar-hide ps-10"
          ref="playlistSlider"
          @scroll="checkScrollEnd"
        >
          <PlaylistPlayable
            v-for="playlist in lastPlaylist"
            :key="playlist.id"
            :playlistId="playlist.id"
            :playlistCover="playlist.cover || albumDefaultIcon"
            :playlistName="playlist.title || 'Playlist sans nom'"
          />
        </div>
        <button
          v-if="showPrevButton"
          class="absolute left-4 top-1/2 p-6 transform -translate-y-1/2 bg-black opacity-70 rounded-full shadow-md hover:opacity-90 transition-opacity"
          @click="scrollPlaylistSliderBack"
        >
          <img :src="arrowLeft" class="w-8 h-8" alt="Flèche gauche" />
        </button>
        <button
          v-if="showNextButton"
          class="absolute right-4 top-1/2 p-6 transform -translate-y-1/2 bg-black opacity-70 rounded-full shadow-md hover:opacity-90 transition-opacity"
          @click="scrollPlaylistSlider"
        >
          <img :src="arrowRight" class="w-8 h-8" alt="Flèche gauche" />
        </button>
      </div>
    </div>

    <div v-if="recommendationList.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">D'après vos écoutes...</h2>
    </div>

    <div v-if="hitsList.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Hits du moment !</h2>
      <!-- Les + écoutés  -->
    </div>

    <div v-if="categoryList.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Pour une envie particulière</h2>
      <!-- Liste des catégories  -->
    </div>

    <div v-if="mostFavoriteList.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Ils font l'unanimité</h2>
      <!-- Les + mis en favoris  -->
    </div>
  </InAppLayout>
</template>
