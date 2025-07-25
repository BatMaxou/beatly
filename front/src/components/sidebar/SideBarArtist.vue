<script lang="ts" setup>
import logo from "@/assets/beatly-logo-white.png";
import miniLogo from "@/assets/beatly-logo-white-mini.png";
import NavItem from "../navigation/NavItem.vue";
import { useRouter } from "vue-router";
import music from "@/assets/icons/music-light.svg";
import library from "@/assets/icons/library-light.svg";
import user from "@/assets/icons/user-light.svg";
import disc from "@/assets/icons/disc-light.svg";
import { usePlayerStore } from "@/stores/player";
import { useUserStore } from "@/stores/user";
import { Role } from "@/utils/types";

const playerStore = usePlayerStore();
const userStore = useUserStore();
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
        class="h-24 mb-4 mx-auto mt-4 object-contain cursor-pointer hidden sm:block"
        @click="goToHome"
      />
      <img
        :src="miniLogo"
        alt="Logo Beatly"
        class="h-auto mb-1 mx-auto mt-1 object-contain cursor-pointer block sm:hidden"
        @click="goToHome"
      />
    </div>
    <div class="flex-1 overflow-y-auto">
      <NavItem label="Albums" :icon="library" @click="router.push('/artist/albums')" />
      <NavItem label="Musiques" :icon="music" @click="router.push('/artist/musiques')" />
    </div>
    <div>
      <NavItem
        v-if="userStore.user?.roles.includes(Role.ARTIST)"
        label="Discographie"
        :icon="disc"
        @click="router.push('/artist')"
      />
      <NavItem label="Mon compte" :icon="user" @click="router.push('/mon-compte')" />
    </div>
  </div>
</template>

<style scoped>
.playingHeight {
  height: calc(100vh - 80px);
}
</style>
