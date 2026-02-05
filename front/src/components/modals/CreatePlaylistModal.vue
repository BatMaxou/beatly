<script setup lang="ts">
import { ref, watch, onUnmounted } from "vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { Music } from "@/utils/types";
import SearchIcon from "@/assets/icons/search-light.svg";
import { FormKit } from "@formkit/vue";

interface CreatePlaylistFormData {
  title: string;
  musics: string[];
  cover: File | null;
  wallpaper: File | null;
}

interface Props {
  isVisible: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  close: [];
  success: [];
}>();

const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();

const loading = ref(false);
const loadingMusics = ref(false);
const coverFile = ref<File | null>(null);
const coverPreview = ref<string>("");
const wallpaperFile = ref<File | null>(null);
const wallpaperPreview = ref<string>("");
const searchResults = ref<Music[]>([]);
const selectedMusics = ref<Music[]>([]);
const searchQuery = ref("");
const isSearchFocused = ref(false);

const formData = ref<CreatePlaylistFormData>({
  title: "",
  musics: [],
  cover: null,
  wallpaper: null,
});

const searchMusics = async (query: string) => {
  if (!query.trim() || query.length < 2) {
    searchResults.value = [];
    return;
  }

  loadingMusics.value = true;
  try {
    const response = await apiClient.search.search(query);
    searchResults.value =
      (response.results.filter((music) => music["@type"] === "Music") as Music[]) || [];
  } catch (error) {
    console.error("Erreur lors de la recherche de musiques:", error);
    showError("Erreur lors de la recherche de musiques");
    searchResults.value = [];
  } finally {
    loadingMusics.value = false;
  }
};

const addMusic = (music: Music) => {
  const musicId = music["@id"] || `/api/musics/${music.id}`;

  if (!selectedMusics.value.find((m) => (m["@id"] || `/api/musics/${m.id}`) === musicId)) {
    selectedMusics.value.push(music);
    formData.value.musics.push(musicId);
  }

  searchQuery.value = "";
  searchResults.value = [];
};

const removeMusic = (music: Music) => {
  const musicId = music["@id"] || `/api/musics/${music.id}`;

  selectedMusics.value = selectedMusics.value.filter(
    (m) => (m["@id"] || `/api/musics/${m.id}`) !== musicId,
  );
  formData.value.musics = formData.value.musics.filter((id) => id !== musicId);
};

const handleCoverChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (file) {
    if (!file.type.startsWith("image/")) {
      showError("Le fichier doit être une image valide");
      return;
    }

    if (file.size > 5 * 1024 * 1024) {
      showError("L'image est trop volumineuse (max 5MB)");
      return;
    }

    coverFile.value = file;
    if (coverPreview.value && coverPreview.value.startsWith("blob:")) {
      URL.revokeObjectURL(coverPreview.value);
    }
    coverPreview.value = URL.createObjectURL(file);
    formData.value.cover = file;
  }

  (event.target as HTMLInputElement).value = "";
};

const handleWallpaperChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (file) {
    if (!file.type.startsWith("image/")) {
      showError("Le fichier doit être une image valide");
      return;
    }

    if (file.size > 5 * 1024 * 1024) {
      showError("L'image est trop volumineuse (max 5MB)");
      return;
    }

    wallpaperFile.value = file;
    if (wallpaperPreview.value && wallpaperPreview.value.startsWith("blob:")) {
      URL.revokeObjectURL(wallpaperPreview.value);
    }
    wallpaperPreview.value = URL.createObjectURL(file);
    formData.value.wallpaper = file;
  }

  (event.target as HTMLInputElement).value = "";
};

const removeCover = () => {
  if (coverPreview.value && coverPreview.value.startsWith("blob:")) {
    URL.revokeObjectURL(coverPreview.value);
  }
  coverFile.value = null;
  coverPreview.value = "";
  formData.value.cover = null;
};

const removeWallpaper = () => {
  if (wallpaperPreview.value && wallpaperPreview.value.startsWith("blob:")) {
    URL.revokeObjectURL(wallpaperPreview.value);
  }
  wallpaperFile.value = null;
  wallpaperPreview.value = "";
  formData.value.wallpaper = null;
};

