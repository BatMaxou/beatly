<script setup lang="ts">
import { ref, onMounted } from "vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { Music } from "@/utils/types";
import eyeDark from "@/assets/icons/eye-dark.svg";
import removeDark from "@/assets/icons/remove-dark.svg";
import eyeLight from "@/assets/icons/eye-light.svg";
import removeLight from "@/assets/icons/remove-light.svg";
import { useRouter } from "vue-router";
import BackButton from "@/components/navigation/BackButton.vue";
import { ressourceUrl } from "@/utils/tools";
import { convertDurationInMinutes } from "@/sharedFunctions";
import AddMusicModal from "@/components/modals/AddMusicModal.vue";

const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();
const router = useRouter();

const musics = ref<Music[]>([]);
const loading = ref(true);
const showMusicModal = ref(false);
const selectedMusic = ref<Music | null>(null);

const currentPage = ref(1);
const totalItems = ref(0);
const itemsPerPage = ref(30);

const fetchMusics = async (page: number = 1) => {
  loading.value = true;
  try {
    const response = await apiClient.music.getAll(page);
    musics.value = response.member || [];
    totalItems.value = response.totalItems || 0;
    currentPage.value = page;
  } catch (error) {
    console.error("Erreur lors de la récupération des musiques:", error);
    showError("Erreur lors de la récupération des musiques");
    musics.value = [];
  } finally {
    loading.value = false;
  }
};

const viewMusic = (music: Music) => {
  selectedMusic.value = music;
  showMusicModal.value = true;
};

const closeMusicModal = () => {
  showMusicModal.value = false;
  selectedMusic.value = null;
};

const deleteMusic = async (music: Music) => {
  if (!confirm(`Êtes-vous sûr de vouloir supprimer la musique "${music.title}" ?`)) {
    return;
  }

  try {
    if (!music.file) {
      throw new Error("Fichier de la musique introuvable");
    }

    const musicFileId = music.file.split("/").pop();
    if (!musicFileId) {
      throw new Error("ID du fichier introuvable");
    }

    await apiClient.music.deleteFile(musicFileId);
    showSuccess("Musique supprimée avec succès !");
    await fetchMusics();
  } catch (error) {
    console.error("Erreur lors de la suppression:", error);
    showError("Erreur lors de la suppression de la musique");
  }
};

const goToNextPage = () => {
  fetchMusics(currentPage.value + 1);
};

const goToPreviousPage = () => {
  if (currentPage.value > 1) {
    fetchMusics(currentPage.value - 1);
  }
};

onMounted(() => fetchMusics(1));
</script>

<template>
  <InAppLayout type="admin" padding="p-10" :loading="loading">
    <div>
      <BackButton to="/admin" />
      <h1 class="text-3xl font-bold mb-6">Gestion des musiques</h1>

      <div v-if="musics.length === 0" class="text-gray-300">Aucune musique trouvée.</div>
      <div v-else>
        <!-- Pagination info -->
        <div class="mb-4 text-gray-300">
          Page {{ currentPage }} - {{ musics.length }} musiques affichées ({{ totalItems }} au
          total)
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white text-black rounded-lg overflow-hidden">
            <thead>
              <tr class="text-left bg-[#3a1452]">
                <th class="px-4 py-3 text-white">Cover</th>
                <th class="px-4 py-3 text-white">Titre</th>
                <th class="px-4 py-3 text-white">Album</th>
                <th class="px-4 py-3 text-white">Artiste</th>
                <th class="px-4 py-3 text-white">Durée</th>
                <th class="px-4 py-3 text-white">Position</th>
                <th class="px-4 py-3 text-white">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="music in musics"
                :key="music.id"
                class="border-b border-[#440a50] hover:bg-[#39124a] hover:text-white cursor-pointer transition-colors group"
                @click="viewMusic(music)"
              >
                <td class="px-4 py-3">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded overflow-hidden"
                  >
                    <img
                      v-if="music.cover"
                      :src="ressourceUrl + music.cover"
                      :alt="music.title"
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
                <td class="px-4 py-3 font-semibold">{{ music.title || "—" }}</td>
                <td class="px-4 py-3">{{ music.album?.title || "—" }}</td>
                <td class="px-4 py-3">
                  {{ music.mainArtist.name }}
                </td>
                <td class="px-4 py-3">
                  <span v-if="music.duration">
                    {{ convertDurationInMinutes(`${music.duration}`) }}
                  </span>
                  <span v-else class="text-gray-600">—</span>
                </td>
                <td class="px-4 py-3">
                  <span v-if="music.albumPosition" class="text-purple-600 font-medium">
                    {{ music.albumPosition }}
                  </span>
                  <span v-else class="text-gray-600">—</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex space-x-2">
                    <button
                      @click.stop="viewMusic(music)"
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
                      @click.stop="deleteMusic(music)"
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
            :disabled="musics.length < itemsPerPage"
            class="px-4 py-2 bg-[#3a1452] text-white rounded hover:bg-[#4a1562] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Page suivante
          </button>
        </div>
      </div>

      <!-- Music Detail Modal -->
      <AddMusicModal
        :is-visible="showMusicModal"
        :album-id="selectedMusic?.album?.id?.toString() || ''"
        :max-position="0"
        :album-cover="selectedMusic?.cover ? ressourceUrl + selectedMusic.cover : ''"
        :edit-music="selectedMusic"
        :readonly="true"
        @close="closeMusicModal"
        @success="closeMusicModal"
      />
    </div>
  </InAppLayout>
</template>
