<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useHead } from "@unhead/vue";

import { useApiClient } from "@/stores/api-client";
import { useAuthStore } from "@/stores/auth";
import { useToast } from "@/composables/useToast";
import PublicLayout from "@/components/layout/PublicLayout.vue";
import LandingButton from "@/components/buttons/LandingButton.vue";

useHead({
  title: "Beatly | Créer un compte",
});

const router = useRouter();
const { apiClient } = useApiClient();
const authStore = useAuthStore();
const { showError } = useToast();
const loading = ref(false);

function handleSubmitRegisterForm(data: { email: string; password: string; name: string }) {
  loading.value = true;
  apiClient.user
    .register({
      email: data.email,
      password: data.password,
      name: data.name,
      registerType: "user_register",
    })
    .then((response) => {
      if (response.result) {
        authStore.setEmail(data.email);
        goToLogin();
      } else {
        loading.value = false;
        showError("Erreur durant l'inscription");
      }
    });
}

function goToLogin() {
  router.push({
    name: "Login",
  });
}

const handleIconClick = (node: { props: { suffixIcon: string; type: string } }) => {
  node.props.suffixIcon = node.props.suffixIcon === "eye" ? "eyeClosed" : "eye";
  node.props.type = node.props.type === "password" ? "text" : "password";
};
</script>

<template>
  <PublicLayout title="Créer un compte">
    <p class="-mt-20 mb-20 cursor-pointer">
      Vous avez déjà un compte ? <span class="font-bold" @click="goToLogin">Connectez-vous</span>
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
          validation="required|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/"
          :validation-messages="{
            regex:
              'Le mot de passe doit contenir au moins 1 majuscule, 1 chiffre et 1 caractère spécial',
          }"
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
        Nous pouvons utiliser votre e-mail et vos appareils pour vous envoyer des actualités et des
        conseils sur les produits et services Beatly.
      </p>
      <div class="flex justify-center">
        <LandingButton
          label="S'inscrire"
          type="submit"
          :loading="loading"
        />
      </div>
    </FormKit>
  </PublicLayout>
</template>
