<script setup lang="ts">
import { defineProps, computed } from "vue";
import { usePlayerStore } from "@/stores/player";
import QueueListItem from "./QueueListItem.vue";

const props = defineProps({
  parentId: {
    type: String,
    default: null,
  },
  customStyles: {
    type: Object,
    default: () => ({}),
  },
  origin: {
    type: String,
    required: true,
  },
  theme: {
    type: String,
    default: "dark",
  },
});

const playerStore = usePlayerStore();

const queueList = computed(() => {
  if (playerStore.isRandomQueue) {
    return playerStore.randomQueue;
  }
  return playerStore.queue;
});
</script>

<template>
  <div
    v-if="queueList && queueList.queueItems.length > 0"
    class="flex flex-col"
    :style="props.customStyles.musicList"
  >
    <QueueListItem
      v-for="music in queueList.queueItems"
      :key="music.position"
      :music="music.music"
      :position="music.position"
      :index="music.position"
      :parentId="parentId"
      :origin="origin"
      :customStyles="props.customStyles"
      :theme="props.theme"
    />
  </div>

  <div
    v-if="!queueList || queueList.queueItems.length === 0"
    class="flex flex-col items-center justify-center py-8 text-gray-500 dark:text-gray-400"
    :style="props.customStyles.emptyMusicList"
  >
    <p>Aucune musique en attente</p>
  </div>
</template>
