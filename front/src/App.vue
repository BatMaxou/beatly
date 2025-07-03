<script setup lang="ts">
import { SearchProvider } from "./providers";
import Layout from "./components/layout/Layout.vue";
</script>

<template>
  <SearchProvider>
    <Layout class="text-white">
      <router-view />
    </Layout>
  </SearchProvider>
</template>
