<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, computed } from "vue";
import { useApiClient } from "@/stores/api-client";
import { useModalsStore } from "@/stores/modals";
import { useToast } from "@/composables/useToast";
import type { Album, Music, Playlist } from "@/utils/types";
import type { CollectionResponse } from "@/stores/api-client/model";
import defaultCover from "@/assets/images/default-cover.png";

const props = defineProps<{
  isVisible: boolean;
  element?: Music | Album | Playlist;
}>();

const { apiClient } = useApiClient();
const modalsStore = useModalsStore();
const { showSuccess, showError } = useToast();
const playlists = ref<CollectionResponse<Playlist> | null>(null);
const loading = ref(false);
const showCreateForm = ref(false);
const formData = ref({ name: "" });
const ressourceUrl = import.meta.env.VITE_API_RESSOURCES_URL;

const fetchUserPlaylists = async () => {
  loading.value = true;
  try {
    const response = await apiClient.me.getPlaylists();
    playlists.value = response || [];
  } catch (error) {
    console.error("Erreur lors du chargement des playlists:", error);
    playlists.value = null;
  } finally {
    loading.value = false;
  }
};

const filteredPlaylists = computed(() => {
  if (!playlists.value?.member) return [];

  if (props.element?.["@type"] === "Playlist") {
    const currentPlaylistId = (props.element as Playlist).id;
    return playlists.value.member.filter((playlist) => playlist.id !== currentPlaylistId);
  }

  return playlists.value.member;
});

// Ajout a la playlist
const addToPlaylist = async (playlist: Playlist, musicsToAdd?: string[]) => {
  try {
    if (!props.element?.["@id"]) {
      showError("Impossible d'ajouter cet élément");
      return;
    }

    const thisPlaylist = await apiClient.playlist.get(playlist.id);
    const existingMusics = thisPlaylist.musics || [];

    let musicsToAddToPlaylist: string[] = [];

    if (musicsToAdd) {
      musicsToAddToPlaylist = musicsToAdd;
    } else {
      if (props.element["@type"] === "Music") {
        musicsToAddToPlaylist = [props.element["@id"]];
      } else if (props.element["@type"] === "Album") {
        musicsToAddToPlaylist = (props.element as Album).musics.map((music) => music["@id"]);
      } else if (props.element["@type"] === "Playlist") {
        musicsToAddToPlaylist = (props.element as Playlist).musics.map(
          (playlistMusic) => playlistMusic.music["@id"],
        );
      }

      // Détection des doublons pour les playlists existantes
      const existingMusicIds = existingMusics.map((playlistMusic) => playlistMusic.music["@id"]);

      const duplicateMusics = musicsToAddToPlaylist.filter((musicId) =>
        existingMusicIds.includes(musicId),
      );
      const newMusics = musicsToAddToPlaylist.filter(
        (musicId) => !existingMusicIds.includes(musicId),
      );

      if (newMusics.length === 0) {
        if (duplicateMusics.length === 1) {
          showError("Ce titre est déjà présent dans la playlist");
        } else {
          showError("Les titres sont déjà présents dans la playlist");
        }
        return;
      }

      musicsToAddToPlaylist = newMusics;
    }

    const addToPlaylistPayload = {
      title: thisPlaylist.title,
      musics: [
        ...existingMusics.map((playlistMusic) => playlistMusic["@id"]),
        ...musicsToAddToPlaylist.map((musicId) => ({ music: musicId })),
      ],
    };

    console.log("addToPlaylistPayload", addToPlaylistPayload);
    const response = await apiClient.playlist.update(playlist.id, addToPlaylistPayload as any);

    if (response) {
      if (musicsToAddToPlaylist.length === 1) {
        showSuccess(`Ajouté à la playlist "${playlist.title}"`);
      } else {
        showSuccess(
          `${musicsToAddToPlaylist.length} titres ajoutés à la playlist "${playlist.title}"`,
        );
      }
    } else {
      showError("Erreur lors de l'ajout à la playlist");
    }
  } catch (error) {
    console.error("Erreur lors de l'ajout à la playlist:", error);
    showError("Erreur lors de l'ajout à la playlist");
    throw error;
  }
};

