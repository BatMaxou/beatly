<script setup lang="ts">
import { defineProps, onMounted, ref, watch } from "vue";
import MusicPlayButton from "@/components/lists/MusicPlayButton.vue";
import type { Music } from "@/utils/types";
// @ts-expect-error Aucune idée de pourquoi il détecte UnifiedMenu comme un module
import UnifiedMenu from "@/components/menus/UnifiedMenu.vue";
import { usePlayerStore } from "@/stores/player";

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

  const isMenuClick =
    target.closest(".album-title-menu") ||
    target.closest("[data-menu]") ||
    target.closest("button[aria-label='Menu options']");

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

const setIsCurrentSongPlaying = (newMusic: Music | null) => {
  if (
    newMusic &&
    props.music &&
    props.music.id === newMusic.id &&
    (!playerStore.queueParent || props.parentId === playerStore.queueParent)
  ) {
    isCurrentSongPlaying.value = true;
  } else {
    isCurrentSongPlaying.value = false;
  }
};

watch(
  () => playerStore.currentMusic,
  (newVal: Music | null, oldVal: Music | null) => {
    if (newVal !== oldVal) {
      setIsCurrentSongPlaying(newVal);
    }
  },
);

onMounted(() => {
  setIsCurrentSongPlaying(playerStore.currentMusic);
});
</script>

<template>
  <div
    class="flex flex-row justify-between items-center h-10 px-4 transition-colors duration-200 ease-in-out cursor-pointer border-b border-gray-200/20 hover:bg-black/80 hover:text-white"
    :class="isCurrentSongPlaying ? 'bg-black/80 text-white' : ''"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
    @click="handlePlaySong"
    @contextmenu="handleContextMenu"
  >
    <div class="flex flex-row items-center gap-2">
      <div class="flex items-center justify-center w-8 min-w-8">
        <MusicPlayButton
          v-if="isHovered || isCurrentSongPlaying"
          :music="music"
          :isClickedToPlay="isClickedToPlay"
          :position="position"
          :parentId="parentId"
          :origin="origin"
          @update:isClickedToPlay="handlePlayStateChange"
        />
        <span v-else class="text-white font-medium">
          {{ index + 1 }}
        </span>
      </div>
      <span class="text-white/30 mx-2"> | </span>
      <span class="font-medium">{{ music.title }}</span>
    </div>
    <div class="text-gray-500 font-normal" data-menu>
      <UnifiedMenu
        ref="menuRef"
        type="albumTitle"
        :element="music"
        :showMenuIcon="isHovered || isCurrentSongPlaying"
        :isFavorite="false"
      />
    </div>
  </div>
</template>
