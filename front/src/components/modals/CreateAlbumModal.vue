<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import { useUserStore } from "@/stores/user";
import type { Music, DraftMusic } from "@/utils/types";
import AddMusicModal from "@/components/modals/AddMusicModal.vue";
import editLight from "@/assets/icons/edit-light.svg";
import removeLight from "@/assets/icons/remove-light.svg";
import { convertDurationInMinutes } from "@/sharedFunctions";

interface Props {
  isVisible: boolean;
}

interface Emits {
  (e: "close"): void;
  (e: "success"): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();
const userStore = useUserStore();

const loading = ref(false);
const showAddMusicModal = ref(false);
const editingMusic = ref<Music | DraftMusic | null>(null);
const readonly = ref(false);
const createdAlbumId = ref<string | null>(null);

const formData = ref({
  title: "",
  releaseDate: "",
});

const coverFile = ref<File | null>(null);
const wallpaperFile = ref<File | null>(null);
const coverPreview = ref<string>("");
const wallpaperPreview = ref<string>("");

const albumMusics = ref<(Music | DraftMusic)[]>([]);

const isOpen = computed(() => props.isVisible);

const close = () => {
  if (createdAlbumId.value) {
    emit("success");
  }
  emit("close");
  resetForm();
};

const resetForm = () => {
  formData.value = {
    title: "",
    releaseDate: "",
  };
  coverFile.value = null;
  wallpaperFile.value = null;
  coverPreview.value = "";
  wallpaperPreview.value = "";
  albumMusics.value = [];
  createdAlbumId.value = null;
  editingMusic.value = null;
  showAddMusicModal.value = false;
  readonly.value = false;
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

const isDraftMusic = (music: Music | DraftMusic): music is DraftMusic => {
  return 'isDraft' in music && music.isDraft === true;
};

const createAlbumWithDraftMusics = async () => {
  loading.value = true;

  try {
    const albumTitle = formData.value.title.trim() || "Nouvel Album";
    const albumDate = formData.value.releaseDate || new Date().toISOString().split('T')[0];
    
    const albumData = {
      title: albumTitle,
      releaseDate: albumDate,
    };

    const createdAlbum = await apiClient.album.create(albumData);
    
    if (!createdAlbum.id) {
      throw new Error("Erreur lors de la création de l'album");
    }

    createdAlbumId.value = createdAlbum.id.toString();

    if (coverFile.value || wallpaperFile.value) {
      await apiClient.album.updateFiles(
        createdAlbumId.value,
        coverFile.value || undefined,
        wallpaperFile.value || undefined,
      );
    }

    if (albumMusics.value.length > 0) {
      const musicIds = [];
      for (let index = 0; index < albumMusics.value.length; index++) {
        const music = albumMusics.value[index];
        if (isDraftMusic(music)) {
          
          if (!music.file) continue;
          
          const response = await fetch(music.file);
          const blob = await response.blob();
          const file = new File([blob], `${music.title}.mp3`, { type: 'audio/mpeg' });

          
          const formData = new FormData();
          formData.append('title', music.title);
          formData.append('albumPosition', (index + 1).toString());

          const musicResponse = await apiClient.music.createMusic(formData, file);
          
          if (musicResponse["@id"]) {
            musicIds.push(musicResponse["@id"]);
          }
        } else {
          
          await apiClient.music.updateMusic(music.id, {
            albumPosition: index, 
          });
          musicIds.push(music["@id"] || `/api/musics/${music.id}`);
        }
      }

      if (musicIds.length > 0) {
        await apiClient.album.update(createdAlbumId.value, {
          musics: musicIds as any,
        });
      }
    }

    const successMessage = albumMusics.value.length > 0 
      ? `Album créé avec succès avec ${albumMusics.value.length} musique${albumMusics.value.length > 1 ? "s" : ""} !`
      : "Album créé avec succès !";
    
    showSuccess(successMessage);
    emit("success");
    close();
    
  } catch (error: any) {
    console.error("Erreur lors de la création complète de l'album:", error);
    showError(error.response?.data?.message || "Erreur lors de la création de l'album");
  } finally {
    loading.value = false;
  }
};

const addMusic = () => {
  
  editingMusic.value = null;
  readonly.value = false;
  showAddMusicModal.value = true;
};

const editMusic = (musicId: number) => {
  const music = albumMusics.value.find((m) => m.id === musicId);
  if (music && !isDraftMusic(music)) {
    
    editingMusic.value = music;
    readonly.value = false;
    showAddMusicModal.value = true;
  } else {
    showError("L'édition des musiques brouillon n'est pas encore supportée");
  }
};

const deleteMusic = async (musicFile: string, musicTitle: string, musicId: number) => {
  if (!confirm(`Êtes-vous sûr de vouloir supprimer la musique "${musicTitle}" ?`)) {
    return;
  }

  const music = albumMusics.value.find(m => m.id === musicId);
  if (!music) {
    showError("Impossible de trouver la musique");
    return;
  }

  
  if (isDraftMusic(music)) {
    if (music.file) {
      URL.revokeObjectURL(music.file);
    }
    albumMusics.value = albumMusics.value.filter(m => m.id !== musicId);
    showSuccess("Musique supprimée du brouillon !");
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
    await refreshAlbumMusics();
  } catch (error) {
    console.error("Erreur lors de la suppression:", error);
    showError("Erreur lors de la suppression de la musique");
  }
};

const closeAddMusicModal = () => {
  showAddMusicModal.value = false;
  editingMusic.value = null;
  readonly.value = false;
};

const handleMusicAdded = async (music?: any) => {
  showAddMusicModal.value = false;
  editingMusic.value = null;
  
  if (!createdAlbumId.value && music) {
    albumMusics.value.push(music);
  } else {
    
    await refreshAlbumMusics();
  }
};

const refreshAlbumMusics = async () => {
  if (!createdAlbumId.value) return;
  
  try {
    const album = await apiClient.album.get(createdAlbumId.value);
    albumMusics.value = album.musics || [];
  } catch (error) {
    console.error("Erreur lors de la récupération des musiques:", error);
  }
};


watch(isOpen, (newValue) => {
  if (!newValue) {
    resetForm();
  }
});
</script>

<template>
  <div 
    v-if="isOpen" 
    class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    @click.self="close"
  >
    <div class="bg-[#1a1a1a] rounded-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
      <div class="sticky top-0 bg-[#1a1a1a] border-b border-gray-700 p-6 rounded-t-2xl z-[999]">
        <div class="flex items-center justify-between">
          <h2 class="text-3xl font-bold text-white">Créer un album</h2>
          <button
            @click="close"
            class="text-gray-400 hover:text-white transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="p-6">
        <div class="bg-[#2E0B40] rounded-lg p-6">
          <div class="relative mb-8">
            <div class="h-64 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg overflow-hidden">
              <img
                v-if="wallpaperPreview"
                :src="wallpaperPreview"
                alt="Wallpaper"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-white">
                <div class="text-center">
                  <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <p class="text-lg">Wallpaper de l'album</p>
                </div>
              </div>
            </div>
            <div class="absolute bottom-4 right-4">
              <label class="px-4 py-2 bg-purple-600 text-white rounded cursor-pointer hover:bg-purple-700 transition-colors">
                {{ wallpaperPreview ? "Changer" : "Ajouter" }} wallpaper
                <input type="file" accept="image/*" @change="handleWallpaperChange" class="hidden" />
              </label>
            </div>
          </div>

          <div class="flex items-start space-x-6 mb-8">
            <div class="relative">
              <div class="w-32 h-32 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg overflow-hidden">
                <img
                  v-if="coverPreview"
                  :src="coverPreview"
                  alt="Cover"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-white text-center">
                  <div>
                    <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-xs">Cover</p>
                  </div>
                </div>
              </div>
              <div class="absolute bottom-0 right-0">
                <label class="p-2 bg-purple-600 text-white rounded-full cursor-pointer hover:bg-purple-700 transition-colors">
                  📷
                  <input type="file" accept="image/*" @change="handleCoverChange" class="hidden" />
                </label>
              </div>
            </div>

            <div class="flex-1">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-white mb-2">Titre</label>
                  <input
                    v-model="formData.title"
                    type="text"
                    class="w-full px-3 py-2 bg-[#3a1452] border border-[#440a50] rounded-lg text-white placeholder-gray-400 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
                    placeholder="Nom de l'album (optionnel)"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-white mb-2">Artiste</label>
                  <p class="text-white text-lg bg-[#3a1452] px-3 py-2 rounded-lg">{{ userStore.user?.name || "—" }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-white mb-2">Date de sortie</label>
                  <input
                    v-model="formData.releaseDate"
                    type="date"
                    class="w-full px-3 py-2 bg-[#3a1452] border border-[#440a50] rounded-lg text-white focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-colors"
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="flex gap-4 mb-8 pb-8 border-b border-gray-600">
            <button
              v-if="!createdAlbumId"
              @click="createAlbumWithDraftMusics"
              :disabled="loading"
              class="px-6 py-3 bg-purple-600 hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-colors flex items-center gap-2"
            >
              <svg v-if="loading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
              </svg>
              {{ loading ? "Création..." : "Créer l'album" }}
            </button>

            <div v-if="createdAlbumId" class="px-6 py-3 bg-green-700 text-white rounded-lg font-medium flex items-center gap-2">
              ✓ Album créé avec succès
            </div>

            <button
              @click="addMusic"
              class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2"
            >
              <span class="text-lg">+</span>
              Ajouter une musique
            </button>

            <button
              @click="close"
              class="px-6 py-3 bg-gray-600 hover:bg-gray-500 text-white rounded-lg font-medium transition-colors ml-auto"
            >Annuler</button>

          </div>

          <div v-if="albumMusics.length > 0">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-bold text-white">Musiques de l'album</h3>
              <p class="text-gray-400">{{ albumMusics.length }} titre{{ albumMusics.length > 1 ? "s" : "" }}</p>
            </div>
            <div class="space-y-3">
              <div
                v-for="(music, index) in albumMusics"
                :key="music.id"
                class="bg-[#3a1452] rounded-lg p-4 flex items-center space-x-4 group hover:bg-[#4a1562] transition-colors"
              >
                <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                  {{ index + 1 }}
                </div>
                <div class="flex-1">
                  <h4 class="text-white font-medium">{{ music.title }}</h4>
                  <p class="text-gray-400 text-sm">
                    {{ convertDurationInMinutes(`${music.duration}`) || "Durée inconnue" }}
                  </p>
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="editMusic(music.id)"
                    class="p-2 rounded hover:bg-yellow-500 transition-colors"
                    title="Éditer"
                  >
                    <img :src="editLight" class="w-4 h-4" alt="Éditer" />
                  </button>
                  <button
                    @click="deleteMusic(music.file as string, music.title, music.id)"
                    class="p-2 rounded hover:bg-red-500 transition-colors"
                    title="Supprimer"
                  >
                    <img :src="removeLight" class="w-4 h-4" alt="Supprimer" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
            </svg>
            <p class="text-lg mb-2">Aucune musique ajoutée</p>
            <p class="text-sm">{{ createdAlbumId ? "Commencez par ajouter des musiques à votre album" : "Cliquez sur 'Ajouter une musique' pour commencer !" }}</p>
          </div>
        </div>
      </div>
    </div>

    <AddMusicModal
      :is-visible="showAddMusicModal"
      :album-id="createdAlbumId || ''"
      :max-position="albumMusics.length"
      :edit-music="editingMusic as Music | null"
      :readonly="readonly"
      @close="closeAddMusicModal"
      @success="handleMusicAdded"
    />
  </div>
</template>
