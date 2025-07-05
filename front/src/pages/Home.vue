<script setup lang="ts">
import { useLogout } from "@/composables/useLogout";

const { logout } = useLogout();

const handleLogout = async () => {
  await logout();
};
</script>

<template>
  <div>
    <p>Home</p>
    <button @click="handleLogout" class="logout-btn">Déconnexion</button>
  </div>
</template>
