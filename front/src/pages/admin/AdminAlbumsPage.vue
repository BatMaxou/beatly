<script setup lang="ts">
import { ref, onMounted } from "vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { Album } from "@/utils/types";
import eyeDark from "@/assets/icons/eye-dark.svg";
import removeDark from "@/assets/icons/remove-dark.svg";
import eyeLight from "@/assets/icons/eye-light.svg";
import removeLight from "@/assets/icons/remove-light.svg";
import { useRouter } from "vue-router";
import BackButton from "@/components/navigation/BackButton.vue";
import { ressourceUrl } from "@/utils/tools";

const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();
const router = useRouter();

const albums = ref<Album[]>([]);
const loading = ref(true);

const currentPage = ref(1);
const totalItems = ref(0);
const itemsPerPage = ref(30);

const fetchAlbums = async (page: number = 1) => {
  loading.value = true;
  try {
    const response = await apiClient.album.getAll(page);
    albums.value = response.member || [];
    totalItems.value = response.totalItems || 0;
    currentPage.value = page;
  } catch (error) {
    console.error("Erreur lors de la récupération des albums:", error);
    showError("Erreur lors de la récupération des albums");
    albums.value = [];
  } finally {
    loading.value = false;
  }
};

const viewAlbum = (album: Album) => {
  const albumId = album.id || album["@id"]?.split("/").pop();
  if (albumId) {
    router.push(`/admin/albums/${albumId}?view=true`);
  }
};

const deleteAlbum = async (album: Album) => {
  if (!confirm(`Êtes-vous sûr de vouloir supprimer l'album "${album.title}" ?`)) {
    return;
  }

  try {
    const albumId = album.id || album["@id"]?.split("/").pop();
    if (!albumId) {
      throw new Error("ID de l'album introuvable");
    }

    await apiClient.album.delete(albumId);
    showSuccess("Album supprimé avec succès !");
    await fetchAlbums();
  } catch (error) {
    console.error("Erreur lors de la suppression:", error);
    showError("Erreur lors de la suppression de l'album");
  }
};

const goToNextPage = () => {
  fetchAlbums(currentPage.value + 1);
};

const goToPreviousPage = () => {
  if (currentPage.value > 1) {
    fetchAlbums(currentPage.value - 1);
  }
};

onMounted(() => fetchAlbums(1));
</script>

<template>
  <InAppLayout type="admin" padding="p-10" :loading="loading">
    <div>
      <BackButton to="/admin" />
      <h1 class="text-3xl font-bold mb-6">Gestion des albums</h1>

      <div v-if="albums.length === 0" class="text-gray-300">Aucun album trouvé.</div>
      <div v-else>
        <!-- Pagination info -->
        <div class="mb-4 text-gray-300">
          Page {{ currentPage }} - {{ albums.length }} albums affichés ({{ totalItems }} au total)
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white text-black rounded-lg overflow-hidden">
            <thead>
              <tr class="text-left bg-[#3a1452]">
                <th class="px-4 py-3 text-white hidden sm:table-cell">Cover</th>
                <th class="px-4 py-3 text-white">Titre</th>
                <th class="px-4 py-3 text-white">Artiste</th>
                <th class="px-4 py-3 text-white hidden sm:table-cell">Date de sortie</th>
                <th class="px-4 py-3 text-white">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="album in albums"
                :key="album.id"
                class="border-b border-[#440a50] hover:bg-[#39124a] hover:text-white cursor-pointer transition-colors group"
                @click="viewAlbum(album)"
              >
                <td class="px-4 py-3 hidden sm:table-cell">
                  <div class="w-12 h-12 bg-gray-800 rounded overflow-hidden">
                    <img
                      v-if="album.cover"
                      :src="ressourceUrl + album.cover"
                      :alt="album.title"
                      class="w-full h-full object-cover"
                    />
                    <div
                      v-else
                      class="w-full h-full flex items-center justify-center text-gray-400 text-xs"
                    >
                      Cover
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 font-semibold">{{ album.title || "—" }}</td>
                <td class="px-4 py-3">{{ album.artist?.name || "—" }}</td>
                <td class="px-4 py-3 hidden sm:table-cell">
                  <span v-if="album.releaseDate">
                    {{ new Date(album.releaseDate).toLocaleDateString("fr-FR") }}
                  </span>
                  <span v-else class="text-gray-600">—</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex gap-2">
                    <button
                      @click.stop="viewAlbum(album)"
                      class="p-0 sm:p-2 rounded hover:bg-blue-500 transition-colors"
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
                      @click.stop="deleteAlbum(album)"
                      class="p-0 sm:p-2 rounded hover:bg-red-500 transition-colors"
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
            :disabled="albums.length < itemsPerPage"
            class="px-4 py-2 bg-[#3a1452] text-white rounded hover:bg-[#4a1562] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Page suivante
          </button>
        </div>
      </div>
    </div>
  </InAppLayout>
</template>
