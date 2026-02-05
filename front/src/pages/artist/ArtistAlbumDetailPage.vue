<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import BackButton from "@/components/navigation/BackButton.vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { Album, Music } from "@/utils/types";
import { ressourceUrl } from "@/utils/tools";
import { convertDurationInMinutes } from "@/sharedFunctions";
import eyeLight from "@/assets/icons/eye-light.svg";
import editLight from "@/assets/icons/edit-light.svg";
import removeLight from "@/assets/icons/remove-light.svg";
import AddMusicModal from "@/components/modals/AddMusicModal.vue";

const route = useRoute();
const router = useRouter();
const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();

const album = ref<Album | null>(null);
const loading = ref(true);
const isEditing = ref(false);
const isEditingMusics = ref(false);
const saving = ref(false);
const showAddMusicModal = ref(false);
const editingMusic = ref<Music | null>(null);
const readonly = ref(false);

const coverFile = ref<File | null>(null);
const wallpaperFile = ref<File | null>(null);
const coverPreview = ref<string>("");
const wallpaperPreview = ref<string>("");

const formData = ref({
  title: "",
  releaseDate: "",
});

const albumId = computed(() => route.params.id as string);
const editMode = computed(() => route.query.edit === "true");

const fetchAlbum = async () => {
  loading.value = true;
  try {
    const response = await apiClient.album.get(albumId.value);
    album.value = response;

    formData.value = {
      title: response.title || "",
      releaseDate: response.releaseDate ? response.releaseDate.split("T")[0] : "",
    };

    coverPreview.value = album.value.cover ? ressourceUrl + album.value.cover : "";
    wallpaperPreview.value = album.value.wallpaper ? ressourceUrl + album.value.wallpaper : "";

    if (editMode.value) {
      isEditing.value = true;
    }
  } catch (error) {
    console.error("Erreur lors de la récupération de l'album:", error);
    showError("Erreur lors de la récupération de l'album");
    router.push("/admin/albums");
  } finally {
    loading.value = false;
  }
};

const toggleEdit = () => {
  isEditing.value = !isEditing.value;
  if (!isEditing.value) {
    if (album.value) {
      formData.value = {
        title: album.value.title || "",
        releaseDate: album.value.releaseDate ? album.value.releaseDate.split("T")[0] : "",
      };
      coverFile.value = null;
      wallpaperFile.value = null;
      coverPreview.value = album.value.cover ? ressourceUrl + album.value.cover : "";
      wallpaperPreview.value = album.value.wallpaper ? ressourceUrl + album.value.wallpaper : "";
    }
  }
};

const handleCoverChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (file) {
    coverFile.value = file;
    coverPreview.value = URL.createObjectURL(file);
  }
};

const handleWallpaperChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (file) {
    wallpaperFile.value = file;
    wallpaperPreview.value = URL.createObjectURL(file);
  }
};

const saveAlbum = async () => {
  if (!album.value) return;

  saving.value = true;
  try {
    await apiClient.album.update(albumId.value, {
      title: formData.value.title,
      releaseDate: formData.value.releaseDate,
    });

    if (coverFile.value || wallpaperFile.value) {
      await apiClient.album.updateFiles(
        albumId.value,
        coverFile.value || undefined,
        wallpaperFile.value || undefined,
      );
    }

    showSuccess("Album mis à jour avec succès !");
    isEditing.value = false;
    await fetchAlbum();
  } catch (error) {
    console.error("Erreur lors de la mise à jour:", error);
    showError("Erreur lors de la mise à jour de l'album");
  } finally {
    saving.value = false;
  }
};

const toggleMusicEdit = () => {
  isEditingMusics.value = !isEditingMusics.value;
};

const addMusic = () => {
  editingMusic.value = null;
  showAddMusicModal.value = true;
};

const closeAddMusicModal = () => {
  showAddMusicModal.value = false;
  editingMusic.value = null;
  readonly.value = false;
};

