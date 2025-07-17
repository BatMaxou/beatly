<script setup lang="ts">
import { onBeforeMount, ref } from "vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import PlaylistPlayable from "@/components/cards/PlaylistPlayableCard.vue";
import HorizontalScroller from "@/components/ui/HorizontalScroller.vue";
import type { Playlist } from "@/utils/types";

const { apiClient } = useApiClient();

const recentlyListened = ref([]);
const lastPlaylist = ref<Playlist[]>([]);
const recommendationList = ref([]);
const hitsList = ref([]);
const categoryList = ref([]);
const mostFavoriteList = ref([]);
const loading = ref(false);

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
</script>

<template>
  <InAppLayout :loading="loading" padding="pt-10">
    <div v-if="recentlyListened.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Écoutes récentes</h2>
    </div>

    <div v-if="lastPlaylist.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Dernières playlists</h2>
      <HorizontalScroller :gap="64" :scroll-amount="3">
        <PlaylistPlayable
          v-for="playlist in lastPlaylist"
          :key="playlist.id"
          :playlist="playlist"
        />
      </HorizontalScroller>
    </div>

    <div v-if="recommendationList.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">D'après vos écoutes...</h2>
      <HorizontalScroller :gap="48" :scroll-amount="4">
        <div
          v-for="(item, index) in recommendationList"
          :key="index"
          class="flex-shrink-0 w-40 h-40 bg-gray-700 rounded-lg"
        ></div>
      </HorizontalScroller>
    </div>

    <div v-if="hitsList.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Hits du moment !</h2>
      <HorizontalScroller :gap="32" :scroll-amount="5">
        <div
          v-for="(item, index) in hitsList"
          :key="index"
          class="flex-shrink-0 w-32 h-32 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full"
        ></div>
      </HorizontalScroller>
    </div>

    <div v-if="categoryList.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Pour une envie particulière</h2>
      <HorizontalScroller :gap="24" :scroll-amount="6">
        <div
          v-for="(item, index) in categoryList"
          :key="index"
          class="flex-shrink-0 w-36 h-20 bg-blue-600 rounded-lg flex items-center justify-center text-white font-semibold"
        ></div>
      </HorizontalScroller>
    </div>

    <div v-if="mostFavoriteList.length > 0">
      <h2 class="ps-10 text-white text-3xl font-bold mb-4">Ils font l'unanimité</h2>
      <HorizontalScroller :gap="56" :scroll-amount="3">
        <div
          v-for="(item, index) in mostFavoriteList"
          :key="index"
          class="flex-shrink-0 w-44 h-44 bg-yellow-600 rounded-xl"
        ></div>
      </HorizontalScroller>
    </div>
  </InAppLayout>
</template>
