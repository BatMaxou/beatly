<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import PublicLayout from "@/components/layout/PublicLayout.vue";
import loading from "@/assets/icons/loading-light.svg";
import LandingButton from "@/components/buttons/LandingButton.vue";

const router = useRouter();
const { apiClient } = useApiClient();
const route = useRoute();
const { showSuccess, showError } = useToast();
const resetToken = ref("");
const isAuthorized = ref(true);
const isLoading = ref(true);
const loadingSubmit = ref(false);

onMounted(async () => {
  resetToken.value = route.params.token as string;

  if (!resetToken.value) {
    return;
  }

    await apiClient.user
    .verifyToken({ token: resetToken.value })
    .then((response) => {
      if (response.result) {
        isLoading.value = false;
      } else {
        isAuthorized.value = false;
        setTimeout(() => {
          router.push("/forgot-password");
        }, 5000);
      }
    });
});

function handleSubmitResetPasswordForm(data: { password: string }) {
  loadingSubmit.value = true;
  apiClient.user
    .resetPassword({ password: data.password, token: resetToken.value })
    .then((response) => {
      if (response.result) {
        showSuccess("Mot de passe mit à jour avec succès");
        setTimeout(() => {
          router.push("/login");
        }, 2000);
      } else {
        loadingSubmit.value = false;
        showError("Erreur durant la réinitialisation du mot de passe");
      }
    })
    .catch(() => {
      loadingSubmit.value = false;
      showError("Erreur lors de la réinitialisation du mot de passe");
    });
}
</script>

<template>
  <PublicLayout title="Réinitialisation du mot de passe">
    <!-- Affichage du loader le temps de vérifier le token -->
    <div
      v-if="isLoading"
      class="absolute inset-0 container z-20 flex flex-col items-center justify-center h-full"
    >
      <div class="flex flex-col items-center max-w-[400px] w-full">
        <img :src="loading" alt="Chargement" class="h-12 w-12 animate-spin mb-4" />
        <p class="text-white text-lg text-center" v-if="!isAuthorized">
          Le lien de réinitialisation du mot de passe est invalide ou a expiré. Vous allez être
          redirigé vers la page de réinitialisation du mot de passe dans quelques secondes.
        </p>
        <p v-if="isAuthorized" class="text-white text-lg">Chargement...</p>
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
          name="password_confirm"
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
        <LandingButton
          label="Enregistrer le mot de passe"
          type="submit"
          :loading="loadingSubmit"
        />
      </div>
    </FormKit>
  </PublicLayout>
</template>
