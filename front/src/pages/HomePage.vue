<script setup lang="ts">
import { onBeforeMount, ref } from "vue";
import { storeToRefs } from "pinia";

import { useHead } from "@unhead/vue";

import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import { useRecommendationStore } from "@/stores/recommendation";
import PlaylistPlayable from "@/components/cards/PlaylistPlayableCard.vue";
import AlbumPlayableCard from "@/components/cards/AlbumPlayableCard.vue";
import HorizontalScroller from "@/components/ui/HorizontalScroller.vue";
import type { Album, Music, Playlist, LastListened } from "@/utils/types";
import loadingIcon from "@/assets/icons/loading-light.svg";

useHead({
  title: "Beatly | Accueil",
});

const { apiClient } = useApiClient();
const recommendationStore = useRecommendationStore();
const { recommendations } = storeToRefs(recommendationStore);
const { setRecommendations } = recommendationStore;

const lastListened = ref<LastListened<Music | Album | Playlist>[]>([]);
const mostListenedMusics = ref<Music[]>([]);
const mostLikedMusics = ref<Music[]>([]);
const mostLikedPlaylists = ref<Playlist[]>([]);
const loading = ref(false);
const recommendationLoading = ref(false);

onBeforeMount(async () => {
  loading.value = true;

  if (recommendations.value.length > 0) {
    recommendationLoading.value = false;
  } else {
    recommendationLoading.value = true;
  }

  try {
    const dashboardResponse = await apiClient.dashboard.get();
    if (dashboardResponse) {
      lastListened.value = dashboardResponse.lastListened;
      mostListenedMusics.value = dashboardResponse.mostListenedMusics;
      mostLikedMusics.value = dashboardResponse.mostLikedMusics;
      mostLikedPlaylists.value = dashboardResponse.mostLikedPlaylists;
    }

    if (recommendations.value.length === 0) {
      apiClient.dashboard
        .getRecommendations()
        .then((recommendationResponse) => {
          setRecommendations(recommendationResponse.recommendations || []);
          recommendationLoading.value = false;
        })
        .catch((error) => {
          console.error("Erreur lors de la récupération des recommandations:", error);
        });
    }
  } catch (error) {
    console.error("Erreur lors de la récupération du dashboard:", error);
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <InAppLayout :loading="loading" padding="pt-10">
    <div class="flex flex-col gap-8">
      <div v-if="lastListened.length > 0">
        <h2 class="ps-10 text-white text-3xl font-bold mb-4">Écoutes récentes</h2>
        <HorizontalScroller :gap="32" :scroll-amount="3">
          <div v-for="item in lastListened" :key="item.target['@id']">
            <PlaylistPlayable
              v-if="(item.target as Playlist)['@type'] === 'Playlist'"
              :playlist="item.target as Playlist"
            />
            <AlbumPlayableCard
              v-else-if="(item.target as Album)['@type'] === 'Album'"
              :album="item.target as Album"
              :artistName="(item.target as Album).artist?.name"
              :releaseYear="new Date((item.target as Album).releaseDate).getFullYear()"
            />
            <AlbumPlayableCard
              v-else-if="(item.target as Music)['@type'] === 'Music'"
              :music="item.target as Music"
              :artistName="(item.target as Music).album.artist?.name"
              type="single"
            />
          </div>
        </HorizontalScroller>
      </div>

      <div v-if="mostLikedPlaylists.length > 0">
        <h2 class="ps-10 text-white text-3xl font-bold mb-4">Les meilleures créations</h2>
        <HorizontalScroller :gap="32" :scroll-amount="3">
          <PlaylistPlayable
            v-for="playlist in mostLikedPlaylists"
            :key="playlist.id"
            :playlist="playlist"
          />
        </HorizontalScroller>
      </div>

      <div>
        <h2
          v-if="!recommendationLoading && recommendations.length > 0"
          class="ps-10 text-white text-3xl font-bold mb-4"
        >
          D'après vos écoutes...
        </h2>
        <img
          v-if="recommendationLoading"
          :src="loadingIcon"
          alt="Chargement"
          class="h-12 w-12 animate-spin mb-4 mx-auto"
        />
        <HorizontalScroller v-if="recommendations.length > 0" :gap="32" :scroll-amount="3">
          <AlbumPlayableCard
            v-for="(item, index) in recommendations"
            :key="index"
            :music="item"
            :artistName="item.album.artist?.name"
            type="single"
          />
        </HorizontalScroller>
      </div>

      <div v-if="mostListenedMusics.length > 0">
        <h2 class="ps-10 text-white text-3xl font-bold mb-4">Hits du moment !</h2>
        <HorizontalScroller :gap="32" :scroll-amount="3">
          <div v-for="item in mostListenedMusics" :key="item.id">
            <AlbumPlayableCard :music="item" :artistName="item.album.artist?.name" type="single" />
          </div>
        </HorizontalScroller>
      </div>

      <div v-if="mostLikedMusics.length > 0">
        <h2 class="ps-10 text-white text-3xl font-bold mb-4">Ils font l'unanimité</h2>
        <HorizontalScroller :gap="32" :scroll-amount="3">
          <div v-for="item in mostLikedMusics" :key="item.id">
            <AlbumPlayableCard :music="item" :artistName="item.album.artist?.name" type="single" />
          </div>
        </HorizontalScroller>
      </div>
    </div>
  </InAppLayout>
</template>
