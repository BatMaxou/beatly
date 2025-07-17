<script lang="ts" setup>
import logo from "@/assets/beatly-logo-white.png";
import NavItem from "../navigation/NavItem.vue";
import { useRouter } from "vue-router";
import heart from "@/assets/icons/add-to-fav-inactive-light.svg";
import library from "@/assets/icons/library-light.svg";
import user from "@/assets/icons/user-light.svg";
import SearchBar from "../search/SearchBar.vue";
import { usePlayerStore } from "@/stores/player";

const playerStore = usePlayerStore();
const router = useRouter();

const goToHome = () => {
  router.push("/");
};
</script>

<template>
  <div
    :class="
      playerStore.isPlayerActive
        ? 'flex flex-col justify-between playingHeight bg-black/50 transition-all duration-300 ease'
        : 'flex flex-col justify-between h-screen bg-black/50 transition-all duration-300 ease'
    "
  >
    <div class="p-4">
      <img
        :src="logo"
        alt="Logo Beatly"
        class="h-24 mb-4 mx-auto mt-4 object-contain cursor-pointer"
        @click="goToHome"
      />
    </div>
    <!-- Barre de recherche -->
    <div>
      <SearchBar />
    </div>

    <!-- Menu -->
    <div class="flex-1 overflow-y-auto">
      <NavItem label="Coups de cœur" :icon="heart" @click="router.push('/playlist/favoris')" />
      <NavItem label="Bibliothèque" :icon="library" @click="router.push('/bibliotheque')" />
    </div>
    <div>
      <NavItem label="Mon compte" :icon="user" />
    </div>
  </div>
</template>

<style scoped>
.playingHeight {
  height: calc(100vh - 80px);
}
</style>