const createNewPlaylist = () => {
  showCreateForm.value = true;
};

const backToList = () => {
  showCreateForm.value = false;
  formData.value.name = "";
};

const submitCreatePlaylist = async (data: { name: string }) => {
  try {
    const newPlaylist = await apiClient.playlist.create({
      title: data.name.trim(),
    });

    showSuccess(`Playlist "${data.name}" créée avec succès`);

    if (props.element?.["@id"] && newPlaylist.id) {
      const playlist = { id: newPlaylist.id, title: newPlaylist.title } as Playlist;
      await addToPlaylist(playlist);
    }

    closeModal();
  } catch (error) {
    console.error("Erreur lors de la création de la playlist:", error);
    showError("Erreur lors de la création de la playlist");
  }
};

// Selection directe d'une playlist
const selectPlaylist = async (playlist: Playlist) => {
  try {
    if (props.element?.["@id"]) {
      await addToPlaylist(playlist);
      closeModal();
    } else {
      showError("Impossible d'ajouter cet élément");
    }
  } catch (error) {
    console.error("Erreur lors de l'ajout à la playlist:", error);
    showError("Erreur lors de l'ajout à la playlist");
  }
};

const closeModal = () => {
  modalsStore.closePlaylistSelector();
  showCreateForm.value = false;
  formData.value.name = "";
};

watch(
  () => props.isVisible,
  (newVisible) => {
    if (newVisible) {
      fetchUserPlaylists();
    }
  },
);

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === "Escape" && props.isVisible) {
    closeModal();
  }
};

onMounted(() => {
  document.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
  document.removeEventListener("keydown", handleKeydown);
});
</script>