const handleMusicAdded = async () => {
  showAddMusicModal.value = false;
  editingMusic.value = null;
  await fetchAlbum();
};

const viewMusic = (musicId: number) => {
  const music = album.value?.musics?.find((m) => m.id === musicId);
  if (music) {
    editingMusic.value = music;
    showAddMusicModal.value = true;
    readonly.value = true;
  }
};

const editMusic = (musicId: number) => {
  const music = album.value?.musics?.find((m) => m.id === musicId);
  if (music) {
    editingMusic.value = music;
    showAddMusicModal.value = true;
  }
};

const deleteMusic = async (musicFile: string, musicTitle: string) => {
  if (!confirm(`Êtes-vous sûr de vouloir supprimer la musique "${musicTitle}" ?`)) {
    return;
  }
  if (!musicFile) {
    showError("Impossible de trouver la musique");
    return;
  }
  const musicFileId = musicFile.split("/").pop();
  try {
    await apiClient.music.deleteFile(musicFileId!);
    showSuccess("Musique supprimée avec succès !");
    await fetchAlbum();
  } catch (error) {
    console.error("Erreur lors de la suppression:", error);
    showError("Erreur lors de la suppression de la musique");
  }
};

onMounted(fetchAlbum);
</script>

<template>
  <InAppLayout type="artist" padding="p-10" :loading="loading">
    <div>
      <div class="flex items-center gap-4 mb-8">
        <BackButton
          to="/artist/albums"
          label="Retour aux albums"
        />
      </div>

      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">
          {{ isEditing ? "Éditer l'album" : "Détail de l'album" }}
        </h1>

        <div class="flex space-x-2">
          <button
            v-if="!isEditing"
            @click="toggleEdit"
            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors"
          >
            Éditer
          </button>
          <template v-else>
            <button
              @click="toggleEdit"
              class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors"
            >
              Annuler
            </button>
            <button
              @click="saveAlbum"
              :disabled="saving"
              class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors disabled:opacity-50"
            >
              {{ saving ? "Sauvegarde..." : "Sauvegarder" }}
            </button>
          </template>
        </div>
      </div>

      <div v-if="album" class="bg-[#2E0B40] rounded-lg p-6">
        <!-- Wallpaper -->
        <div class="relative mb-8">
          <div
            class="h-64 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg overflow-hidden"
          >
            <img
              v-if="wallpaperPreview"
              :src="wallpaperPreview"
              alt="Wallpaper"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-white">
              Aucun wallpaper
            </div>
          </div>
          <div v-if="isEditing" class="absolute bottom-4 right-4">
            <label
              class="px-4 py-2 bg-purple-600 text-white rounded cursor-pointer hover:bg-purple-700 transition-colors"
            >
              Changer le wallpaper
              <input type="file" accept="image/*" @change="handleWallpaperChange" class="hidden" />
            </label>
          </div>
        </div>

        <div class="flex items-start space-x-6 mb-8">
          <div class="relative">
            <!-- Cover -->
            <div
              class="w-32 h-32 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg overflow-hidden"
            >
              <img
                v-if="coverPreview"
                :src="coverPreview"
                alt="Cover"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-white">
                Cover
              </div>
            </div>
            <div v-if="isEditing" class="absolute bottom-0 right-0">
              <label
                class="p-2 bg-purple-600 text-white rounded-full cursor-pointer hover:bg-purple-700 transition-colors"
              >
                📷
                <input type="file" accept="image/*" @change="handleCoverChange" class="hidden" />
              </label>
            </div>
          </div>

          <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Titre -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Titre</label>
                <input
                  v-if="isEditing"
                  v-model="formData.title"
                  type="text"
                  class="w-full px-3 py-2 bg-[#3a1452] border border-[#440a50] rounded-lg text-white"
                />
                <p v-else class="text-white text-lg">{{ album.title || "—" }}</p>
              </div>

              <!-- Artiste -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Artiste</label>
                <p class="text-white text-lg">{{ album.artist?.name || "—" }}</p>
              </div>

              <!-- Date de sortie -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Date de sortie</label>
                <input
                  v-if="isEditing"
                  v-model="formData.releaseDate"
                  type="date"
                  class="w-full px-3 py-2 bg-[#3a1452] border border-[#440a50] rounded-lg text-white"
                />
                <p v-else class="text-white text-lg">
                  {{
                    album.releaseDate
                      ? new Date(album.releaseDate).toLocaleDateString("fr-FR")
                      : "—"
                  }}
                </p>
              </div>

              <!-- Musiques -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Musiques</label>
                <p class="text-white text-lg">
                  {{ album.musics?.length || 0 }} musique{{
                    (album.musics?.length || 0) > 1 ? "s" : ""
                  }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Liste des musiques -->
        <div v-if="album.musics && album.musics.length > 0">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-white">Musiques</h3>
            <div class="flex space-x-2">
              <button
                @click="toggleMusicEdit"
                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors text-sm"
              >
                {{ isEditingMusics ? "Terminer l'édition" : "Éditer la liste" }}
              </button>
              <button
                v-if="isEditingMusics"
                @click="addMusic"
                class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors text-sm"
              >
                + Ajouter une musique
              </button>
            </div>
          </div>
          <div class="space-y-3">
            <div
              v-for="(music, index) in album.musics"
              :key="music.id"
              class="bg-[#3a1452] rounded-lg p-4 flex items-center space-x-4 group hover:bg-[#4a1562] transition-colors"
            >
              <div
                class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center text-white font-bold"
              >
                {{ index + 1 }}
              </div>
              <div class="flex-1">
                <h4 class="text-white font-medium">{{ music.title }}</h4>
                <p class="text-gray-400 text-sm">
                  {{ convertDurationInMinutes(`${music.duration}`) || "Durée inconnue" }}
                </p>
              </div>
              <div class="flex space-x-2">
                <!-- Eye icon always visible -->
                <button
                  @click="viewMusic(music.id)"
                  class="p-2 rounded hover:bg-blue-500 transition-colors"
                  title="Consulter"
                >
                  <img :src="eyeLight" class="w-4 h-4" alt="Voir" />
                </button>
                <!-- Edit and delete only in edit mode -->
                <template v-if="isEditingMusics">
                  <button
                    @click="editMusic(music.id)"
                    class="p-2 rounded hover:bg-yellow-500 transition-colors"
                    title="Éditer"
                  >
                    <img :src="editLight" class="w-4 h-4" alt="Éditer" />
                  </button>
                  <button
                    @click="deleteMusic(music.file as string, music.title)"
                    class="p-2 rounded hover:bg-red-500 transition-colors"
                    title="Supprimer"
                  >
                    <img :src="removeLight" class="w-4 h-4" alt="Supprimer" />
                  </button>
                </template>
              </div>
            </div>
          </div>
        </div>

        <!-- Message si aucune musique -->
        <div v-else-if="!album.musics || album.musics.length === 0">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-white">Musiques</h3>
            <button
              @click="addMusic"
              class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-sm"
            >
              + Ajouter une musique
            </button>
          </div>
          <div class="bg-[#3a1452] rounded-lg p-8 text-center">
            <p class="text-gray-400">Aucune musique dans cet album</p>
            <button
              @click="addMusic"
              class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
            >
              Ajouter la première musique
            </button>
          </div>
        </div>

        <AddMusicModal
          :is-visible="showAddMusicModal"
          :album-id="albumId"
          :max-position="album?.musics?.length || 0"
          :album-cover="coverPreview"
          :edit-music="editingMusic"
          :readonly="readonly"
          @close="closeAddMusicModal"
          @success="handleMusicAdded"
        />
      </div>
    </div>
  </InAppLayout>
</template>