const submitHandler = async (data: CreatePlaylistFormData) => {
  if (!coverFile.value) {
    showError("Veuillez sélectionner une cover");
    return;
  }

  if (selectedMusics.value.length === 0) {
    showError("Veuillez sélectionner au moins une musique");
    return;
  }

  try {
    loading.value = true;
    const formDataFinal = new FormData();
    formDataFinal.append("title", data.title.trim());
    formDataFinal.append("cover", formData.value.cover as File);

    if (wallpaperFile.value) {
      formDataFinal.append("wallpaper", wallpaperFile.value);
    }

    // Append musics with indexed format
    formData.value.musics.forEach((musicId, index) => {
      formDataFinal.append(`musics[${index}][music]`, musicId);
    });

    await apiClient.playlist.createPlatform(formDataFinal);

    showSuccess("Playlist créée avec succès !");

    emit("success");
    closeModal();
  } catch (error) {
    console.error("Erreur lors de la création:", error);
    showError("Erreur lors de la création de la playlist");
  } finally {
    loading.value = false;
  }
};

const closeModal = () => {
  removeCover();
  removeWallpaper();
  selectedMusics.value = [];
  searchResults.value = [];
  searchQuery.value = "";
  isSearchFocused.value = false;
  formData.value = {
    title: "",
    musics: [],
    cover: null,
    wallpaper: null,
  };
  emit("close");
};

const lockBodyScroll = () => {
  document.body.style.overflow = "hidden";
};

const unlockBodyScroll = () => {
  document.body.style.overflow = "";
};

const handleSearchFocus = () => {
  isSearchFocused.value = true;
};

const handleSearchBlur = () => {
  setTimeout(() => {
    isSearchFocused.value = false;
  }, 150);
};

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === "Escape" && props.isVisible) {
    closeModal();
  }
};

watch(searchQuery, (newQuery) => {
  if (newQuery.length >= 2) {
    searchMusics(newQuery);
  } else {
    searchResults.value = [];
  }
});

watch(
  () => props.isVisible,
  (newVisible) => {
    if (newVisible) {
      lockBodyScroll();
    } else {
      unlockBodyScroll();
    }
  },
);

onUnmounted(() => {
  unlockBodyScroll();
});
</script>

