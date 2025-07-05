<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useApiClient } from "@/stores/api-client";
import imageVinyl from "@/assets/images/vinyl-black-and-white-landing.jpg";
import logo from "@/assets/beatly-logo-white.png";
import loading from "@/assets/icons/loading-light.svg";

const router = useRouter();
const { apiClient } = useApiClient();
const route = useRoute();
const resetToken = ref("");
const isAuthorized = ref(false);
const isLoading = ref(true);

onMounted(async () => {
  resetToken.value = route.params.token as string;

  if (!resetToken.value) {
    return;
  }

  const response = await apiClient.user
    .verifyToken({ token: resetToken.value })
    .then((response) => {
      if (response.result) {
        isAuthorized.value = true;
      } else {
        isAuthorized.value = false;
        setTimeout(() => {
          router.push("/forgot-password");
        }, 5000);
      }
      isLoading.value = false;
    });
});

function handleSubmitResetPasswordForm(data: { password: string }) {
  apiClient.user
    .resetPassword({ password: data.password, token: resetToken.value })
    .then((response) => {
      if (response.result) {
        // Toastr Success Mot de passe mis à jour avec succès
        setTimeout(() => {
          router.push("/login");
        }, 3000);
      } else {
        // Toastr Erreur durant la réinitialisation du mot de passe
      }
    })
    .catch((error) => {
      console.error("Erreur lors de la réinitialisation du mot de passe :", error);
    });
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

      <h2 class="text-2xl uppercase text-center mt-12 mb-20">Réinitialisation du mot de passe</h2>

      <!-- Affichage du loader le temps de vérifier le token -->
      <div
        v-if="isLoading"
        class="absolute inset-0 container z-20 flex flex-col items-center justify-center h-full"
      >
        <div class="flex flex-col items-center">
          <img :src="loading" alt="Chargement" class="h-12 w-12 animate-spin mb-4" />
          <p class="text-white text-lg">Chargement...</p>
        </div>
      </div>

      <!-- Affichage du formulaire si verification terminée et valide -->
      <FormKit
        v-if="!isLoading && isAuthorized"
        type="form"
        :classes="{
          form: 'w-full flex flex-col gap-4 max-w-[400px]',
        }"
        @submit="handleSubmitResetPasswordForm"
        :actions="false"
        :messages-class="'hidden'"
      >
        <div class="flex flex-col gap-2 justify-start">
          <FormKit
            type="password"
            name="password"
            label="Mot de passe"
            :classes="{
              input:
                'px-4 py-2 max-h-12 h-12 w-full text-black rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]',
            }"
            validation="required|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/"
            :validation-messages="{
              regex:
                'Le mot de passe doit contenir au moins 1 majuscule, 1 chiffre et 1 caractère spécial',
            }"
            validation-visibility="submit"
            placeholder="Mot de passe"
          />
        </div>
        <div class="flex flex-col gap-2 justify-start">
          <FormKit
            type="password"
            name="password"
            label="Confirmation du mot de passe"
            :classes="{
              input:
                'px-4 py-2 max-h-12 h-12 w-full text-black rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]',
            }"
            validation="required|confirm:password"
            :validation-messages="{
              confirm: 'Les mot de passe ne correspondent pas',
            }"
            validation-visibility="submit"
            placeholder="Confirmation du mot de passe"
          />
        </div>
        <div class="flex justify-center mt-8 mb-16">
          <FormKit
            type="submit"
            label="Enregistrer le mot de passe"
            :classes="{
              input:
                'uppercase bg-[#B00D70] rounded-3xl w-fit px-10 py-2 text-sm font-bold text-white hover:bg-[#940a5e] transition disabled:opacity-50',
            }"
          />
        </div>
      </FormKit>

      <!-- Affichage du formulaire si verification terminée et invalide -->
      <div v-if="!isLoading && !isAuthorized" class="text-white text-center">
        <p>Le lien de réinitialisation du mot de passe est invalide ou a expiré.</p>
        <p>
          Vous allez être redirigé vers la page de réinitialisation du mot de passe dans quelques
          secondes.
        </p>
      </div>
    </div>
  </div>
</template>