<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
    @click.self="closeModal"
  >
    <div
      class="bg-[#1a0725] border border-[#440a50] rounded-xl shadow-xl w-full max-w-md mx-4 max-h-[80vh] overflow-hidden"
      @click.stop
    >
      <!-- Header -->
      <div class="p-6 border-b border-[#440a50] relative">
        <button
          v-if="showCreateForm"
          @click="backToList"
          class="absolute left-4 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full hover:bg-[#440a50] transition-colors"
        >
          <span class="text-white text-xl">←</span>
        </button>
        <h2 class="text-xl font-semibold text-white text-center">
          {{ showCreateForm ? "Créer une playlist" : "Ajouter à une playlist" }}
        </h2>
        <button
          @click="closeModal"
          class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-[#440a50] transition-colors"
        >
          <span class="text-white text-xl">&times;</span>
        </button>
      </div>

      <!-- Contenu -->
      <div class="p-6 h-[500px] flex flex-col">
        <!-- Formulaire de création -->
        <div v-if="showCreateForm" class="flex-1 flex flex-col">
          <FormKit
            v-model="formData"
            type="form"
            :actions="false"
            @submit="submitCreatePlaylist"
            class="flex-1 flex flex-col justify-between"
          >
            <div>
              <FormKit
                type="text"
                name="name"
                label="Nom de la playlist"
                placeholder="Entrez le nom de la playlist..."
                validation="required|length:1,100"
                :validation-messages="{
                  required: 'Le nom de la playlist est requis',
                  length: 'Le nom doit contenir entre 1 et 100 caractères',
                }"
                :classes="{
                  label: 'block text-sm font-medium text-white mb-2',
                  input:
                    'w-full px-3 py-2 bg-[#2a0d35] border border-[#440a50] rounded-lg text-white placeholder-white/50 focus:outline-none focus:border-[#5a0f60] transition-colors',
                  message: 'text-red-400 text-sm mt-1',
                }"
                autocomplete="off"
              />
            </div>

            <div class="flex justify-between flex-col-reverse sm:flex-row gap-3 mt-6">
              <button
                type="button"
                @click="backToList"
                class="flex-1 px-4 py-2 border border-[#440a50] text-white rounded-lg hover:bg-[#440a50] transition-colors"
              >
                Annuler
              </button>
              <FormKit
                type="submit"
                label="Créer"
                :disabled="!formData.name?.trim()"
                :classes="{
                  input:
                    'flex-1 px-4 py-2 bg-[#440a50] text-white rounded-lg hover:bg-[#5a0f60] transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-medium',
                }"
                wrapper-class="flex-1"
                outer-class="w-full sm:w-1/2"
              />
            </div>
          </FormKit>
        </div>

        <!-- Loading state -->
        <div v-else-if="loading" class="flex-1 flex items-center justify-center">
          <div class="text-white/70">Chargement des playlists...</div>
        </div>

        <!-- Empty state -->
        <div
          v-else-if="playlists && filteredPlaylists.length === 0"
          class="flex-1 flex flex-col items-center justify-center"
        >
          <div class="text-white/70 mb-4">
            {{
              props.element?.["@type"] === "Playlist"
                ? "Aucune autre playlist disponible"
                : "Aucune playlist trouvée"
            }}
          </div>
          <button
            @click="createNewPlaylist"
            class="px-4 py-2 bg-[#440a50] text-white rounded-lg hover:bg-[#5a0f60] transition-colors"
          >
            Créer une nouvelle playlist
          </button>
        </div>

        <!-- Liste des playlists -->
        <div v-else class="flex-1 flex flex-col min-h-0">
          <button
            @click="createNewPlaylist"
            class="w-full px-4 py-2 bg-[#440a50] text-white rounded-lg hover:bg-[#5a0f60] transition-colors flex items-center justify-center gap-2 flex-shrink-0 mb-3"
          >
            <span class="text-lg">+</span>
            Créer une nouvelle playlist
          </button>

          <div class="flex-1 overflow-y-auto scrollbar-custom">
            <div class="space-y-2 pr-2">
              <div
                v-for="playlist in filteredPlaylists"
                :key="playlist.id"
                @click="selectPlaylist(playlist)"
                class="flex items-center p-3 rounded-lg hover:bg-[#440a50] cursor-pointer transition-colors group"
              >
                <div
                  class="w-12 h-12 rounded bg-gradient-to-br from-purple-500 to-pink-500 flex-shrink-0"
                >
                  <img
                    :src="playlist.cover ? ressourceUrl + playlist.cover : defaultCover"
                    :alt="playlist.title"
                    class="w-full h-full rounded object-cover"
                  />
                </div>
                <div class="ml-3 flex-1 min-w-0">
                  <h3 class="text-white font-medium truncate">{{ playlist.title }}</h3>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                  <span class="text-white/70 text-sm">Ajouter</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Scrollbar personnalisée */
.scrollbar-custom {
  scrollbar-width: thin;
  scrollbar-color: rgba(68, 10, 80, 0.8) rgba(68, 10, 80, 0.2);
}

.scrollbar-custom::-webkit-scrollbar {
  width: 8px;
}

.scrollbar-custom::-webkit-scrollbar-track {
  background: rgba(68, 10, 80, 0.2);
  border-radius: 4px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
  background: rgba(68, 10, 80, 0.8);
  border-radius: 4px;
}

.scrollbar-custom::-webkit-scrollbar-thumb:hover {
  background: rgba(90, 15, 96, 1);
}

/* FormKit personnalisations supplémentaires */
:deep(.formkit-outer) {
  margin-bottom: 0;
}

:deep(.formkit-wrapper) {
  margin-bottom: 0;
}

:deep(.formkit-input[type="submit"]) {
  width: 100%;
}

/* Assurer une hauteur minimale pour le contenu */
.min-h-0 {
  min-height: 0;
}
</style>
