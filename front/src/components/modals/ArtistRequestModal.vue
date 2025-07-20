<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from "vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { ArtistRequest } from "@/utils/types";
import { streamToAudioUrl } from "@/utils/stream";

interface ArtistRequestFormData {
  message: string;
  files: File[];
}

interface AudioFilePreview {
  file: File;
  url: string;
  name: string;
}

const props = defineProps<{
  isVisible: boolean;
  isAdminView?: boolean;
  request?: ArtistRequest;
}>();

const emit = defineEmits<{
  close: [];
  success: [];
  accept: [request: ArtistRequest];
  decline: [request: ArtistRequest];
}>();

const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();

const formData = ref<ArtistRequestFormData>({
  message: "",
  files: [],
});

const loading = ref(false);
const adminActionLoading = ref(false);

const audioFiles = ref<AudioFilePreview[]>([]);

const handleFileChange = (event: Event) => {
  const files = Array.from((event.target as HTMLInputElement).files || []);

  files.forEach((file) => {
    if (file.type.startsWith("audio/")) {
      if (file.size > 10 * 1024 * 1024) {
        showError(`Le fichier ${file.name} est trop volumineux (max 10MB)`);
        return;
      }

      if (audioFiles.value.length >= 5) {
        showError("Maximum 5 fichiers audio autorisés");
        return;
      }

      const url = URL.createObjectURL(file);
      audioFiles.value.push({
        file,
        url,
        name: file.name,
      });
      formData.value.files.push(file);
    } else {
      showError(`Le fichier ${file.name} n'est pas un fichier audio valide`);
    }
  });

  (event.target as HTMLInputElement).value = "";
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

  const files = Array.from(event.dataTransfer?.files || []);
  files.forEach((file) => {
    if (file.type.startsWith("audio/")) {
      if (file.size > 10 * 1024 * 1024) {
        showError(`Le fichier ${file.name} est trop volumineux (max 10MB)`);
        return;
      }

      if (audioFiles.value.length >= 5) {
        showError("Maximum 5 fichiers audio autorisés");
        return;
      }

      const url = URL.createObjectURL(file);
      audioFiles.value.push({
        file,
        url,
        name: file.name,
      });
      formData.value.files.push(file);
    }
  });
};

const removeFile = (index: number) => {
  const fileToRemove = audioFiles.value[index];
  URL.revokeObjectURL(fileToRemove.url);
  audioFiles.value.splice(index, 1);
  formData.value.files.splice(index, 1);
};

const submitHandler = async (data: ArtistRequestFormData) => {
  if (audioFiles.value.length === 0) {
    showError("Veuillez ajouter au moins un fichier audio pour présenter votre travail");
    return;
  }

  try {
    loading.value = true;
    // Upload chaque fichier et récupère les @id
    const fileIds: string[] = [];
    for (const audio of audioFiles.value) {
      const uploadResponse = await apiClient.music.upload(audio.file);
      if (!uploadResponse["@id"]) {
        throw new Error("Erreur lors de l'upload d'un fichier audio");
      }
      fileIds.push(uploadResponse["@id"]);
    }

    // Envoi la demande artiste avec les @id
    await apiClient.artistRequest.create({
      message: data.message,
      files: fileIds,
    });

    showSuccess("Demande envoyée avec succès ! Vous recevrez une réponse par email.");
    emit("success");
    closeModal();
  } catch (error) {
    console.error("Erreur lors de l'envoi de la demande:", error);
    showError("Erreur lors de l'envoi de la demande. Veuillez réessayer.");
  } finally {
    loading.value = false;
  }
};

const handleAccept = async () => {
  if (!props.request) return;

  try {
    adminActionLoading.value = true;
    emit("accept", props.request);
  } finally {
    adminActionLoading.value = false;
  }
};

const handleDecline = async () => {
  if (!props.request) return;

  try {
    adminActionLoading.value = true;
    emit("decline", props.request);
  } finally {
    adminActionLoading.value = false;
  }
};

const closeModal = () => {
  audioFiles.value.forEach((file) => {
    URL.revokeObjectURL(file.url);
  });

  formData.value = {
    message: "",
    files: [],
  };
  audioFiles.value = [];

  emit("close");
};

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === "Escape" && props.isVisible) {
    closeModal();
  }
};

