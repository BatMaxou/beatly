<script setup lang="ts">
import { useRouter } from "vue-router";
import PublicLayout from "@/components/layout/PublicLayout.vue";
import LandingButton from "@/components/buttons/LandingButton.vue";

const router = useRouter();

function goToLogin() {
  router.push("/login");
}

function goToRegister() {
  router.push("/register");
}
</script>

<template>
  <PublicLayout title="Écoutez votre musique favorite en un seul clic">
    <div class="flex flex-row flex-wrap gap-x-32 gap-y-16 mb-28 justify-center items-center">
      <div class="flex flex-col gap-8 items-center justify-between max-w-[375px] text-xl">
        <p class="text-center">Profiter gratuitement <br />de toutes votre musique</p>
        <LandingButton label="S'inscrire" @click="goToRegister"/>
      </div>
      <div class="flex flex-col gap-8 items-center justify-between max-w-[375px] text-xl">
        <p class="text-center">Vous avez déjà un compte ?</p>
        <LandingButton label="Se connecter" @click="goToLogin"/>
      </div>
    </div>

    <p class="text-white text-xl text-center max-w-[600px]">
      Explorez tout un monde de musique sans publicité, hors connexion et même avec l’écran
      verrouillé. Disponible sur mobile et ordinateur. Beatly propose des albums officiels, des
      playlists, des singles et plus encore.
    </p>
  </PublicLayout>
</template>
