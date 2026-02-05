<script lang="ts" setup>
import { ref } from "vue";
import logo from "@/assets/beatly-logo-white.png";
import miniLogo from "@/assets/beatly-logo-white-mini.png";
import NavItem from "../navigation/NavItem.vue";
import { useRouter } from "vue-router";
import heart from "@/assets/icons/add-to-fav-inactive-light.svg";
import library from "@/assets/icons/library-light.svg";
import user from "@/assets/icons/user-light.svg";
import shield from "@/assets/icons/shield-light.svg";
import disc from "@/assets/icons/disc-light.svg";
import SearchBar from "../search/SearchBar.vue";
import { usePlayerStore } from "@/stores/player";
import { useUserStore } from "@/stores/user";
import { Role } from "@/utils/types";

const playerStore = usePlayerStore();
const userStore = useUserStore();
const router = useRouter();
const searchBarRef = ref<InstanceType<typeof SearchBar>>();

const emit = defineEmits(["search", "close-search"]);

const goToHome = () => {
  router.push("/");
};

const handleSearch = (query: string) => {
  emit("search", query);
};

const closeSearch = () => {
  emit("close-search");
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
    <!-- Barre de recherche -->
    <div>
      <SearchBar ref="searchBarRef" @search="handleSearch" @close-search="closeSearch" />
    </div>

    <!-- Menu -->
    <div class="flex-1 overflow-y-auto">
      <NavItem label="Coups de cœur" :icon="heart" @click="router.push('/playlist/favoris')" />
      <NavItem label="Bibliothèque" :icon="library" @click="router.push('/bibliotheque')" />
    </div>
    <div>
      <NavItem
        v-if="userStore.user?.roles.includes(Role.PLATFORM)"
        label="Administration"
        :icon="shield"
        @click="router.push('/admin')"
      />
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
