<script setup lang="ts">
import Sidebar from "@/components/sidebar/Sidebar.vue";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "@/composables/useToast";

const authStore = useAuthStore();
const { showSuccess } = useToast();

if(authStore.loginSuccess) {
  showSuccess("Vous êtes connecté !");
  authStore.setLoginSuccess(false);
}
</script>

<template>
  <div class="relative min-h-screen flex">
    <div
      class="absolute inset-0 z-10"
      style="background: linear-gradient(to top, #5523BF, #B00D72); opacity: 1"
    ></div>

    <!-- Sidebar -->
     <Sidebar class="max-w-[250px] w-full z-20"/>

    <!-- Emplacement du contenu de la page -->
     <div class="z-20 w-full ps-8 pt-20">
      <slot></slot>
    </div>
  </div>
</template>