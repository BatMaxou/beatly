<script setup>
import { ref, computed, onMounted, provide } from "vue";
import Home from "./pages/Home.vue";
import Layout from "./components/layout/Layout.vue";
import Ui from "./pages/Ui.vue";
import Auth from "./pages/Auth.vue";
import NotFound from "./pages/NotFound.vue";
import { SearchProvider } from "./providers";

const routes = {
  "/": Home, // Condition a placer sur l'état de connexion de l'utilisateur / Affichage Landing ou Home
  "/ui": Ui,
  "/auth": Auth,
};

const currentPath = ref(window.location.pathname);

window.addEventListener("popstate", () => {
  currentPath.value = window.location.pathname;
});

const navigateTo = (path) => {
  window.history.pushState({}, "", path);
  currentPath.value = path;
};

onMounted(() => {
  document.addEventListener("click", (e) => {
    const link = e.target.closest("a");
    if (link && link.href.startsWith(window.location.origin) && !link.dataset.external) {
      e.preventDefault();
      const url = new URL(link.href);
      navigateTo(url.pathname);
    }
  });
});

const currentView = computed(() => {
  console.log("Current path:", currentPath.value);
  return routes[currentPath.value || "/"] || NotFound;
});
</script>

<template>
  <SearchProvider>
    <Layout :current-view="currentView" class="text-white" />
  </SearchProvider>
</template>
