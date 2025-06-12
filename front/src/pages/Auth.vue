<template>
  <div class="relative min-h-screen flex justify-center">
    <div
      class="absolute inset-0 bg-cover bg-center z-0"
      :style="{ backgroundImage: `url(${imageVinyl})` }"
    ></div>
    <div
      class="absolute inset-0 z-10"
      style="background: linear-gradient(to bottom, #5523bf, #b00d72); opacity: 0.7"
    ></div>
    <div class="relative z-20 flex flex-col items-center mt-12 shadow-lg w-full">
      <img :src="logo" alt="Logo Beatly" class="h-24 mb-4" />

      <h2 class="text-lg uppercase text-center mt-12 mb-28">
        Écoutez votre musique favorite en un seul clic
      </h2>

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
        verrouillé. Disponible sur mobile et ordinateur. Ziq&mu propose des albums officiels, des
        playlists, des singles et plus encore.
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import imageVinyl from "@/assets/images/vinyl-black-and-white-landing.jpg";
import logo from "@/assets/beatly-logo-white.png";
function goToLogin() {
  //Redirection vers la page de connexion
}

function goToRegister() {
  //Redirection vers la page d'inscription
}
</script>