// Si on est en vue admin, on ajoute les valeurs par défauts de message et on ajoute les fichiers audio contenus dans la demande

watch(
  () => [props.isAdminView, props.request],
  async ([isAdminView, request]) => {
    if (isAdminView && request) {
      formData.value.message = (request as ArtistRequest).message || "";

      // Récupération des fichiers audio
      if ((request as ArtistRequest).files && (request as ArtistRequest).files.length > 0) {
        audioFiles.value = [];
        for (const file of (request as ArtistRequest).files) {
          const fileIdParts = file.split("/");
          const fileId = fileIdParts[fileIdParts.length - 1];

          try {
            const fileResponse = await apiClient.music.getFile(fileId);

            const audioUrl = await streamToAudioUrl(fileResponse);

            // Création d'un fichier audio fictif
            const dummyFile = new File([], `audio-${fileId}.mp3`, { type: "audio/mpeg" });

            audioFiles.value.push({
              file: dummyFile,
              url: audioUrl,
              name: `Fichier audio ${audioFiles.value.length + 1}`,
            });
          } catch (error) {
            console.error(`Erreur lors du chargement du fichier ${fileId}:`, error);
          }
        }
      }
    } else {
      audioFiles.value = [];
      formData.value.message = "";
    }
  },
  { immediate: true },
);

onMounted(() => {
  document.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
  document.removeEventListener("keydown", handleKeydown);
  audioFiles.value.forEach((file) => {
    URL.revokeObjectURL(file.url);
  });
});
</script>

