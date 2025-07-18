<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from "vue";
import { useApiClient } from "@/stores/api-client";
import AlbumPlayableCard from "@/components/cards/AlbumPlayableCard.vue";
import PlaylistPlayableCard from "@/components/cards/PlaylistPlayableCard.vue";
import HorizontalScroller from "@/components/ui/HorizontalScroller.vue";
import CrossIcon from "@/assets/icons/cross-light.svg";
import type { Album, Artist, Music, Playlist } from "@/utils/types";
import { usePlayerStore } from "@/stores/player";
import ArtistProfile from "../artists/ArtistProfile.vue";

const props = defineProps({
  query: {
    type: String,
    required: true,
  },
  isVisible: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["close"]);

const { apiClient } = useApiClient();
const playerStore = usePlayerStore();
const loading = ref(false);
const albumList = ref<Album[]>([]);
const musicsList = ref<Music[]>([]);
const playlistList = ref<Playlist[]>([]);
const artistList = ref<Artist[]>([]);

const searchResults = async () => {
  if (!props.query.trim()) return;

  const sanitizedQuery = props.query.trim();

  if (sanitizedQuery.length < 1) {
    console.warn("Trop court pour une recherche");
    return;
  }

  if (sanitizedQuery.length > 100) {
    console.warn("Trop long pour une recherche");
    return;
  }

  if (!/^[a-zA-Z0-9\s\-_'àâäéèêëïîôöùûüÿçÀÂÄÉÈÊËÏÎÔÖÙÛÜŸÇ]+$/.test(sanitizedQuery)) {
    console.warn("Caractères non autorisés dans la recherche");
    return;
  }

  loading.value = true;
  try {
    const searchResponse = await apiClient.search.search(sanitizedQuery);

    const musics = searchResponse.results.filter(
      (item): item is Music => (item as Music)["@type"] === "Music",
    ) as Music[];

    const albums = searchResponse.results.filter(
      (item): item is Album => (item as Album)["@type"] === "Album",
    ) as Album[];

    const playlists = searchResponse.results.filter(
      (item): item is Playlist =>
        (item as Playlist)["@type"] === "Playlist" ||
        (item as Playlist)["@type"] === "PlatformPlaylist",
    ) as Playlist[];

    const artists = searchResponse.results.filter(
      (item): item is Artist => (item as Artist)["@type"] === "Artist",
    ) as Artist[];

    musicsList.value = musics;
    albumList.value = albums;
    playlistList.value = playlists;
    artistList.value = artists;
  } catch (error) {
    console.error("Erreur lors de la recherche:", error);
  } finally {
    loading.value = false;
  }
};

const closeModal = () => {
  musicsList.value = [];
  albumList.value = [];
  playlistList.value = [];
  artistList.value = [];

  emit("close");
};

watch(
  () => props.query,
  () => {
    if (props.query && props.isVisible) {
      searchResults();
    }
  },
  { immediate: true },
);

// Bloquer le scroll du body quand la modal est visible
watch(
  () => props.isVisible,
  (isVisible) => {
    if (isVisible) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
  },
  { immediate: true },
);

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === "Escape") {
    closeModal();
  }
};

onMounted(() => {
  document.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
  document.removeEventListener("keydown", handleKeydown);
  document.body.style.overflow = "";
});
</script>

<template>
  <div
    class="bg-gradient-to-b bg-[#1a0725] overflow-hidden rounded-t-2xl"
    @click.stop
    @wheel.stop
    @scroll.stop
  >
    <!-- Header -->
    <div class="sticky top-0 bg-gradient-to-b from-[#1a0b2e] to-transparent p-6 pb-4 z-10">
      <div class="flex items-center justify-between">
        <h2 class="text-white text-3xl font-bold">Résultats pour "{{ query }}"</h2>
        <button @click="closeModal" class="text-white hover:text-gray-300 transition-colors p-2">
          <img :src="CrossIcon" alt="Fermer la sidebar" class="w-6 h-6" />
        </button>
      </div>
    </div>

    <!-- Content -->
    <div
      :class="
        'px-4 pb-6 overflow-y-auto search-content scrollbar-hide' +
        (playerStore.isPlayerActive ? ' search-content-resized' : '')
      "
      @click.stop
      @wheel.stop
    >
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
      </div>

      <!-- Results -->
      <div v-else class="space-y-8 pb-20">
        <!-- Musiques -->
        <div v-if="musicsList.length > 0">
          <h3 class="text-white text-2xl font-bold mb-4">Musiques</h3>
          <HorizontalScroller inline-padding="px-0">
            <div v-for="music in musicsList" :key="music['@id']">
              <AlbumPlayableCard
                :music="music"
                :artistName="music.album.artist?.name"
                type="single"
              />
            </div>
          </HorizontalScroller>
        </div>

        <!-- Albums -->
        <div v-if="albumList.length > 0">
          <h3 class="text-white text-2xl font-bold mb-4">Albums</h3>
          <HorizontalScroller inline-padding="px-0">
            <AlbumPlayableCard
              v-for="album in albumList"
              :key="album.id"
              :album="album"
              :artistName="album.artist?.name"
              :releaseYear="new Date(album.releaseDate).getFullYear()"
            />
          </HorizontalScroller>
        </div>

        <!-- Artistes -->
        <div v-if="artistList.length > 0">
          <h3 class="text-white text-2xl font-bold mb-4">Artistes</h3>
          <HorizontalScroller inline-padding="px-0">
            <ArtistProfile v-for="artist in artistList" :key="artist.id" :artist="artist" />
          </HorizontalScroller>
        </div>

        <!-- Playlists -->
        <div v-if="playlistList.length > 0">
          <h3 class="text-white text-2xl font-bold mb-4">Playlists</h3>
          <HorizontalScroller inline-padding="px-0">
            <PlaylistPlayableCard
              v-for="playlist in playlistList"
              :key="playlist.id"
              :playlist="playlist"
            />
          </HorizontalScroller>
        </div>

        <div
          v-if="
            !loading &&
            musicsList.length === 0 &&
            albumList.length === 0 &&
            playlistList.length === 0 &&
            artistList.length === 0
          "
          class="text-center py-16"
        >
          <div class="text-gray-400 text-lg">Aucun résultat trouvé pour "{{ query }}"</div>
          <div class="text-gray-500 text-sm mt-2">Essayez avec des mots-clés différents</div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.search-content {
  max-height: calc(100vh - 4rem);
  overflow-y: auto;
  pointer-events: auto;
  touch-action: auto;
}

.search-content-resized {
  max-height: calc(100vh - 9rem);
  overflow-y: auto;
}

.bg-gradient-to-b {
  pointer-events: auto;
  touch-action: none;
}
</style>
