<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import type { Music, Album } from "@/utils/types";
import { useUserStore } from "@/stores/user";
import { useToast } from "@/composables/useToast";
import { ressourceUrl } from "@/utils/tools";
import defaultCover from "@/assets/images/default-cover.png"; 

const router = useRouter();
const { apiClient } = useApiClient();
const userStore = useUserStore();
const {showError} = useToast();

const albumsCount = ref(0);
const musicsCount = ref(0);
const topMusics = ref<Music[]>([]);
const topAlbum = ref<Album | null>(null);
const topAlbumMusicsCount = ref(0);
const topAlbumTotalListenings = ref(0);

const loading = ref(false);

const getThisArtist = async () => {
  if (!userStore.user) {
    showError("Utilisateur non connecté");
    return router.push("/login");
  }
  try {
    const myArtist = await apiClient.artist.get(userStore.user.id);
    albumsCount.value = myArtist.albums?.length || 0;
    musicsCount.value = myArtist.musics?.length || 0;

    topMusics.value = myArtist.musics?.sort((a, b) =>
      (b.listeningsNumber || 0) - (a.listeningsNumber || 0)
    ).slice(0, 5) || [];

    if (myArtist.albums && myArtist.albums.length > 0) {
      try {
        const fullAlbumsPromises = myArtist.albums.map(album => 
          apiClient.album.get(album.id).catch(error => {
            console.error(`Erreur lors de la récupération de l'album ${album.id}:`, error);
            return album;
          })
        );
        
        const fullAlbums = await Promise.all(fullAlbumsPromises);
        
        const bestAlbum = fullAlbums.reduce((prev, current) => {
          const currentListenings = current.musics?.reduce((sum, music) => sum + (music.listeningsNumber || 0), 0) || 0;
          const prevListenings = prev.musics?.reduce((sum, music) => sum + (music.listeningsNumber || 0), 0) || 0;
          return currentListenings > prevListenings ? current : prev;
        }, fullAlbums[0]);
        
        topAlbum.value = bestAlbum;
        topAlbumMusicsCount.value = bestAlbum.musics?.length || 0;
        topAlbumTotalListenings.value = bestAlbum.musics?.reduce((total, music) => total + (music.listeningsNumber || 0), 0) || 0;
      } catch (error) {
        console.error("Erreur lors de la récupération des albums complets:", error);
        const fallbackAlbum = myArtist.albums[0];
        topAlbum.value = fallbackAlbum;
        topAlbumMusicsCount.value = fallbackAlbum.musics?.length || 0;
        topAlbumTotalListenings.value = fallbackAlbum.musics?.reduce((total, music) => total + (music.listeningsNumber || 0), 0) || 0;
      }
    } else {
      topAlbum.value = null;
      topAlbumMusicsCount.value = 0;
      topAlbumTotalListenings.value = 0;
    }

    return myArtist;
  } catch (error) {
    console.error("Erreur lors de la récupération de l'artiste:", error);
    return null;
  }
};
const goToAlbums = () => {
  router.push("/artist/albums");
};
const goToMusics = () => {
  router.push("/artist/musics");
};
const goToAlbum = (albumId: number) => {
  router.push(`/artist/album/${albumId}`);
};

onMounted(async () => {
  loading.value = true; 
  await getThisArtist();
  loading.value = false;
});
</script>

