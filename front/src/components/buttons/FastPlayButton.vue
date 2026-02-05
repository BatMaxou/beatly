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
const { loadQueue, loadRandomQueue, storeAdjacentMusicInQueue, loadQueueFile } =
  usePlayerPreparation();
const emit = defineEmits(["update:isClickedToPlay"]);

const { origin, parentId, isClickedToPlay, albumPosition } = defineProps({
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
  albumPosition: {
    type: Number,
    default: null,
  },
});

const resetPlayState = () => {
  emit("update:isClickedToPlay", false);
};

watch(
  () => isClickedToPlay,
  async (newVal) => {
    if (newVal && parentId !== playerStore.queueParent) {
      if (parentId) {
        await loadQueue(origin, parentId, albumPosition || 1, null);
        if (playerStore.queue) {
          await loadQueueFile();
          let firstMusic;
          if (playerStore.isRandomQueue) {
            await loadRandomQueue(albumPosition || 0);
            firstMusic =
              (playerStore.randomQueue &&
                Object.entries(playerStore.randomQueue?.queueItems)[0][1]) ||
              null;
          } else {
            if (albumPosition) {
              firstMusic = Object.values(playerStore.queue.queueItems).find(
                (item) => item.music.albumPosition === albumPosition,
              );
            } else {
              firstMusic = Object.entries(playerStore.queue.queueItems)[0][1];
            }
          }

          if (firstMusic) {
            const queueFile = playerStore.queueFile?.find(
              (item) => item.musicId === firstMusic.music.id,
            );
            if (queueFile) {
              await playerStore.setListen(
                firstMusic.music,
                queueFile.file,
                firstMusic.position,
                parentId,
              );
              await storeAdjacentMusicInQueue();
            } else {
              apiClient.music.getFile(firstMusic.music.id).then(async (response) => {
                if (response) {
                  await playerStore.setListen(
                    firstMusic.music,
                    await streamToAudioUrl(response),
                    firstMusic.position,
                    parentId,
                  );
                  await storeAdjacentMusicInQueue();
                } else {
                  showError("Ce titre n'est pas disponible");
                }
              });
            }
          }
        }

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
