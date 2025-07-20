<script setup lang="ts">
import { ref, onMounted } from "vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { Playlist } from "@/utils/types";
import eyeDark from "@/assets/icons/eye-dark.svg";
import removeDark from "@/assets/icons/remove-dark.svg";
import eyeLight from "@/assets/icons/eye-light.svg";
import removeLight from "@/assets/icons/remove-light.svg";
import { useRouter } from "vue-router";
import BackButton from "@/components/navigation/BackButton.vue";
import { ressourceUrl } from "@/utils/tools";
import CreatePlaylistModal from "@/components/modals/CreatePlaylistModal.vue";

const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();
const router = useRouter();

const playlists = ref<Playlist[]>([]);
const loading = ref(true);
const showCreateModal = ref(false);

const currentPage = ref(1);
const totalItems = ref(0);
const itemsPerPage = ref(30);

const fetchPlaylists = async (page: number = 1) => {
  loading.value = true;
  try {
    const response = await apiClient.playlist.getAll(page);
    playlists.value = response.member || [];
    totalItems.value = response.totalItems || 0;
    currentPage.value = page;
  } catch (error) {
    console.error("Erreur lors de la récupération des playlists:", error);
    showError("Erreur lors de la récupération des playlists");
    playlists.value = [];
  } finally {
    loading.value = false;
  }
};

const viewPlaylist = (playlist: Playlist) => {
  const playlistId = playlist.id || playlist["@id"]?.split("/").pop();
  if (playlistId) {
    router.push(`/admin/playlists/${playlistId}`);
  }
};

const deletePlaylist = async (playlist: Playlist) => {
  if (!confirm(`Êtes-vous sûr de vouloir supprimer la playlist "${playlist.title}" ?`)) {
    return;
  }

  try {
    const playlistId = playlist.id || playlist["@id"]?.split("/").pop();
    if (!playlistId) {
      throw new Error("ID de la playlist introuvable");
    }

    await apiClient.playlist.delete(playlistId);
    showSuccess("Playlist supprimée avec succès !");
    await fetchPlaylists(currentPage.value);
  } catch (error) {
    console.error("Erreur lors de la suppression:", error);
    showError("Erreur lors de la suppression de la playlist");
  }
};

const goToNextPage = () => {
  fetchPlaylists(currentPage.value + 1);
};

const goToPreviousPage = () => {
  if (currentPage.value > 1) {
    fetchPlaylists(currentPage.value - 1);
  }
};

const openCreateModal = () => {
  showCreateModal.value = true;
};

const closeCreateModal = () => {
  showCreateModal.value = false;
};

const handlePlaylistCreated = async () => {
  showCreateModal.value = false;
  await fetchPlaylists(currentPage.value);
};

onMounted(() => fetchPlaylists(1));
</script>

<template>
  <InAppLayout type="admin" padding="p-10" :loading="loading">
    <div>
      <BackButton to="/admin" />
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Gestion des playlists</h1>

        <button
          @click="openCreateModal"
          class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all flex items-center space-x-2"
        >
          <span class="text-lg">+</span>
          <span>Créer une playlist</span>
        </button>
      </div>

      <div v-if="playlists.length === 0" class="text-gray-300">Aucune playlist trouvée.</div>
      <div v-else>
        <!-- Pagination info -->
        <div class="mb-4 text-gray-300">
          Page {{ currentPage }} - {{ playlists.length }} playlists affichées ({{ totalItems }} au
          total)
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white text-black rounded-lg overflow-hidden">
            <thead>
              <tr class="text-left bg-[#3a1452]">
                <th class="px-4 py-3 text-white">Cover</th>
                <th class="px-4 py-3 text-white">Nom</th>
                <th class="px-4 py-3 text-white">Créateur</th>
                <th class="px-4 py-3 text-white">Type</th>
                <th class="px-4 py-3 text-white">Musiques</th>
                <th class="px-4 py-3 text-white">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="playlist in playlists"
                :key="playlist.id"
                class="border-b border-[#440a50] hover:bg-[#39124a] hover:text-white cursor-pointer transition-colors group"
                @click="viewPlaylist(playlist)"
              >
                <td class="px-4 py-3">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded overflow-hidden"
                  >
                    <img
                      v-if="playlist.cover"
                      :src="ressourceUrl + playlist.cover"
                      :alt="playlist.title"
                      class="w-full h-full object-cover"
                    />
                    <div
                      v-else
                      class="w-full h-full flex items-center justify-center text-white text-xs"
                    >
                      Cover
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 font-semibold">{{ playlist.title || "—" }}</td>
                <td class="px-4 py-3">{{ playlist.creator?.name || "—" }}</td>
                <td class="px-4 py-3">
                  <span
                    v-if="playlist['@type']"
                    :class="[
                      'px-2 py-1 rounded text-xs font-medium',
                      playlist['@type'] === 'Playlist'
                        ? 'bg-blue-100 text-blue-800'
                        : 'bg-purple-100 text-purple-800',
                    ]"
                  >
                    {{ playlist["@type"] }}
                  </span>
                  <span v-else class="text-gray-600">—</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span
                    v-if="playlist.musics && playlist.musics.length"
                    class="text-purple-600 font-medium"
                  >
                    {{ playlist.musics.length }}
                  </span>
                  <span v-else class="text-gray-600 text-xs">Aucune</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex space-x-2">
                    <button
                      @click.stop="viewPlaylist(playlist)"
                      class="p-2 rounded hover:bg-blue-500 transition-colors"
                      title="Consulter"
                    >
                      <img :src="eyeDark" :class="['w-5 h-5', 'group-hover:hidden']" alt="Voir" />
                      <img
                        :src="eyeLight"
                        :class="['w-5 h-5', 'hidden group-hover:block']"
                        alt="Voir"
                      />
                    </button>
                    <button
                      @click.stop="deletePlaylist(playlist)"
                      class="p-2 rounded hover:bg-red-500 transition-colors"
                      title="Supprimer"
                    >
                      <img
                        :src="removeDark"
                        :class="['w-5 h-5', 'group-hover:hidden']"
                        alt="Supprimer"
                      />
                      <img
                        :src="removeLight"
                        :class="['w-5 h-5', 'hidden group-hover:block']"
                        alt="Supprimer"
                      />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination controls -->
        <div class="mt-6 flex justify-center items-center space-x-4">
          <button
            @click="goToPreviousPage"
            :disabled="currentPage === 1"
            class="px-4 py-2 bg-[#3a1452] text-white rounded hover:bg-[#4a1562] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Page précédente
          </button>

          <span class="text-white">Page {{ currentPage }}</span>

          <button
            @click="goToNextPage"
            :disabled="playlists.length < itemsPerPage"
            class="px-4 py-2 bg-[#3a1452] text-white rounded hover:bg-[#4a1562] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Page suivante
          </button>
        </div>
      </div>

      <!-- Create Playlist Modal -->
      <CreatePlaylistModal
        :is-visible="showCreateModal"
        @close="closeCreateModal"
        @success="handlePlaylistCreated"
      />
    </div>
  </InAppLayout>
</template>
