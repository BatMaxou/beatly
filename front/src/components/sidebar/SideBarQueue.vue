<script lang="ts" setup>
import { ref, onMounted, onUnmounted, watch } from "vue";
import { usePlayerStore } from "@/stores/player";
import CrossIcon from "@/assets/icons/cross-light.svg";
import QueueList from "../lists/QueueList.vue";

const playerStore = usePlayerStore();
const activeTab = ref<"queue" | "lyrics">("queue");

const setActiveTab = (tab: "queue" | "lyrics") => {
  activeTab.value = tab;
};

const handleBodyScroll = () => {
  if (playerStore.showQueue) {
    document.body.style.overflow = "hidden";
  } else {
    document.body.style.overflow = "";
  }
};

watch(
  () => playerStore.showQueue,
  (newValue) => {
    if (newValue) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
  },
  { immediate: true },
);

onMounted(() => {
  handleBodyScroll();
});

onUnmounted(() => {
  document.body.style.overflow = "";
});
</script>

<template>
  <div
    class="p-6 h-full flex flex-col bg-gradient-to-b from-[#1a0b2e] to-[#16213e] overflow-hidden"
    @click.stop
    @wheel.stop
    @scroll.stop
  >
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold">Lecteur</h2>
      <button
        @click="playerStore.setShowQueue(false)"
        class="transition-colors p-2 hover:bg-[#440a50] rounded-full"
      >
        <img :src="CrossIcon" alt="Fermer la sidebar" class="w-6 h-6" />
      </button>
    </div>

    <!-- Menu onglets -->
    <div class="flex border-b border-white/20 mb-6">
      <button
        @click="setActiveTab('queue')"
        :class="[
          'px-4 py-2 font-medium transition-colors relative',
          activeTab === 'queue'
            ? 'text-white border-b-2 border-white'
            : 'text-white/70 hover:text-white',
        ]"
      >
        File d'attente
      </button>
      <button
        @click="setActiveTab('lyrics')"
        :class="[
          'px-4 py-2 font-medium transition-colors relative ml-4',
          activeTab === 'lyrics'
            ? 'text-white border-b-2 border-white'
            : 'text-white/70 hover:text-white',
        ]"
      >
        Paroles
      </button>
    </div>

    <!-- Onglets -->
    <div class="flex-1 overflow-y-auto h-full scrollbar-hide" @click.stop @wheel.stop>
      <!-- File d'attente -->
      <div v-if="activeTab === 'queue'">
        <ul class="space-y-2">
          <QueueList
            origin="queue"
            :parentId="playerStore.queueParent ? playerStore.queueParent : undefined"
          />
        </ul>
      </div>

      <!-- Paroles -->
      <div v-if="activeTab === 'lyrics'" class="text-center py-12">
        <div class="text-white/70">
          <div class="text-lg mb-2">🎵</div>
          <p class="text-base">Nous n'avons pas ceci sous la main...</p>
          <p class="text-sm mt-2 text-white/50">Peut être bientôt !</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.overflow-hidden {
  pointer-events: auto;
}

.overflow-y-auto {
  pointer-events: auto;
}
</style>
