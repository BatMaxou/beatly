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
import AddMusicModal from "@/components/modals/AddMusicModal.vue";

const route = useRoute();
const router = useRouter();
const { apiClient } = useApiClient();
const { showError } = useToast();

const album = ref<Album | null>(null);
const loading = ref(true);
const showAddMusicModal = ref(false);
const editingMusic = ref<Music | null>(null);

const coverPreview = ref<string>("");
const wallpaperPreview = ref<string>("");

const albumId = computed(() => route.params.id as string);

const fetchAlbum = async () => {
  loading.value = true;
  try {
    const response = await apiClient.album.get(albumId.value);
    album.value = response;

    coverPreview.value = album.value.cover ? ressourceUrl + album.value.cover : "";
    wallpaperPreview.value = album.value.wallpaper ? ressourceUrl + album.value.wallpaper : "";
  } catch (error) {
    console.error("Erreur lors de la récupération de l'album:", error);
    showError("Erreur lors de la récupération de l'album");
    router.push("/admin/albums");
  } finally {
    loading.value = false;
  }
};

const closeAddMusicModal = () => {
  showAddMusicModal.value = false;
  editingMusic.value = null;
};

const viewMusic = (musicId: number) => {
  const music = album.value?.musics?.find((m) => m.id === musicId);
  if (music) {
    editingMusic.value = music;
    showAddMusicModal.value = true;
  }
};

onMounted(fetchAlbum);
</script>

<template>
  <InAppLayout type="admin" padding="p-10" :loading="loading">
    <div>
      <BackButton to="/admin/albums" />

      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Détail de l'album</h1>
      </div>

      <div v-if="album" class="bg-[#2E0B40] rounded-lg p-6">
        <!-- Wallpaper -->
        <div class="relative mb-8">
          <div class="h-64 bg-gray-800 rounded-lg overflow-hidden">
            <img
              v-if="wallpaperPreview"
              :src="wallpaperPreview"
              alt="Wallpaper"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
              Aucun wallpaper
            </div>
          </div>
        </div>

        <div class="flex items-start space-x-6 mb-8">
          <div class="relative">
            <!-- Cover -->
            <div class="w-32 h-32 bg-gray-800 rounded-lg overflow-hidden">
              <img
                v-if="coverPreview"
                :src="coverPreview"
                alt="Cover"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                Cover
              </div>
            </div>
          </div>

          <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Titre -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Titre</label>
                <p class="text-white text-lg">{{ album.title || "—" }}</p>
              </div>

              <!-- Artiste -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Artiste</label>
                <p class="text-white text-lg">{{ album.artist?.name || "—" }}</p>
              </div>

              <!-- Date de sortie -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Date de sortie</label>
                <p class="text-white text-lg">
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
                <button
                  @click="viewMusic(music.id)"
                  class="p-2 rounded hover:bg-blue-500 transition-colors"
                  title="Consulter"
                >
                  <img :src="eyeLight" class="w-4 h-4" alt="Voir" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Message si aucune musique -->
        <div v-else-if="!album.musics || album.musics.length === 0">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-white">Musiques</h3>
          </div>
          <div class="bg-[#3a1452] rounded-lg p-8 text-center">
            <p class="text-gray-400">Aucune musique dans cet album</p>
          </div>
        </div>

        <AddMusicModal
          :is-visible="showAddMusicModal"
          :album-id="albumId"
          :max-position="album?.musics?.length || 0"
          :album-cover="coverPreview"
          :edit-music="editingMusic"
          :readonly="true"
          @close="closeAddMusicModal"
          @success="closeAddMusicModal"
        />
      </div>
    </div>
  </InAppLayout>
</template>
