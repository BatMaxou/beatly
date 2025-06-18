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

      <h2 class="text-2xl uppercase text-center mt-12 mb-20">Se connecter</h2>

      <form class="w-full flex flex-col gap-8 max-w-[400px]">
        <div class="flex flex-col gap-2 justify-start">
          <label class="block text-gray-700 text-white">Votre adresse courriel</label>
          <input
            type="email"
            v-model="email"
            placeholder="Adresse email"
            class="px-4 py-2 max-h-12 h-12 rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]"
            required
          />
        </div>
        <div class="flex flex-col gap-2 justify-start">
          <label class="block text-gray-700 text-white">Votre mot de passe</label>
          <input
            type="password"
            v-model="password"
            placeholder="Mot de passe"
            class="px-4 py-2 max-h-12 h-12 rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]"
            required
          />
          <p
            @click="goToForgotPassword"
            class="text-end text-white font-light underline cursor-pointer"
          >
            Mot de passe oublié ?
          </p>
        </div>
      </form>

      <div class="flex justify-center mt-8 mb-16">
        <button
          class="uppercase bg-[#B00D70] rounded-3xl w-fit px-10 py-2 text-sm font-bold text-white hover:bg-[#940a5e] transition"
          @click="handleSubmitLoginForm"
        >
          Se connecter
        </button>
      </div>

      <p class="text-center mb-4 text-white font-light">Vous n'avez pas de compte ?</p>
      <div class="flex justify-center mb-16">
        <button
          class="uppercase bg-[#B00D70] rounded-3xl w-fit px-10 py-2 text-sm font-bold text-white hover:bg-[#940a5e] transition"
          @click="goToRegister"
        >
          S'inscrire
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import imageVinyl from "@/assets/images/vinyl-black-and-white-landing.jpg";
import logo from "@/assets/beatly-logo-white.png";

// const router = useRouter()
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const acceptCGV = ref(false);

function handleSubmitLoginForm() {
  // Ici, tu peux ajouter la logique d'inscription (API, validation, etc.)
  // Redirection ou autre logique après inscription
}

function goToRegister() {
  window.location.href = "/register";
}
</script>
