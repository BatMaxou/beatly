<script setup lang="ts">
import { onBeforeMount, ref } from "vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import { useUserStore } from "@/stores/user";
import PlaylistPlayable from "@/components/cards/PlaylistPlayableCard.vue";
import AlbumPlayableCard from "@/components/cards/AlbumPlayableCard.vue";
import albumDefaultIcon from "@/assets/icons/disc-dark.svg";
import type { Playlist } from "@/utils/types";

const { apiClient } = useApiClient();
const userStore = useUserStore();

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
    console.error('Erreur lors de la récupération des playlists:', error);
    lastPlaylist.value = [];
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <InAppLayout :loading="loading">
    <!-- Écoutes récentes si il y en a -->
     <div v-if="recentlyListened.length > 0">
      <h2 class="text-white text-3xl font-bold mb-4">Écoutes récentes</h2>
    </div>

    <div v-if="lastPlaylist.length > 0">
      <h2 class="text-white text-3xl font-bold mb-4">Dernières playlists</h2>
      <div class="flex flex-row justify-start gap-16 overflow-x-auto">
        <PlaylistPlayable
          v-for="playlist in lastPlaylist"
          :key="playlist.id"
          :playlistId="playlist.id"
          :playlistCover="playlist.cover || albumDefaultIcon"
          :playlistName="playlist.title || 'Playlist sans nom'"
        />
      </div>
    </div>

    <div v-if="recommendationList.length > 0">
      <h2 class="text-white text-3xl font-bold mb-4">D'après vos écoutes...</h2>
      <AlbumPlayableCard
        :albumCover="albumDefaultIcon"
        albumName="Mon album préféré !"
        artistName="Lomepal"
        releaseYear="2017"
      />
    </div>

    <div v-if="hitsList.length > 0">
      <h2 class="text-white text-3xl font-bold mb-4">Hits du moment !</h2><!-- Les + écoutés  -->
    </div>

    <div v-if="categoryList.length > 0">
      <h2 class="text-white text-3xl font-bold mb-4">Pour une envie particulière</h2><!-- Liste des catégories  -->
    </div>

    <div v-if="mostFavoriteList.length > 0">
      <h2 class="text-white text-3xl font-bold mb-4">Ils font l'unanimité</h2><!-- Les + mis en favoris  -->
    </div>
  </InAppLayout>
</template>
