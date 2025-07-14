<script setup lang="ts">
import { watch, defineProps } from "vue";
import { useToast } from "@/composables/useToast";
import { usePlayerStore } from "@/stores/player";
import { useApiClient } from "@/stores/api-client";
import { usePlayerPreparation } from "@/composables/usePlayerPreparation";
import playLight from "@/assets/icons/play-light.svg";
import pauseLight from "@/assets/icons/pause-light.svg";
import { streamToAudioUrl } from "@/utils/stream";

const { showError } = useToast();
const playerStore = usePlayerStore();
const { apiClient } = useApiClient();
const { storeAdjacentMusicInQueue, loadQueueFile } = usePlayerPreparation();
const emit = defineEmits(["update:isClickedToPlay"]);

const { origin, parentId, isClickedToPlay } = defineProps({
  origin: {
    type: String,
    required: true,
  },
  parentId: {
    type: String,
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

const addQueue = async (origin: string) => {
  if (origin === "album") {
    return await apiClient.queue.add({ album: parentId, currentPosition: 1 });
  } else if (origin === "playlist") {
    return await apiClient.queue.add({ playlist: parentId, currentPosition: 1 });
  }
  return Promise.reject(new Error("Invalid origin"));
};

watch(
  () => isClickedToPlay,
  async (newVal) => {
    if (newVal && parentId !== playerStore.queueParent) {
      if (parentId) {
        if (playerStore.queue) {
          playerStore.clearQueue();
        }
        const queue = await addQueue(origin);
        if (queue) {
          playerStore.setQueue(queue, parentId);
          playerStore.setQueueFile(await loadQueueFile());
          const firstMusic = Object.entries(queue.queueItems)[0][1];

          const queueFile = playerStore.queueFile?.find(
            (item) => item.musicId === firstMusic.music.id,
          );
          if (queueFile) {
            playerStore.setListen(firstMusic.music, queueFile.file, firstMusic.position);
          } else {
            apiClient.music.getFile(firstMusic.music.id).then(async (response) => {
              if (response) {
                playerStore.setListen(
                  firstMusic.music,
                  await streamToAudioUrl(response),
                  firstMusic.position,
                );
              } else {
                showError("Ce titre n'est pas disponible");
              }
            });
          }
        }

        storeAdjacentMusicInQueue();
        resetPlayState();
      } else {
        showError("Ce titre n'est pas disponible");
        resetPlayState();
        return;
      }
    } else if (newVal && parentId === playerStore.queueParent) {
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
    <img
      :src="playerStore.isPlay && parentId === playerStore.queueParent ? pauseLight : playLight"
      :alt="playerStore.isPlay && parentId === playerStore.queueParent ? 'Pause' : 'Play'"
      class="w-20 h-20 box-content"
    />
  </div>
</template>
