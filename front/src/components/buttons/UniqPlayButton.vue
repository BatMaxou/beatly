<script setup lang="ts">
import { ref, watch, defineProps } from "vue";
import { usePlayerStore } from "@/stores/player";
import { useToast } from "@/composables/useToast";
import { useApiClient } from "@/stores/api-client";
import playLight from "@/assets/icons/play-light.svg";

const isRotating = ref(false);
const emit = defineEmits(["update:isClickedToPlay"]);
const { showError } = useToast();
const playerStore = usePlayerStore();
const { apiClient } = useApiClient();

const { origin, elementId, isClickedToPlay } = defineProps({
  origin: {
    type: String,
    required: true,
  },
  elementId: {
    type: Number,
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

const getPlaylistQueue = async () => {
  try {
  } catch (error) {
    showError("Erreur lors de la récupération de la playlist");
    return [];
  }
};

watch(
  () => isClickedToPlay,
  (newVal, oldVal) => {
    if (newVal && elementId !== playerStore.currentMusic?.id) {
      if (elementId) {
        // apiClient.music.getFile(music.id).then(async (response) => {
        //   if (response) {
        //     playerStore.setListen(music, await streamToAudioUrl(response));
        //     // PLUS TARD - Ajouter la file d'attente
        //   } else {
        //     showError("Ce titre n'est pas disponible");
        //   }
        // });
        resetPlayState();
      } else {
        showError("Ce titre n'est pas disponible");
        resetPlayState();
        return;
      }
    } else if (newVal && elementId === playerStore.currentMusic?.id) {
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
  <div class="flex justify-center items-center cursor-pointer">
    <img :src="playLight" alt="Play" class="w-20 h-20 box-content" />
  </div>
</template>