<template>
  <InAppLayout type="artist" :loading="loading" padding="p-10">
    <h1 class="text-3xl font-bold mb-4">Discographie</h1>
    <p class="mb-8 text-lg text-gray-200">Retrouvez toute votre musique à partir d'ici !</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
      <!-- Albums -->
      <div
        class="bg-[#2E0B40] rounded-lg p-6 shadow cursor-pointer hover:bg-[#3a1452] transition-colors"
        @click="goToAlbums"
      >
        <h2 class="text-xl font-semibold mb-2">Albums</h2>
        <p class="text-gray-300 mb-4">Total des albums publiés.</p>
        <div class="text-3xl font-bold text-yellow-400">
          <span v-if="loading">...</span>
          <span v-else>{{ albumsCount }}</span>
        </div>
        <p class="text-sm text-gray-400 mt-2">
          {{
            albumsCount === 0
              ? "Aucun album"
              : albumsCount === 1
                ? "album publié"
                : "albums publiés"
          }}
        </p>
      </div>

      <!-- Musiques -->
      <div
        class="bg-[#2E0B40] rounded-lg p-6 shadow cursor-pointer hover:bg-[#3a1452] transition-colors"
        @click="goToMusics"
      >
        <h2 class="text-xl font-semibold mb-2">Musiques</h2>
        <p class="text-gray-300 mb-4">Total des musiques publiées.</p>
        <div class="text-3xl font-bold text-red-400">
          <span v-if="loading">...</span>
          <span v-else>{{ musicsCount }}</span>
        </div>
        <p class="text-sm text-gray-400 mt-2">
          {{
            musicsCount === 0
              ? "Aucune musique"
              : musicsCount === 1
                ? "musique publiée"
                : "musiques publiées"
          }}
        </p>
      </div>
    </div>

    <!-- Performances -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Top Musiques -->
      <div class="bg-[#2E0B40] rounded-lg p-6 shadow">
        <h2 class="text-2xl font-semibold mb-4 text-white">🎵 Top Titres</h2>
        <p class="text-gray-300 mb-6">Vos musiques les plus écoutées</p>
        
        <div v-if="topMusics.length === 0" class="text-center py-8">
          <p class="text-gray-400">Aucune musique disponible</p>
        </div>
        
        <div v-else class="space-y-4">
          <div 
            v-for="(music, index) in topMusics" 
            :key="music.id"
            class="flex items-center gap-4 p-3 bg-black/20 rounded-lg hover:bg-black/30 transition-colors"
          >
            <!-- Position -->
            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
              {{ index + 1 }}
            </div>
            
            <!-- Cover -->
            <div class="flex-shrink-0">
              <img 
                :src=" music.cover ? ressourceUrl + music.cover : defaultCover" 
                :alt="music.title"
                class="w-12 h-12 rounded-lg object-cover bg-gray-700"
              />
            </div>
            
            <!-- Info -->
            <div class="flex-grow min-w-0">
              <h4 class="font-medium text-white truncate">{{ music.title }}</h4>
              <p class="text-sm text-gray-400 truncate">
                {{ music.artists?.map(a => a.name).join(', ') || 'Artiste inconnu' }}
              </p>
            </div>
            
            <!-- Stats -->
            <div class="flex-shrink-0 text-right">
              <p class="text-lg font-bold text-green-400">
                {{ music.listeningsNumber || 0 }}
              </p>
              <p class="text-xs text-gray-400">écoutes</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Album -->
      <div class="bg-[#2E0B40] rounded-lg p-6 shadow">
        <h2 class="text-2xl font-semibold mb-4 text-white">🏆 Meilleur Album</h2>
        <p class="text-gray-300 mb-6">Votre album le plus performant</p>
                
        <div v-if="!topAlbum" class="text-center py-8">
          <p class="text-gray-400">Aucun album disponible</p>
        </div>
        
        <div 
          v-else 
          class="text-center cursor-pointer hover:bg-black/10 rounded-lg p-4 transition-colors" 
          @click="goToAlbum(topAlbum.id)"
          title="Cliquez pour voir l'album"
        >
          <!-- Cover de l'album -->
          <div class="mb-6">
            <img 
              :src="topAlbum.cover ? ressourceUrl + topAlbum.cover : defaultCover" 
              :alt="topAlbum.title"
              class="w-48 h-48 mx-auto rounded-2xl object-cover bg-gray-700 shadow-2xl"
            />
          </div>
          
          <!-- Info de l'album -->
          <div class="space-y-3">
            <h3 class="text-2xl font-bold text-white">{{ topAlbum.title }}</h3>
            <p class="text-gray-400">
              Sorti le {{ new Date(topAlbum.releaseDate).toLocaleDateString('fr-FR') }}
            </p>
            
            <!-- Stats de l'album -->
            <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-gray-600">
              <div class="text-center">
                <p class="text-2xl font-bold text-blue-400">{{ topAlbumMusicsCount }}</p>
                <p class="text-sm text-gray-400">titres</p>
              </div>
              <div class="text-center">
                <p class="text-2xl font-bold text-purple-400">
                  {{ topAlbumTotalListenings }}
                </p>
                <p class="text-sm text-gray-400">écoutes totales</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </InAppLayout>
</template>
