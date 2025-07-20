<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useHead } from "@unhead/vue";

import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import type { Artist, Music, Album } from "@/utils/types";
import MusicList from "@/components/lists/MusicList.vue";
import LandingButton from "@/components/buttons/LandingButton.vue";
import arrowLeft from "@/assets/icons/arrow-left-light.svg";
import arrowRight from "@/assets/icons/arrow-right-light.svg";
import defaultWallpaper from "@/assets/images/default-wallpaper-playlist.jpg";
import defaultCover from "@/assets/images/default-cover.png";
import AlbumPlayableCover from "@/components/albums/AlbumPlayableCover.vue";
import FastPlayButton from "@/components/buttons/FastPlayButton.vue";
import AlbumPlayableCard from "@/components/cards/AlbumPlayableCard.vue";
import { ressourceUrl } from "@/utils/tools";

const artist = ref<Artist | null>(null);
useHead({
  title: computed(() => `Beatly | Détail de ${artist?.value?.name || "l'artiste"}`),
});

const router = useRouter();
const route = useRoute();
const { apiClient } = useApiClient();
const loading = ref<boolean>(false);
const bestListenedSongList = ref<{ music: Music }[]>([]);
const lastAlbum = ref<Album | null>(null);
const artistId = route.params.id as string;
const albumSlider = ref<HTMLElement | null>(null);
const showNextButton = ref(false);
const showPrevButton = ref(false);
const isClickedToPlay = ref(false);

const handleBack = () => {
  router.go(-1);
};

function scrollAlbumSlider() {
  if (albumSlider.value) {
    const elementWidth = albumSlider.value.firstElementChild?.clientWidth || 0;
    const gap = 64;
    const scrollDistance = (elementWidth + gap) * 3;
    albumSlider.value.scrollBy({ left: scrollDistance, behavior: "smooth" });
  }
}

function scrollAlbumSliderBack() {
  if (albumSlider.value) {
    const elementWidth = albumSlider.value.firstElementChild?.clientWidth || 0;
    const gap = 64;
    const scrollDistance = (elementWidth + gap) * 3;
    albumSlider.value.scrollBy({ left: -scrollDistance, behavior: "smooth" });
  }
}

function checkScrollEnd() {
  if (albumSlider.value) {
    const { scrollLeft, scrollWidth, clientWidth } = albumSlider.value;
    showNextButton.value = scrollLeft + clientWidth < scrollWidth - 10; // 10px de marge
    showPrevButton.value = scrollLeft > 10;
  }
}

const handlePlayAlbum = (event: Event) => {
  const target = event.target as HTMLElement;
  const isButtonPlayClick = target.closest("[data-play]");

  if (!isButtonPlayClick) {
    if (target.closest("[data-lastAlbum]")) {
      router.push({ name: "Album", params: { id: lastAlbum.value?.id.toString() } });
      return;
    } else if (target.closest("[data-album]")) {
      const albumElement = target.closest("[data-album]");
      const albumId = albumElement?.getAttribute("data-album-id");
      if (albumId) {
        router.push({ name: "Album", params: { id: albumId } });
      }
    }
  } else {
    isClickedToPlay.value = true;
  }
};

const handlePlayStateChange = (newState: boolean) => {
  isClickedToPlay.value = newState;
};

onMounted(async () => {
  if (artistId) {
    loading.value = true;
    try {
      const response = await apiClient.artist.get(artistId);
      if (response) {
        artist.value = response;

        if (response.musics) {
          // @ts-expect-error Le tableau est modifié pour inclure l'artiste actuel afin de faire fonctionner les informations du player
          bestListenedSongList.value = response.musics
            .sort((a, b) => (b.listeningsNumber || 0) - (a.listeningsNumber || 0))
            .slice(0, 5)
            .map((music) => ({
              music: {
                ...music,
                mainArtist: {
                  ...music.mainArtist,
                  name: artist.value?.name,
                },
              },
            }));
        }

        if (response.albums) {
          const sortedAlbums = response.albums.sort((a, b) => {
            const dateA = new Date(a.releaseDate || 0).getTime();
            const dateB = new Date(b.releaseDate || 0).getTime();
            return dateB - dateA;
          });
          lastAlbum.value = sortedAlbums[0] || null; // Prendre le premier album (le plus récent)
        }
      }
      loading.value = false;
    } catch (error) {
      console.error("Erreur lors du chargement de l'artiste:", error);
      loading.value = false;
    }
  }
  setTimeout(() => {
    checkScrollEnd();
  }, 100);
});
</script>

