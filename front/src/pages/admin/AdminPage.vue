<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";

const router = useRouter();
const { apiClient } = useApiClient();

const requestsCount = ref(0);
const usersCount = ref(0);
const artistsCount = ref(0);
const albumsCount = ref(0);
const playlistsCount = ref(0);

const loadingRequests = ref(true);
const loadingUsers = ref(true);
const loadingArtists = ref(true);
const loadingAlbums = ref(true);
const loadingPlaylists = ref(true);

const fetchRequestsCount = async () => {
  try {
    const response = await apiClient.artistRequest.getAll();
    requestsCount.value = response.totalItems || 0;
  } catch (error) {
    console.error("Erreur lors de la récupération des demandes:", error);
    requestsCount.value = 0;
  } finally {
    loadingRequests.value = false;
  }
};

const fetchUsersCount = async () => {
  try {
    const response = await apiClient.user.getAll();
    usersCount.value = response.totalItems || 0;
  } catch (error) {
    console.error("Erreur lors de la récupération des utilisateurs:", error);
    usersCount.value = 0;
  } finally {
    loadingUsers.value = false;
  }
};

const fetchArtistsCount = async () => {
  try {
    const response = await apiClient.artist.getAll();
    artistsCount.value = response.totalItems || 0;
  } catch (error) {
    console.error("Erreur lors de la récupération des artistes:", error);
    artistsCount.value = 0;
  } finally {
    loadingArtists.value = false;
  }
};

const fetchAlbumsCount = async () => {
  try {
    const response = await apiClient.album.getAll();
    albumsCount.value = response.totalItems || 0;
  } catch (error) {
    console.error("Erreur lors de la récupération des albums:", error);
    albumsCount.value = 0;
  } finally {
    loadingAlbums.value = false;
  }
};

const fetchPlaylistsCount = async () => {
  try {
    const response = await apiClient.playlist.getAll();
    playlistsCount.value = response.length || 0;
  } catch (error) {
    console.error("Erreur lors de la récupération des playlists:", error);
    playlistsCount.value = 0;
  } finally {
    loadingPlaylists.value = false;
  }
};

const goToArtistRequests = () => {
  router.push("/admin/demandes");
};

onMounted(() => {
  fetchRequestsCount();
  fetchUsersCount();
  fetchArtistsCount();
  fetchAlbumsCount();
  fetchPlaylistsCount();
});
</script>

<template>
  <InAppLayout type="admin" padding="p-10">
    <h1 class="text-3xl font-bold mb-4">Administration</h1>
    <p class="mb-8 text-lg text-gray-200">Bienvenue sur la page d'administration de Beatly.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Demandes d'artiste -->
      <div
        class="bg-[#2E0B40] rounded-lg p-6 shadow cursor-pointer hover:bg-[#3a1452] transition-colors"
        @click="goToArtistRequests"
      >
        <h2 class="text-xl font-semibold mb-2">Demandes d'artiste</h2>
        <p class="text-gray-300 mb-4">Gérer les demandes de compte artiste.</p>
        <div class="text-3xl font-bold text-purple-400">
          <span v-if="loadingRequests">...</span>
          <span v-else>{{ requestsCount }}</span>
        </div>
        <p class="text-sm text-gray-400 mt-2">
          {{
            requestsCount === 0
              ? "Aucune demande"
              : requestsCount === 1
                ? "demande en attente"
                : "demandes en attente"
          }}
        </p>
      </div>

      <!-- Utilisateurs -->
      <div class="bg-[#2E0B40] rounded-lg p-6 shadow">
        <h2 class="text-xl font-semibold mb-2">Utilisateurs</h2>
        <p class="text-gray-300 mb-4">Total des comptes utilisateurs.</p>
        <div class="text-3xl font-bold text-blue-400">
          <span v-if="loadingUsers">...</span>
          <span v-else>{{ usersCount }}</span>
        </div>
        <p class="text-sm text-gray-400 mt-2">
          {{
            usersCount === 0
              ? "Aucun utilisateur"
              : usersCount === 1
                ? "utilisateur inscrit"
                : "utilisateurs inscrits"
          }}
        </p>
      </div>

      <!-- Artistes -->
      <div class="bg-[#2E0B40] rounded-lg p-6 shadow">
        <h2 class="text-xl font-semibold mb-2">Artistes</h2>
        <p class="text-gray-300 mb-4">Total des comptes artistes.</p>
        <div class="text-3xl font-bold text-green-400">
          <span v-if="loadingArtists">...</span>
          <span v-else>{{ artistsCount }}</span>
        </div>
        <p class="text-sm text-gray-400 mt-2">
          {{
            artistsCount === 0
              ? "Aucun artiste"
              : artistsCount === 1
                ? "artiste actif"
                : "artistes actifs"
          }}
        </p>
      </div>

      <!-- Albums -->
      <div class="bg-[#2E0B40] rounded-lg p-6 shadow">
        <h2 class="text-xl font-semibold mb-2">Albums</h2>
        <p class="text-gray-300 mb-4">Total des albums publiés.</p>
        <div class="text-3xl font-bold text-yellow-400">
          <span v-if="loadingAlbums">...</span>
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

      <!-- Playlists -->
      <div class="bg-[#2E0B40] rounded-lg p-6 shadow">
        <h2 class="text-xl font-semibold mb-2">Playlists</h2>
        <p class="text-gray-300 mb-4">Total des playlists créées.</p>
        <div class="text-3xl font-bold text-pink-400">
          <span v-if="loadingPlaylists">...</span>
          <span v-else>{{ playlistsCount }}</span>
        </div>
        <p class="text-sm text-gray-400 mt-2">
          {{
            playlistsCount === 0
              ? "Aucune playlist"
              : playlistsCount === 1
                ? "playlist créée"
                : "playlists créées"
          }}
        </p>
      </div>
    </div>
  </InAppLayout>
</template>
