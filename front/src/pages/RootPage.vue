<script setup lang="ts">
import { ref, onMounted } from "vue";
import { getCookie } from "@/utils/cookies";
import Home from "./Home.vue";
import Landing from "./Landing.vue";

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
