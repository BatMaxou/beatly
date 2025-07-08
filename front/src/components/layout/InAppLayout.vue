<script setup lang="ts">
import SideBar from "@/components/sidebar/SideBar.vue";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "@/composables/useToast";
import loadingIcon from "@/assets/icons/loading-light.svg";
import PlayerSection from "@/components/player/PlayerSection.vue";
import { usePlayerStore } from '@/stores/player'


// Props
const { loading } = defineProps({
  loading: {
    type: Boolean,
    default: false,
  }
});

const playerStore = usePlayerStore()
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
      class="absolute inset-0 z-10"
      style="background: linear-gradient(to top, #5523BF, #B00D72); opacity: 1"
    ></div>

    <div :class="playerStore.isPlayerActive ? 'playingMinHeight z-20 flex' : 'min-h-screen z-20 flex'">
      <!-- Sidebar -->
      <SideBar class="max-w-[250px] w-full z-20"/>
    
      <!-- Emplacement du contenu de la page -->
      <div class="z-20 w-full ps-8 pt-10 relative">
          <div v-if="loading" class="absolute inset-0 flex flex-col items-center justify-center">
            <img :src="loadingIcon" alt="Chargement" class="h-12 w-12 animate-spin mb-4" />
          </div>
          <div :class="loading ? 'opacity-0 transition ease duration-700' : 'opacity-1 transition ease duration-700'">
            <slot v-if="!loading"></slot>
          </div>
        </div>

      <!-- Player en bas de page -->
    </div>
    <div class="fixed bottom-0 left-0 right-0 z-20 bg-[#2E0B40] flex justify-center items-center">
      <PlayerSection/>
    </div>
  </div>
</template>

<style scoped>
.playingMinHeight {
  max-height: calc(100vh - 80px);
}
</style>