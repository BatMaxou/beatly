<script setup lang="ts">
import imageVinyl from "@/assets/images/vinyl-black-and-white-landing.jpg";
import logo from "@/assets/beatly-logo-white.png";
import { useRouter } from "vue-router";
import { useApiClient } from "@/stores/api-client";

const router = useRouter();
const { apiClient } = useApiClient();

function handleSubmitRegisterForm(data) {
  const registerRequest = apiClient.user
    .register(data)
    .then((response) => console.log("Register response:", response));

  console.log("Données du formulaire :", data);
  console.log("Request du formulaire :", registerRequest);
}

function goToLogin() {
  router.push("/login");
}

const handleIconClick = (node, e) => {
  node.props.suffixIcon = node.props.suffixIcon === "eye" ? "eyeClosed" : "eye";
  node.props.type = node.props.type === "password" ? "text" : "password";
};
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
    <div class="relative container z-20 flex flex-col items-center my-12 w-full">
      <img :src="logo" alt="Logo Beatly" class="h-24 mb-4" />

      <h2 class="text-2xl uppercase text-center mt-12">Créer un compte</h2>
      <p class="mb-20 cursor-pointer">
        Vous avez déjà un compte ? <span @click="goToLogin">Connectez-vous</span>
      </p>

      <FormKit
        type="form"
        id="registration-example"
        :classes="{
          form: 'w-full flex flex-col gap-4 max-w-[400px]',
        }"
        @submit="handleSubmitRegisterForm"
        :actions="false"
        :messages-class="'hidden'"
      >
        <div class="flex flex-col gap-2 justify-start">
          <FormKit
            type="text"
            name="name"
            label="Nom d'utilisateur"
            :classes="{
              input:
                'px-4 py-2 max-h-12 h-12 w-full text-black rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]',
            }"
            validation="required"
            validation-visibility="submit"
            placeholder="Nom d'utilisateur"
          />
        </div>
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
        <div>
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
        </div>
        <div class="relative">
          <FormKit
            type="password"
            name="password_confirm"
            label="Confirmation du mot de passe"
            :classes="{
              inner: 'relative flex items-center',
              input:
                'px-4 py-2 max-h-12 h-12 w-full text-black rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]',
              suffixIcon: 'absolute right-4 text-[#5523bf] hover:text-blue-500 cursor-pointer w-6',
            }"
            validation="required|confirm:password"
            validation-visibility="submit"
            validation-label="Confirmation du mot de passe"
            suffix-icon="eyeClosed"
            @suffix-icon-click="handleIconClick"
            suffix-icon-class="hover:text-blue-500"
          />
        </div>
        <div class="flex flex-row gap-2 justify-start items-start">
          <FormKit
            type="checkbox"
            name="cgv"
            label="J'accepte les CGV"
            :value="false"
            validation="required"
            :classes="{
              outer: 'flex flex-row gap-2 justify-start items-start',
              wrapper: 'flex items-center gap-2',
              input: 'accent-[#5523bf] w-5 h-5 mt-1',
              label: 'text-sm select-none cursor-pointer',
            }"
            validation-visibility="submit"
          />
        </div>
        <p class="text-center mt-4 text-white font-light">
          Nous pouvons utiliser votre e-mail et vos appareils pour vous envoyer des actualités et
          des conseils sur les produits et services Beatly.
        </p>
        <div class="flex justify-center">
          <FormKit
            type="submit"
            label="S'inscrire"
            :classes="{
              input:
                'uppercase bg-[#B00D70] rounded-3xl w-fit px-10 py-2 text-sm font-bold text-white hover:bg-[#940a5e] transition',
            }"
          />
        </div>
      </FormKit>
    </div>
  </div>
</template>
