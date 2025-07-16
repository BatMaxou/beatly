<script setup lang="ts">
import { defineProps, onMounted, ref, watch } from "vue";
import MusicPlayButton from "@/components/lists/MusicPlayButton.vue";
import type { Music } from "@/utils/types";
import defaultCover from "@/assets/images/default-cover.png";
import { usePlayerStore } from "@/stores/player";
// @ts-expect-error Aucune idée de pourquoi il détecte UnifiedMenu comme un module
import UnifiedMenu from "../menus/UnifiedMenu.vue";

const props = defineProps<{
  music: Music;
  index: number;
  position: number;
  parentId?: string;
  origin: string;
}>();
const isHovered = ref(false);
const isClickedToPlay = ref(false);
const isCurrentSongPlaying = ref(false);
const ressourceUrl = import.meta.env.VITE_API_RESSOURCES_URL;
const playerStore = usePlayerStore();
const menuRef = ref<InstanceType<typeof UnifiedMenu> | null>(null);

const handleMouseEnter = () => {
  isHovered.value = true;
};

const handleMouseLeave = () => {
  isHovered.value = false;
};

const handlePlaySong = (event: Event) => {
  const target = event.target as HTMLElement;

  const isMenuClick = target.closest(".album-title-menu") || target.closest("[data-menu]");

  if (isMenuClick) {
    return;
  }

  isClickedToPlay.value = true;
};

const handleContextMenu = (event: MouseEvent) => {
  event.preventDefault();

  // Vérifie si le clic droit est fait dans le menu ou le bouton
  const target = event.target as HTMLElement;
  const isMenuClick =
    target.closest(".z-50") ||
    target.closest("button[aria-label='Menu options']") ||
    target.closest("[data-menu]");

  if (isMenuClick) {
    return;
  }

  if (menuRef.value) {
    const x = event.clientX;
    const y = event.clientY;
    menuRef.value.openMenu(x, y);
  }
};

const handlePlayStateChange = (newState: boolean) => {
  isClickedToPlay.value = newState;
};

const setIsCurrentSongPlaying = () => {
  if (
    playerStore.currentMusic &&
    props.music &&
    props.music.id === playerStore.currentMusic.id &&
    (!playerStore.queueParent || props.parentId === playerStore.queueParent)
  ) {
    isCurrentSongPlaying.value = true;
  } else {
    isCurrentSongPlaying.value = false;
  }
};

watch(
  () => playerStore.currentMusic,
  () => {
    setIsCurrentSongPlaying();
  },
);

watch(
  () => playerStore.isRandomQueue,
  () => {
    setIsCurrentSongPlaying();
  },
);

onMounted(() => {
  setIsCurrentSongPlaying();
});
</script>

<template>
  <div
    class="flex flex-row justify-between items-center h-14 px-4 transition-colors duration-200 ease-in-out cursor-pointer border-b border-gray-200/20 hover:bg-black/80 hover:text-white"
    :class="isCurrentSongPlaying ? 'bg-black/80 text-white' : ''"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
    @click="handlePlaySong"
    @contextmenu="handleContextMenu"
  >
    <div class="flex flex-row items-center w-full gap-2">
      <div class="relative flex items-center justify-center w-12 min-w-12">
        <img
          :src="music.cover ? ressourceUrl + music.cover : defaultCover"
          class="w-12 h-12 rounded"
        />
        <div
          v-if="isHovered"
          class="absolute inset-0 flex items-center justify-center bg-black/50 rounded"
        >
          <MusicPlayButton
            :music="music"
            :isClickedToPlay="isClickedToPlay"
            :position="position"
            :parentId="parentId"
            :origin="origin"
            @update:isClickedToPlay="handlePlayStateChange"
          />
        </div>
      </div>
      <div class="flex flex-col">
        <span class="font-medium text-white">{{ music.title }}</span>
        <p v-if="origin === 'playlist'" class="font-medium text-white">
          <span v-for="artist in music.artists" :key="artist.id" class="text-white/70 text-sm">
            {{ artist.name }}
          </span>
        </p>
      </div>
    </div>
    <div class="text-gray-500 font-normal">
      <UnifiedMenu
        ref="menuRef"
        type="queue"
        :element="music"
        :isFavorite="music.isFavorite"
        :showMenuIcon="isHovered || isCurrentSongPlaying"
      />
    </div>
  </div>
</template>
