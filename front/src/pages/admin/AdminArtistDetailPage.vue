<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useHead } from "@unhead/vue";

import InAppLayout from "@/components/layout/InAppLayout.vue";
import BackButton from "@/components/navigation/BackButton.vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { Artist } from "@/utils/types";
import { ressourceUrl } from "@/utils/tools";

const artist = ref<Artist | null>(null);
useHead({
  title: computed(() => `Beatly | Détail de ${artist?.value?.name || "l'artiste"}`),
});

const route = useRoute();
const router = useRouter();
const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();

const loading = ref(true);
const sendingPassword = ref(false);

const avatarPreview = ref<string>("");
const wallpaperPreview = ref<string>("");

const artistId = computed(() => route.params.id as string);

const fetchArtist = async () => {
  loading.value = true;
  try {
    const response = await apiClient.artist.get(artistId.value);
    artist.value = response;

    avatarPreview.value = artist.value.avatar ? ressourceUrl + artist.value.avatar : "";
    wallpaperPreview.value = artist.value.wallpaper ? ressourceUrl + artist.value.wallpaper : "";
  } catch (error) {
    console.error("Erreur lors de la récupération de l'artiste:", error);
    showError("Erreur lors de la récupération de l'artiste");
    router.push("/admin/artistes");
  } finally {
    loading.value = false;
  }
};
const getAlbumMusics = (albumId: number) => {
  const musics = artist.value?.musics?.filter((music) => {
    return music.album.id === albumId;
  });
  return musics ? musics?.length || 0 : 0;
};

const sendNewPassword = async () => {
  if (!artist.value?.email) {
    showError("Aucun email disponible pour cet artiste");
    return;
  }

  sendingPassword.value = true;
  try {
    await apiClient.user.forgotPassword({ email: artist.value.email });
    showSuccess(`Nouveau mot de passe envoyé à ${artist.value.email}`);
  } catch (error) {
    console.error("Erreur lors de l'envoi du mot de passe:", error);
    showError("Erreur lors de l'envoi du nouveau mot de passe");
  } finally {
    sendingPassword.value = false;
  }
};

const goToAlbum = (albumId: number) => {
  router.push(`/admin/albums/${albumId}?view=true`);
};

onMounted(fetchArtist);
</script>

<template>
  <InAppLayout type="admin" padding="p-10" :loading="loading">
    <div>
      <BackButton to="/admin/artistes" />

      <div class="flex items-center justify-start items-baseline gap-4 mb-6">
        <h1 class="text-3xl font-bold">Détail de l'artiste</h1>
        <span class="text-xl font-bold">Id: {{ artist.id }}</span>
      </div>

      <div v-if="artist" class="bg-[#2E0B40] rounded-lg p-6">
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

        <!-- Avatar and basic info -->
        <div class="flex items-start space-x-6 mb-8">
          <div class="relative">
            <div
              class="w-32 h-32 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full overflow-hidden"
            >
              <img
                v-if="avatarPreview"
                :src="avatarPreview"
                alt="Avatar"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-white">
                Avatar
              </div>
            </div>
          </div>

          <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Nom -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Nom</label>
                <p class="text-white text-lg">{{ artist.name || "—" }}</p>
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Email</label>
                <p class="text-white text-lg">{{ artist.email || "—" }}</p>
              </div>

              <!-- Reset Password -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Mot de passe</label>
                <button
                  @click="sendNewPassword"
                  :disabled="sendingPassword || !artist.email"
                  class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {{ sendingPassword ? "Envoi en cours..." : "Envoyer nouveau mot de passe" }}
                </button>
              </div>

              <!-- Albums -->
              <div>
                <label class="block text-sm font-medium text-white mb-2">Albums</label>
                <p class="text-white text-lg">
                  {{ artist.albums?.length || 0 }} album{{
                    (artist.albums?.length || 0) > 1 ? "s" : ""
                  }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div v-if="artist.albums && artist.albums.length > 0">
          <h3 class="text-xl font-bold text-white mb-4">Albums</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="album in artist.albums"
              :key="album.id"
              class="bg-[#3a1452] rounded-lg p-4 cursor-pointer hover:bg-[#4a1562] transition-colors"
              @click="goToAlbum(album.id)"
            >
              <div class="aspect-square bg-gray-800 rounded-lg mb-3 overflow-hidden">
                <img
                  v-if="album.cover"
                  :src="ressourceUrl + album.cover"
                  :alt="album.title"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                  Cover
                </div>
              </div>
              <h4 class="text-white font-medium">{{ album.title }}</h4>
              <p class="text-gray-400 text-sm">
                {{ getAlbumMusics(album.id) }} musique{{
                  (album.musics?.length || 0) > 1 ? "s" : ""
                }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </InAppLayout>
</template>
