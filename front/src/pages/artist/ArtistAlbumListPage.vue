<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import type { Album } from "@/utils/types";
import { useUserStore } from "@/stores/user";
import { useToast } from "@/composables/useToast";
import { ressourceUrl } from "@/utils/tools";
import defaultCover from "@/assets/images/default-cover.png";
import BackButton from "@/components/navigation/BackButton.vue";
import CreateAlbumModal from "@/components/modals/CreateAlbumModal.vue";

const router = useRouter();
const { apiClient } = useApiClient();
const userStore = useUserStore();
const { showError } = useToast();

const albums = ref<Album[]>([]);
const loading = ref(false);
const loadingStats = ref(false);
const showCreateModal = ref(false);

const fetchMyAlbums = async () => {
  if (!userStore.user) {
    showError("Utilisateur non connecté");
    return router.push("/login");
  }
  
  try {
    const myArtist = await apiClient.artist.get(userStore.user.id);
    const artistAlbums = myArtist.albums || [];
    
    albums.value = artistAlbums;
    
    if (artistAlbums.length > 0) {
      loadingStats.value = true;
      
      const fullAlbumsPromises = artistAlbums.map(album => 
        apiClient.album.get(album.id).catch(error => {
          console.error(`Erreur lors de la récupération de l'album ${album.id}:`, error);
          return album;
        })
      );
      
      const fullAlbums = await Promise.all(fullAlbumsPromises);
      albums.value = fullAlbums;
      loadingStats.value = false;
    }
  } catch (error) {
    console.error("Erreur lors de la récupération des albums:", error);
    showError("Erreur lors de la récupération des albums");
    albums.value = [];
    loadingStats.value = false;
  }
};

const goToAlbum = (albumId: number) => {
  router.push(`/artist/album/${albumId}`);
};

const openCreateModal = () => {
  showCreateModal.value = true;
};

const closeCreateModal = () => {
  showCreateModal.value = false;
};

const handleAlbumCreated = async () => {
  closeCreateModal();
  await fetchMyAlbums();
};

onMounted(async () => {
  loading.value = true;
  await fetchMyAlbums();
  loading.value = false;
});
</script>

<template>
  <InAppLayout type="artist" :loading="loading" padding="p-10">
    <!-- Header avec bouton retour -->
    <div class="flex items-center gap-4 mb-8">
      <BackButton
        to="/artist"
        label="Retour au dashboard"
      />
    </div>

    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-4xl font-bold mb-4">Mes Albums</h1>
        <p class="text-lg text-gray-300">
          Gérez et consultez tous vos albums publiés
        </p>
      </div>
      <button
        @click="openCreateModal"
        class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all flex items-center space-x-2"
      >
        <span class="text-lg">+</span>
        <span>Créer un album</span>
      </button>
    </div>

    <!-- Statistiques générales -->
    <div v-if="albums.length > 0" class="mb-16 bg-[#2E0B40] rounded-xl p-8">
      <h2 class="text-2xl font-bold text-white mb-6">📊 Statistiques générales</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="text-center p-4 bg-black/20 rounded-lg">
          <p class="text-3xl font-bold text-yellow-400 mb-2">{{ albums.length }}</p>
          <p class="text-gray-300">Albums publiés</p>
        </div>
        
        <div class="text-center p-4 bg-black/20 rounded-lg">
          <p class="text-3xl font-bold text-blue-400 mb-2">
            <span v-if="loadingStats">...</span>
            <span v-else>{{ albums.reduce((total, album) => total + (album.musics?.length || 0), 0) }}</span>
          </p>
          <p class="text-gray-300">Titres au total</p>
        </div>
        
        <div class="text-center p-4 bg-black/20 rounded-lg">
          <p class="text-3xl font-bold text-green-400 mb-2">
            <span v-if="loadingStats">...</span>
            <span v-else>{{ albums.reduce((total, album) => total + (album.musics?.reduce((sum, music) => sum + (music.listeningsNumber || 0), 0) || 0), 0) }}</span>
          </p>
          <p class="text-gray-300">Écoutes totales</p>
        </div>
      </div>
    </div>

    <!-- Albums Grid -->
    <div v-if="albums.length === 0 && !loading" class="text-center py-16">
      <div class="mb-6">
        <svg class="w-24 h-24 mx-auto text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
        </svg>
      </div>
      <h3 class="text-2xl font-semibold text-gray-400 mb-2">Aucun album trouvé</h3>
      <p class="text-gray-500 mb-8">Vous n'avez pas encore publié d'albums.</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
      <div 
        v-for="album in albums" 
        :key="album.id"
        @click="goToAlbum(album.id)"
        class="group cursor-pointer bg-[#2E0B40] rounded-2xl p-6 shadow-lg hover:shadow-2xl hover:bg-[#3a1452] transition-all duration-300 transform hover:-translate-y-2"
      >
        <!-- Cover de l'album -->
        <div class="relative mb-6 overflow-hidden rounded-xl">
          <img 
            :src="album.cover ? ressourceUrl + album.cover : defaultCover" 
            :alt="album.title"
            class="w-full aspect-square object-cover transition-transform duration-300 group-hover:scale-110"
          />
        </div>

        <!-- Info de l'album -->
        <div class="space-y-3">
          <h3 class="text-xl font-bold text-white group-hover:text-purple-300 transition-colors line-clamp-2">
            {{ album.title }}
          </h3>
          
          <p class="text-gray-400 text-sm">
            Sorti le {{ new Date(album.releaseDate).toLocaleDateString('fr-FR') }}
          </p>

          <!-- Statistiques -->
          <div class="flex items-center justify-between pt-4 border-t border-gray-600">
            <div class="flex items-center gap-4 text-sm">
              <span class="text-blue-400 font-medium">
                <span v-if="loadingStats">...</span>
                <span v-else>{{ album.musics?.length || 0 }} titres</span>
              </span>
              <span class="text-green-400 font-medium">
                <span v-if="loadingStats">...</span>
                <span v-else>{{ album.musics?.reduce((total, music) => total + (music.listeningsNumber || 0), 0) || 0 }} écoutes</span>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <CreateAlbumModal
      :is-visible="showCreateModal"
      @close="closeCreateModal"
      @success="handleAlbumCreated"
    />
  </InAppLayout>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