<template>
  <InAppLayout :loading="loading" padding="p-0">
    <div v-if="artist">
      <div
        :style="{
          backgroundImage: artist.wallpaper
            ? `url(${ressourceUrl + artist.wallpaper})`
            : `url(${defaultWallpaper})`,
        }"
        class="bg-cover bg-center object-cover flex flex-row justify-between items-end relative albumBackground mb-12 z-2"
      >
        <div
          class="relative flex flex-row justify-start items-between gap-4 xl:pt-28 lg:pt-24 pt-20 ps-16 z-10"
        >
          <img
            :src="artist.avatar ? ressourceUrl + artist.avatar : defaultCover"
            alt="Album Cover"
            class="xl:w-[200px] lg:w-[150px] xl:h-[200px] lg:h-[150px] w-[125px] h-[125px] object-cover rounded-full -mb-4"
          />
          <div class="flex flex-col items-start justify-end mb-4">
            <span class="lg:mb-8 mb-2">Artiste</span>
            <p class="text-white text-4xl font-bold">{{ artist.name }}</p>
          </div>
        </div>
        <!-- Menu -->
      </div>
      <div class="text-white px-4 md:px-8 lg:px-16">
        <div class="flex flex-col xl:flex-row gap-8 xl:gap-12">
          <!-- Card Album - Gauche -->
          <div v-if="lastAlbum" class="flex-shrink-0 xl:w-1/3">
            <h2 class="text-2xl lg:text-3xl font-bold mb-6">Dernière sortie</h2>
            <div class="rounded-lg bg-[#400a52]/90 p-6 lg:p-8">
              <div class="flex flex-row flex-wrap gap-4" @click="handlePlayAlbum">
                <div class="flex-shrink-0 xl:self-center">
                  <AlbumPlayableCover
                    :isPlayable="false"
                    :albumCover="lastAlbum.cover ? ressourceUrl + lastAlbum.cover : defaultCover"
                    :album="lastAlbum"
                    class="w-full max-w-[200px] mx-auto sm:mx-0 cursor-pointer"
                    data-lastAlbum
                  />
                </div>
                <div class="flex-1 flex flex-row justify-between items-end">
                  <div class="text-left w-fit" data-lastAlbum>
                    <p class="text-lg lg:text-xl font-bold mb-2 cursor-pointer">
                      {{ lastAlbum.title }}
                    </p>
                    <p class="text-sm text-white/70 cursor-pointer">
                      {{
                        lastAlbum.releaseDate
                          ? new Date(lastAlbum.releaseDate).toLocaleDateString("fr-FR", {
                              year: "numeric",
                              month: "long",
                              day: "numeric",
                            })
                          : ""
                      }}
                    </p>
                  </div>
                  <div
                    class="flex justify-center sm:justify-start lg:justify-center xl:self-start w-fit h-fit"
                  >
                    <div
                      class="bg-black/50 opacity-90 hover:bg-black/60 hover:opacity-100 transition-all rounded-full p-4 h-fit cursor-pointer"
                      data-play
                    >
                      <FastPlayButton
                        :musicId="lastAlbum.id"
                        origin="album"
                        :parentId="lastAlbum['@id']"
                        class="w-6 h-6 lg:w-6 lg:h-6"
                        :isClickedToPlay="isClickedToPlay"
                        @update:isClickedToPlay="handlePlayStateChange"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Liste Top Titres - Droite -->
          <div class="flex-1 xl:w-2/3">
            <h2 class="text-2xl lg:text-3xl font-bold mb-6">Top titres</h2>
            <div class="bg-black/20 rounded-lg p-4 lg:p-6">
              <MusicList
                :musicList="bestListenedSongList"
                :origin="`top-titles`"
                parentId="top_titles"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="mt-8">
        <!-- Liste des albums -->
        <h2 class="text-2xl lg:text-3xl font-bold mb-6 px-8 lg:px-16">Albums</h2>
        <div class="relative">
          <div
            class="flex flex-row justify-start gap-16 overflow-x-auto scrollbar-hide lg:px-16 px-8"
            ref="albumSlider"
            @scroll="checkScrollEnd"
          >
            <AlbumPlayableCard
              v-for="(album, index) in artist.albums"
              :key="album.id"
              :index="index"
              :album="album"
              :artistName="artist.name"
              :releaseYear="new Date(album.releaseDate).getFullYear()"
              @click="handlePlayAlbum"
              data-album
            />
          </div>
          <button
            v-if="showPrevButton"
            class="absolute left-4 top-1/2 p-2 lg:p-6 transform -translate-y-1/2 bg-black opacity-70 rounded-full shadow-md hover:opacity-90 transition-opacity"
            @click="scrollAlbumSliderBack"
          >
            <img :src="arrowLeft" class="w-8 h-8" alt="Flèche gauche" />
          </button>
          <button
            v-if="showNextButton"
            class="absolute right-4 top-1/2 p-2 lg:p-6 transform -translate-y-1/2 bg-black opacity-70 rounded-full shadow-md hover:opacity-90 transition-opacity"
            @click="scrollAlbumSlider"
          >
            <img :src="arrowRight" class="w-8 h-8" alt="Flèche gauche" />
          </button>
        </div>
      </div>
    </div>
    <div
      v-else
      class="absolute inset-0 flex flex-col justify-center items-center gap-2 text-white text-center"
    >
      <p class="text-2xl">Oups...</p>
      <p class="text-lg">Nous ne parvenons pas à trouver cette artiste.</p>
      <LandingButton
        label="Revenir en arrière"
        :icon="arrowLeft"
        type="button"
        @click="handleBack"
      />
    </div>
  </InAppLayout>
</template>

<style scoped>
.albumBackground::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.1));
  z-index: 2;
}
</style>