<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm overflow-hidden"
    @click.self="closeModal"
    @keydown="handleKeydown"
  >
    <div
      class="bg-[#1a0725] border border-[#440a50] rounded-xl shadow-xl w-full max-w-3xl mx-4 h-[90vh] flex flex-col overflow-hidden"
      @click.stop
    >
      <!-- Header -->
      <div class="p-6 border-b border-[#440a50] relative flex-shrink-0">
        <h2 class="text-xl font-semibold text-white text-center">Créer une playlist</h2>
        <button
          @click="closeModal"
          class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-[#440a50] transition-colors"
        >
          <span class="text-white text-xl">&times;</span>
        </button>
      </div>
      <div class="flex-1 overflow-hidden relative">
        <FormKit
          type="form"
          :value="formData"
          @submit="submitHandler"
          :actions="false"
          :config="{ validationVisibility: 'live' }"
          class="h-full flex flex-col"
        >
          <!-- Scrollable Content -->
          <div class="flex-1 overflow-y-auto scrollbar-custom">
            <div class="p-6 space-y-6">
              <!-- Titre -->
              <FormKit
                type="text"
                name="title"
                label="Nom de la playlist"
                placeholder="Nom de la playlist"
                validation="required|length:1,100"
                :validation-messages="{
                  required: 'Le nom est obligatoire',
                  length: 'Le nom doit contenir entre 1 et 100 caractères',
                }"
                :classes="{
                  wrapper: 'space-y-2',
                  label: 'block text-sm font-medium text-white',
                  input:
                    'w-full px-3 py-2 bg-[#2a0d35] border border-[#440a50] rounded-lg text-white placeholder-white/50 focus:outline-none focus:border-[#5a0f60] transition-colors',
                  message: 'text-red-400 text-sm',
                }"
              />

              <!-- Cover et Wallpaper -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cover -->
                <div>
                  <label class="block text-sm font-medium text-white mb-2">Cover</label>

                  <!-- File Input - Only show when no cover is selected -->
                  <div
                    v-if="!coverPreview"
                    class="border-2 border-dashed rounded-lg p-6 text-center transition-colors border-[#440a50] hover:border-[#5a0f60]"
                  >
                    <input
                      type="file"
                      accept="image/*"
                      @change="handleCoverChange"
                      class="hidden"
                      id="cover-file"
                    />
                    <label for="cover-file" class="cursor-pointer">
                      <div class="text-white/70 mb-2">
                        <span class="text-2xl">🖼️</span>
                      </div>
                      <p class="text-white font-medium mb-1">Sélectionner une cover</p>
                      <p class="text-white/50 text-sm">
                        Glissez votre image ici ou cliquez pour sélectionner
                      </p>
                      <p class="text-white/30 text-xs mt-1">
                        Formats acceptés : JPG, PNG, GIF (max 5MB)
                      </p>
                    </label>
                  </div>

                  <!-- Cover Preview - Only show when cover is selected -->
                  <div
                    v-if="coverPreview"
                    class="bg-[#2a0d35] border border-[#440a50] rounded-lg p-4"
                  >
                    <div class="flex items-center justify-between mb-3">
                      <div class="flex items-center space-x-3">
                        <div class="w-16 h-16 bg-gray-800 rounded-lg overflow-hidden">
                          <img
                            :src="coverPreview"
                            alt="Cover preview"
                            class="w-full h-full object-cover"
                          />
                        </div>
                        <div>
                          <p class="text-white font-medium">Cover sélectionnée</p>
                          <p v-if="coverFile" class="text-white/50 text-sm">
                            {{ (coverFile.size / 1024 / 1024).toFixed(2) }} MB
                          </p>
                        </div>
                      </div>
                      <button
                        @click="removeCover"
                        class="p-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-colors"
                        title="Supprimer la cover"
                      >
                        🗑️
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Wallpaper -->
                <div>
                  <label class="block text-sm font-medium text-white mb-2"
                    >Wallpaper (optionnel)</label
                  >

                  <div
                    v-if="!wallpaperPreview"
                    class="border-2 border-dashed rounded-lg p-6 text-center transition-colors border-[#440a50] hover:border-[#5a0f60]"
                  >
                    <input
                      type="file"
                      accept="image/*"
                      @change="handleWallpaperChange"
                      class="hidden"
                      id="wallpaper-file"
                    />
                    <label for="wallpaper-file" class="cursor-pointer">
                      <div class="text-white/70 mb-2">
                        <span class="text-2xl">🌄</span>
                      </div>
                      <p class="text-white font-medium mb-1">Sélectionner un wallpaper</p>
                      <p class="text-white/50 text-sm">
                        Glissez votre image ici ou cliquez pour sélectionner
                      </p>
                      <p class="text-white/30 text-xs mt-1">
                        Formats acceptés : JPG, PNG, GIF (max 5MB)
                      </p>
                    </label>
                  </div>

                  <div
                    v-if="wallpaperPreview"
                    class="bg-[#2a0d35] border border-[#440a50] rounded-lg p-4"
                  >
                    <div class="flex items-center justify-between mb-3">
                      <div class="flex items-center space-x-3">
                        <div class="w-24 h-16 bg-gray-800 rounded-lg overflow-hidden">
                          <img
                            :src="wallpaperPreview"
                            alt="Wallpaper preview"
                            class="w-full h-full object-cover"
                          />
                        </div>
                        <div>
                          <p class="text-white font-medium">Wallpaper sélectionné</p>
                          <p v-if="wallpaperFile" class="text-white/50 text-sm">
                            {{ (wallpaperFile.size / 1024 / 1024).toFixed(2) }} MB
                          </p>
                        </div>
                      </div>
                      <button
                        @click="removeWallpaper"
                        class="p-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-colors"
                        title="Supprimer le wallpaper"
                      >
                        🗑️
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Musiques -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">
                  Rechercher des musiques
                </label>

                <!-- Search Input with Select-like styling -->
                <div class="relative">
                  <div class="relative">
                    <input
                      v-model="searchQuery"
                      type="text"
                      placeholder="Tapez le nom d'une musique, artiste ou album..."
                      class="w-full px-3 py-2 pr-8 bg-[#2a0d35] border border-[#440a50] rounded-lg text-white placeholder-white/50 focus:outline-none focus:border-[#5a0f60] transition-colors"
                      @focus="handleSearchFocus"
                      @blur="handleSearchBlur"
                    />
                    <div
                      class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                    >
                      <img :src="SearchIcon" alt="Search icon" class="w-4 h-4 text-white/50" />
                    </div>
                  </div>

                  <!-- Search Results - Select-like dropdown -->
                  <div
                    v-if="
                      (searchResults.length > 0 || loadingMusics) &&
                      (isSearchFocused || searchQuery.length >= 2)
                    "
                    class="absolute top-full left-0 right-0 z-50 mt-1 bg-[#2a0d35] border border-[#440a50] rounded-lg shadow-xl max-h-[200px] overflow-y-auto scrollbar-custom"
                  >
                    <!-- Loading state -->
                    <div v-if="loadingMusics" class="p-3 text-center">
                      <div class="flex items-center justify-center space-x-2">
                        <div
                          class="animate-spin rounded-full h-4 w-4 border-2 border-purple-500 border-t-transparent"
                        ></div>
                        <span class="text-white/70 text-sm">Recherche en cours...</span>
                      </div>
                    </div>

                    <!-- Results -->
                    <div v-else>
                      <div
                        v-for="(music, index) in searchResults"
                        :key="music.id"
                        @click="addMusic(music)"
                        class="relative cursor-pointer select-none px-3 py-2 hover:bg-[#3a1452] transition-colors"
                        :class="{ 'border-t border-[#440a50]': index > 0 }"
                      >
                        <div class="flex items-center">
                          <div class="flex-1 min-w-0">
                            <div class="text-white font-medium text-sm truncate">
                              {{ music.title }}
                            </div>
                            <div class="text-gray-400 text-xs truncate">
                              {{ music.album?.title || "Album inconnu" }} -
                              {{ music.album?.artist?.name || "Artiste inconnu" }}
                            </div>
                          </div>
                          <div class="ml-2 flex-shrink-0">
                            <svg
                              class="w-4 h-4 text-purple-400"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                              />
                            </svg>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Selected Musics -->
                <div v-if="selectedMusics.length > 0" class="mt-4">
                  <label class="block text-sm font-medium text-white mb-2">
                    Musiques sélectionnées ({{ selectedMusics.length }})
                  </label>
                  <div
                    class="space-y-2 max-h-40 overflow-y-auto scrollbar-custom bg-[#2a0d35] border border-[#440a50] rounded-lg p-3"
                  >
                    <div
                      v-for="music in selectedMusics"
                      :key="music.id"
                      class="flex items-center justify-between bg-[#3a1452] rounded-lg p-3 hover:bg-[#4a1562] transition-colors"
                    >
                      <div class="flex-1 min-w-0">
                        <div class="text-white font-medium text-sm truncate">
                          {{ music.title }}
                        </div>
                        <div class="text-gray-400 text-xs truncate">
                          {{ music.album?.title || "Album inconnu" }} -
                          {{ music.album?.artist?.name || "Artiste inconnu" }}
                        </div>
                      </div>
                      <button
                        @click="removeMusic(music)"
                        class="ml-3 p-1.5 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-full transition-colors flex-shrink-0"
                        title="Retirer de la playlist"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Validation message -->
                <div
                  v-if="selectedMusics.length === 0"
                  class="text-red-400 text-sm mt-2 flex items-center space-x-1"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  <span>Vous devez sélectionner au moins une musique</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div
            class="p-6 border-t border-[#440a50] bg-[#2a0d35]/50 flex-shrink-0 absolute bottom-0 left-0 w-full"
          >
            <div class="flex justify-between items-center">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 border border-[#440a50] text-white rounded-lg hover:bg-[#440a50] transition-colors"
              >
                Annuler
              </button>
              <FormKit
                type="submit"
                :disabled="loading || loadingMusics || !coverFile || selectedMusics.length === 0"
                :classes="{
                  input:
                    'px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed',
                }"
              >
                <span v-if="loading">Création en cours...</span>
                <span v-else>Créer la playlist</span>
              </FormKit>
            </div>
          </div>
        </FormKit>
      </div>
    </div>
  </div>
</template>

<style scoped>
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
</style>
