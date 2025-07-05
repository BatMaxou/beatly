<script setup lang="ts">
import { useRouter } from "vue-router";
import PublicLayout from "@/components/PublicLayout.vue";

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
    <div class="flex flex-row gap-32 mb-28">
      <div class="flex flex-col gap-8 items-center justify-between max-w-[375px] text-xl">
        <p class="text-center">Profiter gratuitement <br />de toutes votre musique</p>
        <button
          class="uppercase bg-[#B00D70] rounded-3xl px-10 py-2 text-sm font-bold text-white hover:bg-[#940a5e] transition"
          @click="goToRegister"
        >
          S'inscrire
        </button>
      </div>
      <div class="flex flex-col gap-8 items-center justify-between max-w-[375px] text-xl">
        <p class="text-center">Vous avez déjà un compte ?</p>
        <button
          class="uppercase bg-[#B00D70] rounded-3xl px-10 py-2 text-sm font-bold text-white hover:bg-[#940a5e] transition"
          @click="goToLogin"
        >
          Se connecter
        </button>
      </div>
    </div>

    <p class="text-white text-xl text-center max-w-[600px]">
      Explorez tout un monde de musique sans publicité, hors connexion et même avec l’écran
      verrouillé. Disponible sur mobile et ordinateur. Beatly propose des albums officiels, des
      playlists, des singles et plus encore.
    </p>
  </PublicLayout>
</template>
