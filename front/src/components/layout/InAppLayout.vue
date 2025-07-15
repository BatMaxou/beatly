<script setup lang="ts">
import SideBar from "@/components/sidebar/SideBar.vue";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "@/composables/useToast";
import loadingIcon from "@/assets/icons/loading-light.svg";
import PlayerSection from "@/components/player/PlayerSection.vue";
import { usePlayerStore } from "@/stores/player";
import SideBarQueue from "../sidebar/SideBarQueue.vue";

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

    <div :class="playerStore.isPlayerActive ? 'pb-[80px] z-20 flex' : 'min-h-screen z-20 flex'">
      <SideBar class="fixed left-0 top-0 min-w-[250px] w-full max-w-[250px] z-20" />

      <!-- Emplacement du contenu de la page -->
      <div :class="'ms-[250px] z-20 w-full overflow-x-hidden relative ' + padding">
        <div v-if="loading" class="absolute inset-0 flex flex-col items-center justify-center">
          <img :src="loadingIcon" alt="Chargement" class="h-12 w-12 animate-spin mb-4" />
        </div>
        <div
          :class="
            loading
              ? 'opacity-0 transition ease duration-700'
              : 'opacity-1 transition ease duration-700'
          "
        >
          <slot v-if="!loading"></slot>
        </div>
      </div>
    </div>
    <!-- Player en bas de page -->
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-[#2E0B40] flex justify-center items-center">
      <PlayerSection />
    </div>

    <!-- Sidebar File d'attente -->
    <Transition name="slide-right">
      <div
        v-if="playerStore.showQueue"
        class="fixed inset-0 bg-black/50 z-30"
        :class="playerStore.isPlayerActive ? 'pb-[80px]' : 'min-h-screen'"
        @click="playerStore.setShowQueue(false)"
      ></div>
    </Transition>

    <Transition name="slide-right">
      <SideBarQueue
        v-if="playerStore.showQueue"
        :class="playerStore.isPlayerActive ? 'pb-[80px]' : 'min-h-screen'"
        class="fixed top-0 right-0 min-w-[500px] h-full z-40 shadow-2xl"
        style="background: linear-gradient(to top, #5523bf, #b00d72); opacity: 1"
      />
    </Transition>
  </div>
</template>

<style scoped>
.playingMinHeight {
  max-height: calc(100vh - 80px);
}

/* Animation pour la sidebar coulissante */
.slide-right-enter-active,
.slide-right-leave-active {
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
</style>
