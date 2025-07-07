<script setup lang="ts">
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useLogout } from "@/composables/useLogout";

const { logout } = useLogout();

const handleLogout = async () => {
  await logout();
};
</script>

<template>
  <InAppLayout>
    <p>Home</p>
    <button @click="handleLogout" class="logout-btn">Déconnexion</button>
  </InAppLayout>
</template>
