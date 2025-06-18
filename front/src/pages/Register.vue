<script setup>
import { ref } from "vue";
import imageVinyl from "@/assets/images/vinyl-black-and-white-landing.jpg";
import logo from "@/assets/beatly-logo-white.png";

// const router = useRouter()
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const acceptCGV = ref(false);

function handleSubmitRegisterForm() {
  // logique d'inscription puis redirect vers la home
}

function goToLogin() {
  window.location.href = "/login";
}
</script>

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
    <div class="relative container z-20 flex flex-col items-center mt-12 shadow-lg w-full">
      <img :src="logo" alt="Logo Beatly" class="h-24 mb-4" />

      <h2 class="text-2xl uppercase text-center mt-12">Créer un compte</h2>
      <p class="mb-20 cursor-pointer">
        Vous avez déjà un compte ? <span @click="goToLogin">Connectez-vous</span>
      </p>

      <form
        @submit.prevent="handleSubmitRegisterForm"
        class="w-full flex flex-col gap-8 max-w-[400px]"
      >
        <div class="flex flex-col gap-2 justify-start">
          <label class="block text-gray-700 text-white">Adresse courriel</label>
          <input
            type="email"
            v-model="email"
            placeholder="Adresse email"
            class="px-4 py-2 max-h-12 h-12 rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]"
            required
          />
        </div>
        <div class="flex flex-col gap-2 justify-start">
          <label class="block text-gray-700 text-white">Mot de passe</label>
          <input
            type="password"
            v-model="password"
            placeholder="Mot de passe"
            class="px-4 py-2 max-h-12 h-12 rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]"
            required
          />
        </div>
        <div class="flex flex-col gap-2 justify-start">
          <label class="block text-gray-700 text-white">Confirmez votre mot de passe</label>
          <input
            type="password"
            v-model="confirmPassword"
            placeholder="Confirmez mot de passe"
            class="px-4 py-2 max-h-12 h-12 rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]"
            required
          />
        </div>
        <div class="flex flex-row gap-2 justify-start items-start">
          <input
            id="cgv"
            type="checkbox"
            v-model="acceptCGV"
            class="accent-[#5523bf] w-5 h-5"
            required
          />
          <label for="cgv" class="text-sm select-none cursor-pointer"> J'accepte les CGV </label>
        </div>
        <p class="text-center mt-4 text-white font-light">
          Nous pouvons utiliser votre e-mail et vos appareils pour vous envoyer des actualités et
          des conseils sur les produits et services Beatly.
        </p>
        <div class="flex justify-center">
          <button
            class="uppercase bg-[#B00D70] rounded-3xl w-fit px-10 py-2 text-sm font-bold text-white hover:bg-[#940a5e] transition"
            @click="handleSubmitRegisterForm"
          >
            S'inscrire
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
