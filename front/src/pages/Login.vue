<script setup lang="ts">
import { ref } from "vue";
import imageVinyl from "@/assets/images/vinyl-black-and-white-landing.jpg";
import logo from "@/assets/beatly-logo-white.png";
import { useRouter } from "vue-router";
import { useApiClient } from "@/stores/api-client";

const router = useRouter();
const { apiClient } = useApiClient();

function handleSubmitLoginForm(data) {
  const loginRequest = apiClient.login(data.email, data.password).then((response) => {
    if (response.user) {
      localStorage.setItem("token", response.token);
      router.push("/home");
    } else {
      console.error("Erreur durant la connexion");
    }
  });
}

const handleIconClick = (node, e) => {
  node.props.suffixIcon = node.props.suffixIcon === "eye" ? "eyeClosed" : "eye";
  node.props.type = node.props.type === "password" ? "text" : "password";
};

function goToRegister() {
  router.push("/register");
}

function goToForgotPassword() {
  router.push("/forgot-password");
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
    <div class="relative container z-20 flex flex-col items-center mt-12 w-full">
      <img :src="logo" alt="Logo Beatly" class="h-24 mb-4" />

      <h2 class="text-2xl uppercase text-center mt-12 mb-20">Se connecter</h2>

      <FormKit
        type="form"
        :classes="{
          form: 'w-full flex flex-col gap-4 max-w-[400px]',
        }"
        @submit="handleSubmitLoginForm"
        :actions="false"
        :messages-class="'hidden'"
      >
        <div class="flex flex-col gap-2 justify-start">
          <FormKit
            type="email"
            name="email"
            label="Adresse courriel"
            :classes="{
              input:
                'px-4 py-2 max-h-12 h-12 w-full text-black rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]',
            }"
            validation="required|email"
            validation-visibility="submit"
            placeholder="Adresse email"
          />
        </div>
        <div class="flex flex-col gap-2 justify-start">
          <FormKit
            type="password"
            name="password"
            label="Mot de passe"
            :classes="{
              inner: 'relative flex items-center',
              input:
                'px-4 py-2 max-h-12 h-12 w-full text-black rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]',
              suffixIcon: 'absolute right-4 text-[#5523bf] hover:text-blue-500 cursor-pointer w-6',
            }"
            validation="required"
            validation-visibility="submit"
            placeholder="Mot de passe"
            suffix-icon="eyeClosed"
            @suffix-icon-click="handleIconClick"
            suffix-icon-class="hover:text-blue-500"
          />
          <p
            @click="goToForgotPassword"
            class="text-end text-white font-light underline cursor-pointer"
          >
            Mot de passe oublié ?
          </p>
        </div>

        <div class="flex justify-center mt-8 mb-16">
          <FormKit
            type="submit"
            label="Se connecter"
            :classes="{
              input:
                'uppercase bg-[#B00D70] rounded-3xl w-fit px-10 py-2 text-sm font-bold text-white hover:bg-[#940a5e] transition',
            }"
          />
        </div>
      </FormKit>

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
