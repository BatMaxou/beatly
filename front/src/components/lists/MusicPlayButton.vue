<script setup lang="ts">
import { defineProps, defineEmits, watch } from "vue";
import playLight from "@/assets/icons/play-light.svg";
import pauseLight from "@/assets/icons/pause-light.svg";
import type { Music } from "@/utils/types";
import { usePlayerStore } from "@/stores/player";
import { useApiClient } from "@/stores/api-client";
import { streamToAudioUrl } from "@/utils/stream";
import { useToast } from "@/composables/useToast";

const playerStore = usePlayerStore();
const { apiClient } = useApiClient();
const { showError } = useToast();
const emit = defineEmits(["update:isClickedToPlay"]);

const { music, isClickedToPlay } = defineProps({
  music: {
    type: Object as () => Music,
    required: true,
  },
  isClickedToPlay: {
    type: Boolean,
    default: false,
  },
});

const resetPlayState = () => {
  emit("update:isClickedToPlay", false);
};

watch(
  () => isClickedToPlay,
  (newVal, oldVal) => {
    if (newVal && music && music.id !== playerStore.currentMusic?.id) {
      if (music.id) {
        apiClient.music.getFile(music.id).then(async (response) => {
          if (response) {
            playerStore.setListen(music, await streamToAudioUrl(response));
            // PLUS TARD - Ajouter la file d'attente
          } else {
            showError("Ce titre n'est pas disponible");
          }
        });
        resetPlayState();
      } else {
        showError("Ce titre n'est pas disponible");
        resetPlayState();
        return;
      }
    } else if (newVal && music.id === playerStore.currentMusic?.id) {
      if (playerStore.isPlay) {
        playerStore.setPause();
      } else {
        playerStore.setPlay();
      }
      resetPlayState();
    }
  },
  { immediate: true },
);
</script>

<template>
  <div class="music-play-button">
    <img
      :src="
        playerStore.isPlay && playerStore.currentMusic?.id === music.id ? pauseLight : playLight
      "
      :alt="playerStore.isPlay && playerStore.currentMusic?.id === music.id ? 'Pause' : 'Play'"
      class="play-icon"
      :class="{ playing: playerStore.isPlay && playerStore.currentMusic?.id === music.id }"
    />
  </div>
</template>

<style scoped>
.music-play-button {
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

.play-icon {
  width: 0.7rem;
  height: 0.7rem;
  transition: transform 0.3s ease;
}

.playing {
  opacity: 0.8;
}
</style>
