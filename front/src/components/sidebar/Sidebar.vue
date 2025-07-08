<script lang="ts" setup>
import logo from "@/assets/beatly-logo-white.png";
import NavItem from "../navigation/NavItem.vue";

import heart from "@/assets/icons/add-to-fav-inactive-light.svg";
import library from "@/assets/icons/library-light.svg";
import user from "@/assets/icons/user-light.svg";
import SearchBar from "../search/SearchBar.vue";
</script>

<template>
  <div class="flex flex-col justify-between h-screen bg-black/50">
    <div class="p-4">
      <img :src="logo" alt="Logo Beatly" class="h-24 mb-4 mx-auto mt-4 object-contain" />
    </div>
    <!-- Barre de recherche -->
    <div>
      <SearchBar/>
    </div>

    <!-- Menu -->
    <div class="flex-1 overflow-y-auto">
      <NavItem
      label="Coups de cœur"
      :icon="heart"
      />
      <NavItem
      label="Bibliothèque"
      :icon="library"
      />
    </div>
    <div>
      <NavItem
      label="Mon compte"
      :icon="user"
      />
    </div>
  </div>
</template>