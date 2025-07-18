<script setup lang="ts">
import { ref } from "vue";
import SideBar from "@/components/sidebar/SideBar.vue";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "@/composables/useToast";
import PlayerSection from "@/components/player/PlayerSection.vue";
import { usePlayerStore } from "@/stores/player";
import PlaylistSelectorModal from "../modals/PlaylistSelectorModal.vue";
import ContentLayout from "./ContentLayout.vue";
import { useModalsStore } from "@/stores/modals";

// Props
const { loading, padding } = defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
  padding: {
    type: String,
    default: "pt-10 ps-10",
  },
});

const playerStore = usePlayerStore();
const authStore = useAuthStore();
const { showSuccess } = useToast();
const modalsStore = useModalsStore();

const contentLayoutRef = ref<InstanceType<typeof ContentLayout>>();
const sideBarRef = ref<InstanceType<typeof SideBar>>();

const handleSearch = (query: string) => {
  contentLayoutRef.value?.handleSearch(query);
};

const closeSearch = () => {
  contentLayoutRef.value?.closeSearch();
};

if (authStore.loginSuccess) {
  showSuccess("Vous êtes connecté !");
  authStore.setLoginSuccess(false);
}
</script>

<template>
  <div class="relative min-h-screen text-white">
    <div
      class="inset-0 z-10 fixed"
      style="background: linear-gradient(to top, #5523bf, #b00d72); opacity: 1"
    ></div>

    <div
      :class="
        playerStore.isPlayerActive
          ? 'pb-[80px] min-h-screen z-20 flex scrollbar-hide'
          : 'min-h-screen z-20 flex scrollbar-hide'
      "
    >
      <SideBar
        ref="sideBarRef"
        class="fixed left-0 top-0 min-w-[250px] w-full max-w-[250px] z-20"
        @search="handleSearch"
        @close-search="closeSearch"
      />

      <!-- Contenu et modals -->
      <ContentLayout
        ref="contentLayoutRef"
        :class="
          'ms-[250px] z-20 w-full overflow-x-hidden overflow-y-scroll scrollbar-hide relative ' +
          padding
        "
        :loading="loading"
      >
        <slot />
      </ContentLayout>
    </div>

    <!-- Player en bas de page -->
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-[#2E0B40] flex justify-center items-center">
      <PlayerSection />
    </div>

    <!-- Modale de sélection de playlist -->
    <PlaylistSelectorModal
      :isVisible="modalsStore.isPlaylistSelectorVisible"
      :element="modalsStore.playlistSelectorElement as any"
      @close="modalsStore.closePlaylistSelector"
    />
  </div>
</template>

<style scoped>
.playingMinHeight {
  max-height: calc(100vh - 80px);
}

/* Animation pour la sidebar coulissante */
.slide-right-enter-active,
.slide-right-leave-active,
.slide-left-enter-active,
.slide-left-leave-active,
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s ease-in-out;
}

.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
}

.slide-right-enter-to,
.slide-right-leave-from {
  transform: translateX(0);
}

.slide-left-enter-from,
.slide-left-leave-to {
  transform: translateX(-100%);
}

.slide-left-enter-to,
.slide-left-leave-from {
  transform: translateX(0);
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}

.slide-up-enter-to,
.slide-up-leave-from {
  transform: translateY(0);
}
</style>
