<script setup lang="ts">
import { ref } from "vue";
import { usePlayerStore } from "@/stores/player";
import SideBarQueue from "../sidebar/SideBarQueue.vue";
import SearchModal from "../search/SearchModal.vue";
import loadingIcon from "@/assets/icons/loading-light.svg";

const props = defineProps<{
  loading?: boolean;
}>();

const playerStore = usePlayerStore();

const searchQuery = ref("");
const isSearchModalVisible = ref(false);

const handleSearch = (query: string) => {
  searchQuery.value = query;
  isSearchModalVisible.value = true;
};

const closeSearch = () => {
  searchQuery.value = "";
  isSearchModalVisible.value = false;
};

defineExpose({
  handleSearch,
  closeSearch,
});
</script>

<template>
  <div class="content-layout relative h-full">
    <div class="page-content h-full">
      <div v-if="props.loading" class="absolute inset-0 flex flex-col items-center justify-center">
        <img :src="loadingIcon" alt="Chargement" class="h-12 w-12 animate-spin mb-4" />
      </div>
      <div
        :class="
          props.loading
            ? 'opacity-0 transition ease duration-700 pb-8'
            : 'opacity-1 transition ease duration-700 pb-8'
        "
      >
        <slot v-if="!props.loading" />
      </div>
    </div>

    <!-- Overlay recherche -->
    <Transition name="fade">
      <div
        v-if="isSearchModalVisible"
        class="search-overlay fixed z-30 bg-black/95 pointer-events-auto"
        @click="closeSearch"
        @wheel.prevent
        @scroll.prevent
      />
    </Transition>

    <!-- Recherche -->
    <Transition name="slide-up">
      <SearchModal
        v-if="isSearchModalVisible"
        :class="
          'search-modal fixed left-[250px] z-40 shadow-2xl rounded-t-2xl pointer-events-auto' +
          (playerStore.isPlayerActive ? ' resizeSearch' : '')
        "
        :query="searchQuery"
        :isVisible="isSearchModalVisible"
        @close="closeSearch"
      />
    </Transition>

    <!-- Overlay file d'attente-->
    <Transition name="fade">
      <div
        v-if="playerStore.showQueue"
        class="queue-overlay fixed z-30 bg-black/50"
        @click="playerStore.setShowQueue(false)"
        @wheel.prevent
        @scroll.prevent
      />
    </Transition>

    <!-- File d'attente -->
    <Transition name="slide-right">
      <SideBarQueue
        v-if="playerStore.showQueue"
        class="queue-modal fixed top-0 right-0 z-40 shadow-2xl"
        style="background: linear-gradient(to top, #5523bf, #b00d72)"
      />
    </Transition>
  </div>
</template>

<style scoped>
.search-overlay {
  top: 0;
  left: 250px;
  right: 0;
  bottom: 0;
}

.search-modal {
  top: 4rem;
  left: calc(250px + 1rem);
  right: 1rem;
  bottom: 1rem;
  height: calc(100vh - 4rem);
}

.search-modal.resizeSearch {
  height: calc(100vh - 9rem);
}

.queue-overlay {
  top: 0;
  left: -250px;
  right: 0;
  bottom: 0;
}

.queue-modal {
  width: 500px;
  height: 100%;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s ease-out;
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}

.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.3s ease-out;
}

.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
}

@media (max-width: 768px) {
  .queue-modal,
  .search-modal {
    width: 100vw;
  }
  .search-modal {
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 0;
  }
}
</style>
