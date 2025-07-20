<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import { streamToAudioUrl } from "@/utils/stream";
import { convertDurationInMinutes } from "@/sharedFunctions";
import type { Music } from "@/utils/types";

interface AddMusicFormData {
  title: string;
  albumPosition: number;
  file: File | null;
}

interface Props {
  isVisible: boolean;
  albumId: string;
  maxPosition: number;
  albumCover?: string;
  editMusic?: Music | null;
  readonly?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
  readonly: false,
});

const emit = defineEmits<{
  close: [];
  success: [];
}>();

const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();

const loading = ref(false);
const audioFile = ref<File | null>(null);
const audioPreview = ref<string>("");
const loadingExistingAudio = ref(false);

const isEditMode = computed(() => !!props.editMusic && !props.readonly);
const isReadonlyMode = computed(() => props.readonly);
const modalTitle = computed(() => {
  if (isReadonlyMode.value) return "Détail de la musique";
  if (isEditMode.value) return "Modifier la musique";
  return "Ajouter une musique";
});

const formData = ref<AddMusicFormData>({
  title: "",
  albumPosition: props.maxPosition + 1,
  file: null,
});

watch(
  () => props.editMusic,
  async (newMusic) => {
    if (newMusic) {
      formData.value = {
        title: newMusic.title || "",
        albumPosition: newMusic.albumPosition || 1,
        file: null,
      };

      if (newMusic.file) {
        loadingExistingAudio.value = true;
        try {
          const fileIdParts = newMusic.file.split("/");
          const fileId = fileIdParts[fileIdParts.length - 1];
          const fileResponse = await apiClient.music.getFile(fileId);
          audioPreview.value = await streamToAudioUrl(fileResponse);
        } catch (error) {
          console.error("Erreur lors du chargement du fichier audio existant:", error);
        } finally {
          loadingExistingAudio.value = false;
        }
      }
    } else {
      formData.value = {
        title: "",
        albumPosition: props.maxPosition + 1,
        file: null,
      };
      resetAudioFile();
    }
  },
  { immediate: true },
);

const handleFileChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (file) {
    if (!file.type.startsWith("audio/")) {
      showError("Le fichier doit être un fichier audio valide");
      return;
    }

    if (file.size > 50 * 1024 * 1024) {
      showError("Le fichier est trop volumineux (max 50MB)");
      return;
    }

    audioFile.value = file;
    if (audioPreview.value && audioPreview.value.startsWith("blob:")) {
      URL.revokeObjectURL(audioPreview.value);
    }
    audioPreview.value = URL.createObjectURL(file);
    formData.value.file = file;

    if (!formData.value.title && !isEditMode.value) {
      formData.value.title = file.name.replace(/\.[^/.]+$/, "");
    }
  }

  (event.target as HTMLInputElement).value = "";
};

const resetAudioFile = () => {
  if (audioPreview.value && audioPreview.value.startsWith("blob:")) {
    URL.revokeObjectURL(audioPreview.value);
  }
  audioFile.value = null;
  audioPreview.value = "";
  formData.value.file = null;
};

const removeFile = () => {
  resetAudioFile();
};

const submitHandler = async (data: AddMusicFormData) => {
  if (!isEditMode.value && !audioFile.value) {
    showError("Veuillez sélectionner un fichier audio");
    return;
  }

  try {
    loading.value = true;

    if (isEditMode.value && props.editMusic) {
      let fileId = props.editMusic.file;

      if (audioFile.value) {
        const uploadResponse = await apiClient.music.upload(audioFile.value);
        if (!uploadResponse["@id"]) {
          throw new Error("Erreur lors de l'upload du fichier audio");
        }
        fileId = uploadResponse["@id"];
      }

      await apiClient.music.updateMusic(props.editMusic.id, {
        title: data.title,
        albumPosition: data.albumPosition,
        ...(fileId && { file: fileId }),
      });
      showSuccess("Musique modifiée avec succès !");
    } else {
      // 1. Récupération de l'album actuel pour obtenir ses musiques
      const currentAlbum = await apiClient.album.get(props.albumId);
      const currentMusics =
        currentAlbum.musics?.map(
          (music) => (music as Music)["@id"] || `/api/musics/${(music as Music).id}`,
        ) || [];

      // 3. Création de la musique
      const musicResponse = await apiClient.music.createMusic(
        {
          title: data.title,
          albumPosition: data.albumPosition,
          cover: currentAlbum.cover,
        },
        audioFile.value!,
      );

      if (!musicResponse["@id"]) {
        throw new Error("Erreur lors de la création de la musique");
      }
      const musicId = musicResponse["@id"];

      // 4. Ajout de la nouvelle musique au tableau
      const updatedMusics = [...currentMusics, musicId];

      // 5. Mise à jour de l'album avec le nouveau tableau de musiques (en tant que string[])
      await apiClient.album.update(props.albumId, {
        musics: updatedMusics as string[],
      });

      showSuccess("Musique ajoutée avec succès !");
    }

    emit("success");
    closeModal();
  } catch (error) {
    console.error("Erreur lors de l'opération:", error);
    showError(
      isEditMode.value
        ? "Erreur lors de la modification de la musique"
        : "Erreur lors de l'ajout de la musique",
    );
  } finally {
    loading.value = false;
  }
};

