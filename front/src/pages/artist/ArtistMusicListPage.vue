<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import type { Music } from "@/utils/types";
import { useUserStore } from "@/stores/user";
import { useToast } from "@/composables/useToast";
import { ressourceUrl } from "@/utils/tools";
import defaultCover from "@/assets/images/default-cover.png";
import BackButton from "@/components/navigation/BackButton.vue";
import AddMusicModal from "@/components/modals/AddMusicModal.vue";

const router = useRouter();
const { apiClient } = useApiClient();
const userStore = useUserStore();
const { showError } = useToast();

const musics = ref<Music[]>([]);
const loading = ref(false);
const showAddMusicModal = ref(false);
const selectedMusic = ref<Music | null>(null);

const fetchMyMusics = async () => {
  if (!userStore.user) {
    showError("Utilisateur non connecté");
    return router.push("/login");
  }
  
  try {
    const myArtist = await apiClient.artist.get(userStore.user.id);
    const artistMusics = myArtist.musics || [];
    
    musics.value = artistMusics.sort((a, b) => 
      (b.listeningsNumber || 0) - (a.listeningsNumber || 0)
    );
  } catch (error) {
    console.error("Erreur lors de la récupération des musiques:", error);
    showError("Erreur lors de la récupération des musiques");
    musics.value = [];
  }
};

const formatDuration = (seconds: number): string => {
  if (!seconds) return "0:00";
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const getTotalDuration = (): number => {
  return musics.value.reduce((total, music) => total + (music.duration || 0), 0);
};

const getTotalListenings = (): number => {
  return musics.value.reduce((total, music) => total + (music.listeningsNumber || 0), 0);
};

onMounted(async () => {
  loading.value = true;
  await fetchMyMusics();
  loading.value = false;
});

const goToMusic = (musicId: number) => {
  const music = musics.value.find(m => m.id === musicId);
  if (music) {
    selectedMusic.value = music;
    showAddMusicModal.value = true;
  }
};

const closeAddMusicModal = () => {
  showAddMusicModal.value = false;
  selectedMusic.value = null;
};

const handleMusicAdded = async () => {
  closeAddMusicModal();
  await fetchMyMusics();
};

const openAddMusicModal = () => {
  selectedMusic.value = null;
  showAddMusicModal.value = true;
};
</script>

<template>
  <InAppLayout type="artist" :loading="loading" padding="p-10">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
      <BackButton
        to="/artist"
        label="Retour au dashboard"
      />
    </div>

    <div class="mb-8 flex justify-between items-center">
      <div>
        <h1 class="text-4xl font-bold mb-4">Mes Musiques</h1>
        <p class="text-lg text-gray-300">
          Gérez et consultez toutes vos musiques publiées
        </p>
      </div>
      <button
        @click="openAddMusicModal"
        class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all flex items-center space-x-2"
      >
        <span class="text-lg">+</span>
        <span>Ajouter une musique</span>
      </button>
    </div>

    <!-- Statistiques générales -->
    <div v-if="musics.length > 0" class="mb-16 bg-[#2E0B40] rounded-xl p-8">
      <h2 class="text-2xl font-bold text-white mb-6">📊 Statistiques générales</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="text-center p-4 bg-black/20 rounded-lg">
          <p class="text-3xl font-bold text-yellow-400 mb-2">{{ musics.length }}</p>
          <p class="text-gray-300">Musiques publiées</p>
        </div>
        
        <div class="text-center p-4 bg-black/20 rounded-lg">
          <p class="text-3xl font-bold text-blue-400 mb-2">
            {{ formatDuration(getTotalDuration()) }}
          </p>
          <p class="text-gray-300">Durée totale</p>
        </div>
        
        <div class="text-center p-4 bg-black/20 rounded-lg">
          <p class="text-3xl font-bold text-green-400 mb-2">
            {{ getTotalListenings() }}
          </p>
          <p class="text-gray-300">Écoutes totales</p>
        </div>

        <div class="text-center p-4 bg-black/20 rounded-lg">
          <p class="text-3xl font-bold text-purple-400 mb-2">
            {{ musics.length > 0 ? Math.round(getTotalListenings() / musics.length) : 0 }}
          </p>
          <p class="text-gray-300">Écoutes moyennes</p>
        </div>
      </div>
    </div>

    <!-- Message si aucune musique -->
    <div v-if="musics.length === 0 && !loading" class="text-center py-16">
      <div class="mb-6">
        <svg class="w-24 h-24 mx-auto text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
        </svg>
      </div>
      <h3 class="text-2xl font-semibold text-gray-400 mb-2">Aucune musique trouvée</h3>
      <p class="text-gray-500 mb-8">Vous n'avez pas encore publié de musiques.</p>
    </div>

    <!-- Liste des musiques -->
    <div v-else class="space-y-4">
      <div 
        v-for="(music, index) in musics" 
        :key="music.id"
        @click="goToMusic(music.id)"
        class="group cursor-pointer bg-[#2E0B40] rounded-xl p-6 shadow-lg hover:shadow-2xl hover:bg-[#3a1452] transition-all duration-300"
      >
        <div class="flex items-center gap-6">
          <!-- Position dans le classement -->
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
              {{ index + 1 }}
            </div>
          </div>

          <!-- Cover -->
          <div class="flex-shrink-0">
            <img 
              :src="music.cover ? ressourceUrl + music.cover : defaultCover" 
              :alt="music.title"
              class="w-20 h-20 rounded-xl object-cover bg-gray-700 transition-transform duration-300 group-hover:scale-110"
            />
          </div>

          <!-- Informations principales -->
          <div class="flex-grow min-w-0 space-y-2">
            <h3 class="text-xl font-bold text-white group-hover:text-purple-300 transition-colors line-clamp-1">
              {{ music.title }}
            </h3>
            
            <p class="text-gray-400">
              {{ music.album.title }}
            </p>

            <div class="flex items-center gap-4 text-sm text-gray-500">
              <span v-if="music.album?.releaseDate">
                Sorti le {{ new Date(music.album.releaseDate).toLocaleDateString('fr-FR') }}
              </span>
            </div>
          </div>

          <!-- Statistiques -->
          <div class="flex-shrink-0 text-right space-y-2">
            <div class="flex items-center gap-6">
              <!-- Durée -->
              <div class="text-center">
                <p class="text-lg font-bold text-blue-400">
                  {{ formatDuration(music.duration || 0) }}
                </p>
                <p class="text-xs text-gray-400">durée</p>
              </div>

              <!-- Écoutes -->
              <div class="text-center">
                <p class="text-lg font-bold text-green-400">
                  {{ music.listeningsNumber || 0 }}
                </p>
                <p class="text-xs text-gray-400">écoutes</p>
              </div>
            </div>
          </div>

          <!-- Icône de lecture -->
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center group-hover:bg-white/20 transition-colors">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modale d'édition -->
    <AddMusicModal
      :is-visible="showAddMusicModal"
      :album-id="selectedMusic?.album?.id?.toString() || '0'"
      :max-position="selectedMusic?.albumPosition || 0"
      :album-cover="selectedMusic?.album?.cover ? ressourceUrl + selectedMusic?.album?.cover : defaultCover"
      :edit-music="selectedMusic"
      @close="closeAddMusicModal"
      @success="handleMusicAdded"
    />
  </InAppLayout>
</template>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
