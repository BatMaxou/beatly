<script setup lang="ts">
import { onBeforeMount, ref } from "vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import PlaylistPlayable from "@/components/cards/PlaylistPlayableCard.vue";
import AlbumPlayableCard from "@/components/cards/AlbumPlayableCard.vue";
import HorizontalScroller from "@/components/ui/HorizontalScroller.vue";
import type { Album, Music, Playlist, LastListened, Recommendation } from "@/utils/types";

const { apiClient } = useApiClient();

const lastListened = ref<LastListened<Music | Album | Playlist>[]>([]);
const recommendationList = ref<Recommendation[]>([]);
const mostListenedMusics = ref<Music[]>([]);
const mostLikedMusics = ref<Music[]>([]);
const mostLikedPlaylists = ref<Playlist[]>([]);
const loading = ref(false);

onBeforeMount(async () => {
  loading.value = true;
  try {
    const dashboardResponse = await apiClient.dashboard.get();
    if (dashboardResponse) {
      lastListened.value = dashboardResponse.lastListened;
      mostListenedMusics.value = dashboardResponse.mostListenedMusics;
      mostLikedMusics.value = dashboardResponse.mostLikedMusics;
      mostLikedPlaylists.value = dashboardResponse.mostLikedPlaylists;
    }
    // const recommendationsResponse = await apiClient.dashboard.getRecommendations();
    // if (recommendationsResponse) {
    //   recommendationList.value = recommendationsResponse;
    // }
  } catch (error) {
    console.error("Erreur lors de la récupération des playlists:", error);
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

      <div v-if="recommendationList.length > 0">
        <h2 class="ps-10 text-white text-3xl font-bold mb-4">D'après vos écoutes...</h2>
        <HorizontalScroller :gap="32" :scroll-amount="3">
          <div
            v-for="(item, index) in recommendationList"
            :key="index"
            class="flex-shrink-0 w-40 h-40 bg-gray-700 rounded-lg"
          ></div>
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