const closeModal = () => {
  resetAudioFile();

  formData.value = {
    title: "",
    albumPosition: props.maxPosition + 1,
    file: null,
  };

  emit("close");
};

const dragActive = ref(false);

const handleDragOver = (event: DragEvent) => {
  event.preventDefault();
  dragActive.value = true;
};

const handleDragLeave = (event: DragEvent) => {
  event.preventDefault();
  dragActive.value = false;
};

const handleDrop = (event: DragEvent) => {
  event.preventDefault();
  dragActive.value = false;

  const file = event.dataTransfer?.files[0];
  if (file && file.type.startsWith("audio/")) {
    const fakeEvent = {
      target: { files: [file] },
    } as any;
    handleFileChange(fakeEvent);
  }
};

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === "Escape" && props.isVisible) {
    closeModal();
  }
};
</script>

<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
    @click.self="closeModal"
    @keydown="handleKeydown"
  >
    <div
      class="bg-[#1a0725] border border-[#440a50] rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden"
      @click.stop
    >
      <div class="p-6 border-b border-[#440a50] relative">
        <h2 class="text-xl font-semibold text-white text-center">{{ modalTitle }}</h2>
        <button
          @click="closeModal"
          class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-[#440a50] transition-colors"
        >
          <span class="text-white text-xl">&times;</span>
        </button>
      </div>

      <FormKit
        v-if="!isReadonlyMode"
        type="form"
        :value="formData"
        @submit="submitHandler"
        :actions="false"
        :config="{ validationVisibility: 'live' }"
      >
        <div class="p-6 max-h-[70vh] overflow-y-auto scrollbar-custom">
          <div class="space-y-6">
            <!-- File Upload -->
            <div>
              <label class="block text-sm font-medium text-white mb-2">
                Fichier audio
                {{ isEditMode ? "(optionnel - garder le fichier actuel si vide)" : "" }}
              </label>

              <!-- Loading existing audio -->
              <div v-if="loadingExistingAudio" class="text-center py-4">
                <p class="text-white/70">Chargement du fichier audio...</p>
              </div>

              <!-- File upload area -->
              <div v-else-if="!loadingExistingAudio && !isReadonlyMode">
                <div
                  class="border-2 border-dashed rounded-lg p-6 text-center transition-colors"
                  :class="[
                    dragActive
                      ? 'border-[#5a0f60] bg-[#5a0f60]/10'
                      : 'border-[#440a50] hover:border-[#5a0f60]',

                    isReadonlyMode ? 'display-none' : '',
                  ]"
                  @dragover="handleDragOver"
                  @dragleave="handleDragLeave"
                  @drop="handleDrop"
                >
                  <input
                    type="file"
                    accept="audio/*"
                    @change="handleFileChange"
                    class="hidden"
                    id="music-file"
                  />
                  <label for="music-file" class="cursor-pointer">
                    <div class="text-white/70 mb-2">
                      <span class="text-2xl">🎵</span>
                    </div>
                    <p class="text-white font-medium mb-1">
                      {{
                        isEditMode ? "Changer le fichier audio" : "Sélectionner un fichier audio"
                      }}
                    </p>
                    <p class="text-white/50 text-sm">
                      Glissez votre fichier ici ou cliquez pour sélectionner
                    </p>
                    <p class="text-white/30 text-xs mt-1">
                      Formats acceptés : MP3, WAV, FLAC, AAC (max 50MB)
                    </p>
                  </label>
                </div>

                <!-- Audio Preview -->
                <div
                  v-if="audioPreview"
                  class="mt-4 bg-[#2a0d35] border border-[#440a50] rounded-lg p-4"
                >
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                      <div
                        class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center"
                      >
                        <span class="text-white text-lg">🎵</span>
                      </div>
                      <div>
                        <p class="text-white font-medium">
                          {{
                            audioFile
                              ? audioFile.name
                              : isEditMode
                                ? "Fichier audio actuel"
                                : "Fichier audio"
                          }}
                        </p>
                        <p v-if="audioFile" class="text-white/50 text-sm">
                          {{ (audioFile.size / 1024 / 1024).toFixed(2) }} MB
                        </p>
                        <p v-else-if="isEditMode" class="text-white/50 text-sm">Fichier existant</p>
                      </div>
                    </div>
                    <button
                      @click="removeFile"
                      class="p-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-colors"
                      title="Supprimer le fichier"
                    >
                      🗑️
                    </button>
                  </div>

                  <audio
                    :src="audioPreview"
                    controls
                    class="w-full h-8"
                    style="filter: sepia(1) hue-rotate(240deg) saturate(2) brightness(1.2)"
                  >
                    Votre navigateur ne supporte pas l'élément audio.
                  </audio>
                </div>
              </div>
            </div>

            <!-- Title -->
            <FormKit
              type="text"
              name="title"
              label="Titre de la musique"
              placeholder="Nom de la musique"
              validation="required|length:1,100"
              :validation-messages="{
                required: 'Le titre est obligatoire',
                length: 'Le titre doit contenir entre 1 et 100 caractères',
              }"
              :classes="{
                wrapper: 'space-y-2',
                label: 'block text-sm font-medium text-white',
                input:
                  'w-full px-3 py-2 bg-[#2a0d35] border border-[#440a50] rounded-lg text-white placeholder-white/50 focus:outline-none focus:border-[#5a0f60] transition-colors',
                message: 'text-red-400 text-sm',
              }"
            />

            <!-- albumPosition -->
            <FormKit
              type="number"
              name="albumPosition"
              label="Position dans l'album"
              :min="1"
              :max="props.maxPosition + 10"
              validation="required|number|min:1"
              :validation-messages="{
                required: 'La position est obligatoire',
                number: 'La position doit être un nombre',
                min: 'La position doit être supérieure à 0',
              }"
              :classes="{
                wrapper: 'space-y-2',
                label: 'block text-sm font-medium text-white',
                input:
                  'w-full px-3 py-2 bg-[#2a0d35] border border-[#440a50] rounded-lg text-white placeholder-white/50 focus:outline-none focus:border-[#5a0f60] transition-colors',
                message: 'text-red-400 text-sm',
              }"
              :help="
                isEditMode
                  ? `Position actuelle: ${props.editMusic?.albumPosition}`
                  : `Position suggérée: ${props.maxPosition + 1}`
              "
            />

            <!-- Album Cover Preview -->
            <div v-if="albumCover">
              <label class="block text-sm font-medium text-white mb-2"
                >Cover (héritée de l'album)</label
              >
              <div class="w-24 h-24 bg-gray-800 rounded-lg overflow-hidden">
                <img :src="albumCover" alt="Album Cover" class="w-full h-full object-cover" />
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-[#440a50] bg-[#2a0d35]/50">
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
              :disabled="loading || (!isEditMode && !audioFile)"
              :classes="{
                input:
                  'px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed',
              }"
            >
              <span v-if="loading">{{
                isEditMode ? "Modification en cours..." : "Ajout en cours..."
              }}</span>
              <span v-else>{{ isEditMode ? "Modifier la musique" : "Ajouter la musique" }}</span>
            </FormKit>
          </div>
        </div>
      </FormKit>

      <!-- Readonly content -->
      <div v-else class="p-6 max-h-[70vh] overflow-y-auto scrollbar-custom">
        <div class="space-y-6">
          <!-- File Display -->
          <div>
            <label class="block text-sm font-medium text-white mb-2">Fichier audio</label>

            <!-- Loading existing audio -->
            <div v-if="loadingExistingAudio" class="text-center py-4">
              <p class="text-white/70">Chargement du fichier audio...</p>
            </div>

            <!-- Audio Preview -->
            <div
              v-else-if="audioPreview"
              class="bg-[#2a0d35] border border-[#440a50] rounded-lg p-4"
            >
              <div class="flex items-center space-x-3 mb-3">
                <div
                  class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center"
                >
                  <span class="text-white text-lg">🎵</span>
                </div>
                <div>
                  <p class="text-white font-medium">Fichier audio</p>
                  <p class="text-white/50 text-sm">Lecture seule</p>
                </div>
              </div>

              <audio
                :src="audioPreview"
                controls
                class="w-full h-8"
                style="filter: sepia(1) hue-rotate(240deg) saturate(2) brightness(1.2)"
              >
                Votre navigateur ne supporte pas l'élément audio.
              </audio>
            </div>

            <div v-else class="bg-[#2a0d35] border border-[#440a50] rounded-lg p-4 text-center">
              <p class="text-gray-400">Aucun fichier audio</p>
            </div>
          </div>

          <!-- Title (readonly) -->
          <div>
            <label class="block text-sm font-medium text-white mb-2">Titre de la musique</label>
            <p class="text-white text-lg">{{ props.editMusic?.title || "—" }}</p>
          </div>

          <!-- Position (readonly) -->
          <div>
            <label class="block text-sm font-medium text-white mb-2">Position dans l'album</label>
            <p class="text-white text-lg">{{ props.editMusic?.albumPosition || "—" }}</p>
          </div>

          <!-- Duration (readonly) -->
          <div v-if="props.editMusic?.duration">
            <label class="block text-sm font-medium text-white mb-2">Durée</label>
            <p class="text-white text-lg">
              {{ convertDurationInMinutes(`${props.editMusic.duration}`) }}
            </p>
          </div>

          <!-- Album Cover Preview -->
          <div v-if="albumCover">
            <label class="block text-sm font-medium text-white mb-2"
              >Cover (héritée de l'album)</label
            >
            <div class="w-24 h-24 bg-gray-800 rounded-lg overflow-hidden">
              <img :src="albumCover" alt="Album Cover" class="w-full h-full object-cover" />
            </div>
          </div>
        </div>
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
