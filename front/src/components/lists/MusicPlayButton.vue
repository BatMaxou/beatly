<script setup lang="ts">
import { defineProps, defineEmits, watch } from "vue";
import playLight from "@/assets/icons/play-light.svg";
import pauseLight from "@/assets/icons/pause-light.svg";
import type { Music } from "@/utils/types";
import { usePlayerStore } from "@/stores/player";
import { useApiClient } from "@/stores/api-client";
import { streamToAudioUrl } from "@/utils/stream";
import { useToast } from "@/composables/useToast";
import { usePlayerPreparation } from "@/composables/usePlayerPreparation";

const playerStore = usePlayerStore();
const { apiClient } = useApiClient();
const { showError } = useToast();
const { storeAdjacentMusicInQueue, loadQueueFile, loadQueue, loadRandomQueue } =
  usePlayerPreparation();
const emit = defineEmits(["update:isClickedToPlay"]);

const { music, position, origin, isClickedToPlay, parentId, musics } = defineProps({
  music: {
    type: Object as () => Music,
    required: true,
  },
  parentId: {
    type: String || null,
    default: null,
  },
  origin: {
    type: String,
    default: null,
  },
  position: {
    type: Number,
    required: true,
  },
  isClickedToPlay: {
    type: Boolean,
    default: false,
  },
  musics: {
    type: Array as () => { music: Music }[] | null,
    default: null,
  },
});

const resetPlayState = () => {
  emit("update:isClickedToPlay", false);
};

watch(
  () => isClickedToPlay,
  async (newVal) => {
    resetPlayState();
    if (newVal && music && music.id !== playerStore.currentMusic?.id) {
      if (music.id) {
        const queueFile = playerStore.queueFile?.find((item) => item.musicId === music.id);
        if (queueFile) {
          await playerStore.setListen(music, queueFile.file, position, parentId);
        } else {
          await apiClient.music.getFile(music.id).then(async (response) => {
            if (response) {
              await playerStore.setListen(
                music,
                await streamToAudioUrl(response),
                position,
                parentId,
              );
            } else {
              showError("Ce titre n'est pas disponible");
            }
          });
        }

        if (origin !== "queue") {
          if (!playerStore.isRandomQueue && parentId !== playerStore.queueParent) {
            await loadQueue(origin, parentId, position, musics);
            await loadQueueFile();
          } else if (playerStore.isRandomQueue) {
            await loadQueue(origin, parentId, position, musics);
            await loadRandomQueue(position);
            await loadQueueFile();
          }
        }

        storeAdjacentMusicInQueue();
      } else {
        showError("Ce titre n'est pas disponible");
        return;
      }
    } else if (newVal && music.id === playerStore.currentMusic?.id) {
      if (playerStore.isPlay) {
        playerStore.setPause();
      } else {
        playerStore.setPlay();
      }
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
