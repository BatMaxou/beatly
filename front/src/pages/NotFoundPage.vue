<script setup lang="ts">
import { useRouter } from "vue-router";
import { useHead } from "@unhead/vue";

import PublicLayout from "@/components/layout/PublicLayout.vue";
import LandingButton from "@/components/buttons/LandingButton.vue";

useHead({
  title: "Beatly | Page introuvable",
});

const router = useRouter();

function goBack() {
  router.back();
}
</script>

<template>
  <PublicLayout title="Oups ! Cette page n'existe pas">
    <div class="flex flex-col gap-8 items-center justify-center mb-28">
      <p class="text-center text-xl max-w-[375px]">Revenir à la page précédente</p>
      <LandingButton label="Revenir en arrière" @click="goBack" />
    </div>

    <p class="text-white text-xl text-center max-w-[600px]">
      La page que vous recherchez semble avoir disparu dans le beat. Retournez à l'accueil pour
      continuer à explorer votre musique préférée ou utilisez le bouton retour pour revenir sur vos
      pas.
    </p>
  </PublicLayout>
</template>
