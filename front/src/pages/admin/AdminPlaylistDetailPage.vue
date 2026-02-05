<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import BackButton from "@/components/navigation/BackButton.vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { Playlist, Music } from "@/utils/types";
import { ressourceUrl } from "@/utils/tools";
import { convertDurationInMinutes } from "@/sharedFunctions";
import eyeLight from "@/assets/icons/eye-light.svg";
import AddMusicModal from "@/components/modals/AddMusicModal.vue";

const route = useRoute();
const router = useRouter();
const { apiClient } = useApiClient();
const { showError } = useToast();

const playlist = ref<Playlist | null>(null);
const loading = ref(true);
const showMusicModal = ref(false);
const selectedMusic = ref<Music | null>(null);

const coverPreview = ref<string>("");
const wallpaperPreview = ref<string>("");

const playlistId = computed(() => route.params.id as string);

const fetchPlaylist = async () => {
  loading.value = true;
  try {
    const response = await apiClient.playlist.get(playlistId.value);
    playlist.value = response;

    coverPreview.value = playlist.value.cover ? ressourceUrl + playlist.value.cover : "";
    wallpaperPreview.value = playlist.value.wallpaper
      ? ressourceUrl + playlist.value.wallpaper
      : "";
  } catch (error) {
    console.error("Erreur lors de la récupération de la playlist:", error);
    showError("Erreur lors de la récupération de la playlist");
    router.push("/admin/playlists");
  } finally {
    loading.value = false;
  }
};

const closeMusicModal = () => {
  showMusicModal.value = false;
  selectedMusic.value = null;
};

const viewMusic = (musicId: number) => {
  const playlistMusic = playlist.value?.musics?.find((pm) => pm.music.id === musicId);
  if (playlistMusic) {
    selectedMusic.value = playlistMusic.music;
    showMusicModal.value = true;
  }
};

onMounted(fetchPlaylist);
</script>

<template>
  <InAppLayout type="admin" padding="p-10" :loading="loading">
    <div>
      <BackButton to="/admin/playlists" />

      <div class="flex items-center justify-start items-baseline gap-4 mb-6">
        <h1 class="text-3xl font-bold">Détail de la playlist</h1>
        <span class="text-xl font-bold">Id: {{ playlist.id }}</span>
      </div>

      <div v-if="playlist" class="bg-[#2E0B40] rounded-lg p-6">
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
          </div>

          <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Titre -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Titre</label>
                <p class="text-white text-lg">{{ playlist.title || "—" }}</p>
              </div>

              <!-- Créateur -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Créateur</label>
                <p class="text-white text-lg">{{ playlist.creator?.name || "—" }}</p>
              </div>

              <!-- Type -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Type</label>
                <span
                  v-if="playlist['@type']"
                  :class="[
                    'px-3 py-1 rounded text-sm font-medium',
                    playlist['@type'] === 'Playlist'
                      ? 'bg-blue-100 text-blue-800'
                      : 'bg-purple-100 text-purple-800',
                  ]"
                >
                  {{ playlist["@type"] }}
                </span>
                <span v-else class="text-white text-lg">—</span>
              </div>

              <!-- Musiques -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Musiques</label>
                <p class="text-white text-lg">
                  {{ playlist.musics?.length || 0 }} musique{{
                    (playlist.musics?.length || 0) > 1 ? "s" : ""
                  }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Liste des musiques -->
        <div v-if="playlist.musics && playlist.musics.length > 0">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-white">Musiques</h3>
          </div>
          <div class="space-y-3">
            <div
              v-for="(music, index) in playlist.musics"
              :key="music.music.id"
              class="bg-[#3a1452] rounded-lg p-4 flex items-center space-x-4 group hover:bg-[#4a1562] transition-colors"
            >
              <div
                class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center text-white font-bold"
              >
                {{ index + 1 }}
              </div>
              <div class="flex-1">
                <h4 class="text-white font-medium">{{ music.music.title }}</h4>
                <p class="text-gray-400 text-sm">
                  {{ music.music.album?.title || "Album inconnu" }} -
                  {{ music.music.album?.artist?.name || "Artiste inconnu" }}
                </p>
                <p class="text-gray-400 text-sm">
                  {{ convertDurationInMinutes(`${music.music.duration}`) || "Durée inconnue" }}
                </p>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="viewMusic(music.music.id)"
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
        <div v-else-if="!playlist.musics || playlist.musics.length === 0">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-white">Musiques</h3>
          </div>
          <div class="bg-[#3a1452] rounded-lg p-8 text-center">
            <p class="text-gray-400">Aucune musique dans cette playlist</p>
          </div>
        </div>

        <!-- View Music Modal -->
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
    </div>
  </InAppLayout>
</template>
