<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useHead } from "@unhead/vue";

import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { Artist } from "@/utils/types";
import eyeDark from "@/assets/icons/eye-dark.svg";
import editDark from "@/assets/icons/edit-dark.svg";
import removeDark from "@/assets/icons/remove-dark.svg";
import eyeLight from "@/assets/icons/eye-light.svg";
import editLight from "@/assets/icons/edit-light.svg";
import removeLight from "@/assets/icons/remove-light.svg";
import { useRouter } from "vue-router";
import BackButton from "@/components/navigation/BackButton.vue";

useHead({
  title: 'Beatly | Gestion des albums',
})

const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();
const router = useRouter();

const artists = ref<Artist[]>([]);
const loading = ref(true);

const fetchArtists = async () => {
  loading.value = true;
  try {
    const response = await apiClient.artist.getAll();
    artists.value = response.member || [];
  } catch (error) {
    console.error("Erreur lors de la récupération des artistes:", error);
    showError("Erreur lors de la récupération des artistes");
    artists.value = [];
  } finally {
    loading.value = false;
  }
};

const viewArtist = (artist: Artist) => {
  const artistId = artist.id || artist["@id"]?.split("/").pop();
  if (artistId) {
    router.push(`/admin/artistes/${artistId}`);
  }
};

const editArtist = (artist: Artist) => {
  const artistId = artist.id || artist["@id"]?.split("/").pop();
  if (artistId) {
    router.push(`/admin/artistes/${artistId}?edit=true`);
  }
};

const deleteArtist = async (artist: Artist) => {
  if (!confirm(`Êtes-vous sûr de vouloir supprimer l'artiste "${artist.name}" ?`)) {
    return;
  }

  try {
    const artistId = artist.id || artist["@id"]?.split("/").pop();
    if (!artistId) {
      throw new Error("ID de l'artiste introuvable");
    }

    await apiClient.user.delete(artistId);
    showSuccess("Artiste supprimé avec succès !");
    await fetchArtists();
  } catch (error) {
    console.error("Erreur lors de la suppression:", error);
    showError("Erreur lors de la suppression de l'artiste");
  }
};

onMounted(fetchArtists);
</script>

<template>
  <InAppLayout type="admin" padding="p-10" :loading="loading">
    <div>
      <BackButton to="/admin" />
      <h1 class="text-3xl font-bold mb-6">Gestion des artistes</h1>

      <div v-if="artists.length === 0" class="text-gray-300">Aucun artiste trouvé.</div>
      <div v-else class="overflow-x-auto">
        <table class="min-w-full bg-white text-black rounded-lg overflow-hidden">
          <thead>
            <tr class="text-left bg-[#3a1452]">
              <th class="px-4 py-3 text-white">Nom</th>
              <th class="px-4 py-3 text-white">Email</th>
              <th class="px-4 py-3 text-white">Albums</th>
              <th class="px-4 py-3 text-white">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="artist in artists"
              :key="artist.id"
              class="border-b border-[#440a50] hover:bg-[#39124a] hover:text-white cursor-pointer transition-colors group"
              @click="viewArtist(artist)"
            >
              <td class="px-4 py-3 font-semibold">{{ artist.name || "—" }}</td>
              <td class="px-4 py-3">{{ artist.email || "—" }}</td>
              <td class="px-4 py-3">
                <span
                  v-if="artist.albums && artist.albums.length"
                  class="text-purple-600 font-medium"
                >
                  {{ artist.albums.length }} album{{ artist.albums.length > 1 ? "s" : "" }}
                </span>
                <span v-else class="text-gray-600 text-xs">Aucun</span>
              </td>
              <td class="px-4 py-3">
                <div class="flex space-x-2">
                  <button
                    @click.stop="viewArtist(artist)"
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
                    @click.stop="editArtist(artist)"
                    class="p-2 rounded hover:bg-yellow-500 transition-colors"
                    title="Éditer"
                  >
                    <img :src="editDark" :class="['w-5 h-5', 'group-hover:hidden']" alt="Éditer" />
                    <img
                      :src="editLight"
                      :class="['w-5 h-5', 'hidden group-hover:block']"
                      alt="Éditer"
                    />
                  </button>
                  <button
                    @click.stop="deleteArtist(artist)"
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
    </div>
  </InAppLayout>
</template>
