<script setup lang="ts">
import { ref, onMounted } from "vue";
import { getCookie } from "@/utils/cookies";
import Home from "./HomePage.vue";
import Landing from "./LandingPage.vue";

const isAuthenticated = ref(false);

onMounted(() => {
  isAuthenticated.value = !!getCookie("token");
});

function updateAuthState() {
  isAuthenticated.value = !!getCookie("token");
}
</script>

<template>
  <Home v-if="isAuthenticated" @logout="updateAuthState" />
  <Landing v-else />
</template>