<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
    @click.self="closeModal"
  >
    <div
      class="bg-[#1a0725] border border-[#440a50] rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden"
      @click.stop
    >
      <div class="p-6 border-b border-[#440a50] relative">
        <h2 class="text-xl font-semibold text-white text-center">Demande de compte artiste</h2>
        <button
          @click="closeModal"
          class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-[#440a50] transition-colors"
        >
          <span class="text-white text-xl">&times;</span>
        </button>
      </div>

      <FormKit
        type="form"
        :value="formData"
        @submit="submitHandler"
        :actions="false"
        :config="{ validationVisibility: 'live' }"
      >
        <div class="p-6 max-h-[70vh] overflow-y-auto scrollbar-custom">
          <div class="space-y-6">
            <div v-if="!isAdminView" class="bg-[#2a0d35] border border-[#440a50] rounded-lg p-4">
              <h3 class="text-white font-medium mb-2">📝 Instructions</h3>
              <p class="text-white/70 text-sm leading-relaxed">
                Décrivez votre motivation et votre expérience musicale. Vous pouvez également
                ajouter des fichiers audio de vos créations pour donner un aperçu de votre style
                musical à l'équipe de modération.
              </p>
            </div>

            <FormKit
              type="textarea"
              name="message"
              label="Message de motivation"
              placeholder="Parlez-nous de votre parcours musical, vos créations, et pourquoi vous souhaitez rejoindre Beatly en tant qu'artiste..."
              :validation="isAdminView && request ? '' : 'required|length:20,1000'"
              :validation-messages="{
                required: 'Le message est obligatoire',
                length: 'Le message doit contenir entre 20 et 1000 caractères',
              }"
              :readonly="isAdminView && request"
              rows="6"
              :classes="{
                wrapper: 'space-y-2',
                label: 'block text-sm font-medium text-white',
                input:
                  'w-full px-3 py-2 bg-[#2a0d35] border border-[#440a50] rounded-lg text-white placeholder-white/50 focus:outline-none focus:border-[#5a0f60] transition-colors resize-none' +
                  (isAdminView && request ? ' cursor-not-allowed opacity-75' : ''),
                message: 'text-red-400 text-sm',
                help: 'text-white/50 text-xs',
              }"
              :help="
                isAdminView && request
                  ? 'Message de la demande'
                  : 'Minimum 20 caractères, maximum 1000'
              "
            />

            <div>
              <label
                v-if="!isAdminView || (isAdminView && request?.files.length)"
                class="block text-sm font-medium text-white mb-2"
              >
                Fichiers audio {{ isAdminView && request ? "(lecture seule)" : "(optionnel)" }}
              </label>
              <div class="space-y-4">
                <div
                  v-if="!isAdminView || !request"
                  class="border-2 border-dashed rounded-lg p-6 text-center transition-colors"
                  :class="[
                    dragActive
                      ? 'border-[#5a0f60] bg-[#5a0f60]/10'
                      : 'border-[#440a50] hover:border-[#5a0f60]',
                  ]"
                  @dragover="handleDragOver"
                  @dragleave="handleDragLeave"
                  @drop="handleDrop"
                >
                  <input
                    type="file"
                    multiple
                    accept="audio/*"
                    @change="handleFileChange"
                    class="hidden"
                    id="audio-files"
                  />
                  <label for="audio-files" class="cursor-pointer">
                    <div class="text-white/70 mb-2">
                      <span class="text-2xl">🎵</span>
                    </div>
                    <p class="text-white font-medium mb-1">Ajouter des fichiers audio</p>
                    <p class="text-white/50 text-sm">
                      Glissez vos fichiers ici ou cliquez pour sélectionner
                    </p>
                    <p class="text-white/30 text-xs mt-1">
                      Formats acceptés : MP3, WAV, FLAC, AAC (max 10MB par fichier, 5 fichiers max)
                    </p>
                  </label>
                </div>

                <div v-if="audioFiles.length > 0" class="space-y-3">
                  <h4 class="text-white font-medium">
                    {{ isAdminView && request ? "" : "Fichiers ajoutés :" }}
                  </h4>
                  <div
                    v-for="(audioFile, index) in audioFiles"
                    :key="index"
                    class="bg-[#2a0d35] border border-[#440a50] rounded-lg p-4"
                  >
                    <div class="flex items-center justify-between mb-3">
                      <div class="flex items-center space-x-3">
                        <div
                          class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center"
                        >
                          <span class="text-white text-lg">🎵</span>
                        </div>
                        <div>
                          <p class="text-white font-medium truncate max-w-xs">
                            {{ audioFile.name }}
                          </p>
                          <p v-if="!isAdminView || !request" class="text-white/50 text-sm">
                            {{ (audioFile.file.size / 1024 / 1024).toFixed(2) }} MB
                          </p>
                          <p v-else class="text-white/50 text-sm">Fichier de la demande</p>
                        </div>
                      </div>
                      <button
                        v-if="!isAdminView || !request"
                        @click="removeFile(index)"
                        class="p-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-colors"
                        title="Supprimer le fichier"
                      >
                        🗑️
                      </button>
                    </div>

                    <audio
                      :src="audioFile.url"
                      controls
                      class="w-full h-8"
                      style="filter: sepia(1) hue-rotate(240deg) saturate(2) brightness(1.2)"
                    >
                      Votre navigateur ne supporte pas l'élément audio.
                    </audio>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-[#440a50] bg-[#2a0d35]/50">
          <div v-if="isAdminView && request" class="flex justify-between space-x-3">
            <div>
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 border border-[#440a50] text-white rounded-lg hover:bg-[#440a50] transition-colors"
              >
                Fermer
              </button>
            </div>
            <div class="flex space-x-3">
              <button
                type="button"
                @click="handleDecline"
                :disabled="adminActionLoading"
                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="adminActionLoading">Traitement...</span>
                <span v-else>Décliner</span>
              </button>
              <button
                type="button"
                @click="handleAccept"
                :disabled="adminActionLoading"
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="adminActionLoading">Traitement...</span>
                <span v-else>Accepter</span>
              </button>
            </div>
          </div>

          <div v-else class="flex justify-between items-center">
            <div class="text-white/50 text-sm">{{ audioFiles.length }}/5 fichiers ajoutés</div>
            <div class="flex space-x-3">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 border border-[#440a50] text-white rounded-lg hover:bg-[#440a50] transition-colors"
              >
                Annuler
              </button>
              <FormKit
                type="submit"
                :disabled="loading"
                :classes="{
                  input:
                    'px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed',
                }"
              >
                <span v-if="loading">Envoi en cours...</span>
                <span v-else>Envoyer la demande</span>
              </FormKit>
            </div>
          </div>
        </div>
      </FormKit>
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
