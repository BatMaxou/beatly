<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import { useHead } from "@unhead/vue";

import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { User } from "@/utils/types";
import { Role } from "@/utils/types";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import ArtistRequestModal from "@/components/modals/ArtistRequestModal.vue";
import router from "@/router";
import { ressourceUrl } from "@/utils/tools";

useHead({
  title: "Beatly | Mon Compte",
});

const loading = ref(false);
const loadingButton = ref(false);
const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();

const user = ref<User>({} as User);
const profileForm = ref({
  name: "",
  avatar: null as File | null,
  wallpaper: null as File | null,
});

const avatarPreview = ref<string | null>(null);
const wallpaperPreview = ref<string | null>(null);
const showArtistRequestModal = ref(false);

const isArtist = computed(() => {
  return user.value?.roles?.some((role) => role === Role.ARTIST) || false;
});

const showDeleteModal = ref(false);
const deleteInput = ref("");
const deleting = ref(false);

const fetchUserData = async () => {
  try {
    loading.value = true;
    const userData = await apiClient.me.get();
    user.value = userData;
    profileForm.value.name = userData.name;
  } catch (error) {
    console.error("Erreur lors du chargement des données utilisateur:", error);
    showError("Erreur lors du chargement des données");
  } finally {
    loading.value = false;
  }
};

const handleAvatarChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (file) {
    profileForm.value.avatar = file;
    avatarPreview.value = URL.createObjectURL(file);
  }
};

const handleWallpaperChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (file) {
    profileForm.value.wallpaper = file;
    wallpaperPreview.value = URL.createObjectURL(file);
  }
};

const saveProfile = async () => {
  try {
    if (profileForm.value.name !== user.value?.name) {
      await apiClient.user.update(user.value.id, { name: profileForm.value.name });
    }

    if (profileForm.value.avatar || profileForm.value.wallpaper) {
      await apiClient.user.updateFiles(
        user.value.id,
        profileForm.value.avatar || undefined,
        profileForm.value.wallpaper || undefined,
      );
    }

    showSuccess("Profil mis à jour avec succès");
    await fetchUserData();

    avatarPreview.value = null;
    wallpaperPreview.value = null;
    profileForm.value.avatar = null;
    profileForm.value.wallpaper = null;
  } catch (error) {
    console.error("Erreur lors de la mise à jour du profil:", error);
    showError("Erreur lors de la mise à jour du profil");
  }
};

const forgotPassword = async () => {
  loadingButton.value = true;
  try {
    if (!user.value?.email) {
      showError("Email utilisateur non disponible");
      return;
    }

    await apiClient.user.forgotPassword({ email: user.value.email });
    showSuccess("Un email de réinitialisation a été envoyé à votre adresse email");
  } catch (error) {
    console.error("Erreur lors de l'envoi de l'email de réinitialisation:", error);
    showError("Erreur lors de l'envoi de l'email de réinitialisation");
  }
  loadingButton.value = false;
};

const requestArtistConversion = () => {
  showArtistRequestModal.value = true;
};

const confirmDeleteAccount = async () => {
  if (deleteInput.value !== "supprimer") return;
  deleting.value = true;
  try {
    await apiClient.user.delete(user.value.id);
    showSuccess("Compte supprimé avec succès");
    router.push("/logout");
  } catch (error) {
    console.error("Erreur lors de la suppression du compte:", error);
    showError("Erreur lors de la suppression du compte");
  }
  deleting.value = false;
  closeDeleteModal();
};

const handleModalClose = () => {
  showArtistRequestModal.value = false;
};

const openDeleteModal = () => {
  showDeleteModal.value = true;
  deleteInput.value = "";
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  deleteInput.value = "";
};

const handleRequestSuccess = () => {
  showArtistRequestModal.value = false;
  fetchUserData();
};

onMounted(() => {
  fetchUserData();
});
</script>

<template>
  <InAppLayout :loading="loading" padding="p-8">
    <div class="max-w-4xl mx-auto">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Mon Compte</h1>
        <p class="text-white/70">Gérez vos informations personnelles et paramètres</p>
      </div>

      <div class="space-y-8">
        <div class="bg-[#1a0725] border border-[#440a50] rounded-xl p-6">
          <h2 class="text-xl font-semibold text-white mb-6">Informations du profil</h2>

          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-white mb-2">Avatar</label>
              <div class="flex items-center space-x-4">
                <div
                  class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 overflow-hidden"
                >
                  <img
                    :src="avatarPreview || (user?.avatar ? ressourceUrl + user.avatar : '')"
                    :alt="user?.name || 'Avatar'"
                    class="w-full h-full object-cover"
                    v-if="avatarPreview || user?.avatar"
                  />
                  <div
                    v-else
                    class="w-full h-full flex items-center justify-center text-white font-semibold"
                  >
                    {{ user?.name?.charAt(0)?.toUpperCase() }}
                  </div>
                </div>
                <div>
                  <input
                    type="file"
                    accept="image/*"
                    @change="handleAvatarChange"
                    class="hidden"
                    id="avatar-input"
                  />
                  <label
                    for="avatar-input"
                    class="px-4 py-2 bg-[#440a50] text-white rounded-lg hover:bg-[#5a0f60] transition-colors cursor-pointer"
                  >
                    Changer l'avatar
                  </label>
                </div>
              </div>
            </div>

            <div v-if="isArtist">
              <label class="block text-sm font-medium text-white mb-2"
                >Wallpaper (Profil Artiste)</label
              >
              <div class="space-y-4">
                <div
                  class="w-full h-32 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 overflow-hidden"
                >
                  <img
                    :src="
                      wallpaperPreview || (user?.wallpaper ? ressourceUrl + user.wallpaper : '')
                    "
                    alt="Wallpaper"
                    class="w-full h-full object-cover"
                    v-if="wallpaperPreview || user?.wallpaper"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center text-white">
                    Aucun wallpaper
                  </div>
                </div>
                <div>
                  <input
                    type="file"
                    accept="image/*"
                    @change="handleWallpaperChange"
                    class="hidden"
                    id="wallpaper-input"
                  />
                  <label
                    for="wallpaper-input"
                    class="px-4 py-2 bg-[#440a50] text-white rounded-lg hover:bg-[#5a0f60] transition-colors cursor-pointer"
                  >
                    Changer le wallpaper
                  </label>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-white mb-2">Nom</label>
              <input
                v-model="profileForm.name"
                type="text"
                class="w-full px-3 py-2 bg-[#2a0d35] border border-[#440a50] rounded-lg text-white placeholder-white/50 focus:outline-none focus:border-[#5a0f60] transition-colors"
                placeholder="Votre nom"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-white mb-2">Email</label>
              <input
                :value="user?.email"
                type="email"
                readonly
                class="w-full px-3 py-2 bg-[#2a0d35]/50 border border-[#440a50] rounded-lg text-white/70 cursor-not-allowed"
              />
              <p class="text-xs text-white/50 mt-1">L'email ne peut pas être modifié</p>
            </div>

            <div class="flex justify-end">
              <button
                @click="saveProfile"
                :disabled="!profileForm.name.trim()"
                class="px-6 py-2 bg-[#440a50] text-white rounded-lg hover:bg-[#5a0f60] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Sauvegarder les modifications
              </button>
            </div>
          </div>
        </div>

        <div class="bg-[#1a0725] border border-[#440a50] rounded-xl p-6">
          <h2 class="text-xl font-semibold text-white mb-6">Mot de passe</h2>

          <div class="space-y-4">
            <p class="text-white/70 leading-relaxed">
              Pour des raisons de sécurité, vous ne pouvez pas modifier votre mot de passe
              directement depuis cette page. Utilisez la fonction "Mot de passe oublié" pour
              recevoir un lien de réinitialisation par email.
            </p>

            <div class="bg-[#2a0d35] border border-[#440a50] rounded-lg p-4">
              <div class="flex items-center justify-between">
                <div>
                  <h4 class="text-white font-medium mb-1">Réinitialiser le mot de passe</h4>
                  <p class="text-white/70 text-sm">Un email sera envoyé à : {{ user?.email }}</p>
                </div>
                <button
                  @click="forgotPassword"
                  class="px-4 py-2 bg-[#440a50] text-white rounded-lg hover:bg-[#5a0f60] transition-colors whitespace-nowrap"
                >
                  <span v-if="loadingButton">Chargement...</span>
                  <span v-else>Mot de passe oublié</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="!isArtist" class="bg-[#1a0725] border border-[#440a50] rounded-xl p-6">
          <h2 class="text-xl font-semibold text-white mb-4">Devenir Artiste</h2>

          <div class="space-y-4">
            <p class="text-white/70 leading-relaxed">
              Vous souhaitez partager votre musique avec la communauté Beatly ? Demandez la
              conversion de votre compte en compte artiste pour pouvoir uploader vos créations et
              gérer votre profil artistique.
            </p>

            <div class="bg-[#2a0d35] border border-[#440a50] rounded-lg p-4">
              <h4 class="text-white font-medium mb-2">Avantages du compte artiste :</h4>
              <ul class="text-white/70 text-sm space-y-1">
                <li>• Upload et gestion de vos musiques</li>
                <li>• Création et gestion d'albums</li>
                <li>• Personnalisation de votre profil avec wallpaper</li>
                <li>• Statistiques d'écoute détaillées</li>
                <li>• Visibilité accrue sur la plateforme</li>
              </ul>
            </div>

            <div class="flex justify-end">
              <button
                :disabled="user.artistRequest ? true : false"
                @click="requestArtistConversion"
                :class="
                  'px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all' +
                  (user.artistRequest ? ' opacity-50 cursor-not-allowed' : '')
                "
              >
                <span v-if="user.artistRequest">Demande déjà soumise</span>
                <span v-else>Faire une demande</span>
              </button>
            </div>
          </div>
        </div>

        <div
          v-else
          class="bg-gradient-to-r from-purple-500/20 to-pink-500/20 border border-purple-500/30 rounded-xl p-6"
        >
          <div class="flex items-center space-x-3">
            <div
              class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center"
            >
              <span class="text-white font-bold">🎵</span>
            </div>
            <div>
              <h3 class="text-white font-semibold">Compte Artiste Actif</h3>
              <p class="text-white/70 text-sm">
                Vous avez accès à toutes les fonctionnalités artiste
              </p>
            </div>
          </div>
        </div>

        <div class="bg-[#1a0725] border border-[#440a50] rounded-xl p-6">
          <h2 class="text-xl font-semibold text-white mb-6">Déconnexion</h2>

          <div class="space-y-4 flex flex-row justify-between items-center">
            <p class="text-white/70 leading-relaxed">
              Vous avez besoin d'un peu de calme? <br />On peut comprendre, on ne vous en veut
              pas...<br />Mais revenez vite quand même !
            </p>
            <button
              @click="router.push('/logout')"
              class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-500 text-white rounded-lg font-semibold shadow-lg hover:from-pink-600 hover:to-purple-600 transition-all text-lg"
            >
              Déconnexion
            </button>
          </div>
        </div>

        <div class="bg-[#1a0725] border border-[#440a50] rounded-xl p-6">
          <h2 class="text-xl font-semibold text-white mb-6">Supprimer le compte</h2>

          <div class="space-y-4 flex flex-row justify-between items-center">
            <p class="text-white/70 leading-relaxed">
              Attention, cette action est irréversible ! <br />
              Votre compte sera supprimé définitivement, ainsi que toutes vos données associées.
            </p>
            <button
              @click="openDeleteModal"
              class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-500 text-white rounded-lg font-semibold shadow-lg hover:from-pink-600 hover:to-purple-600 transition-all text-lg"
            >
              Supprimer le compte
            </button>
          </div>
        </div>
      </div>
    </div>
    <ArtistRequestModal
      :isVisible="showArtistRequestModal"
      @close="handleModalClose"
      @success="handleRequestSuccess"
    />

    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60"
    >
      <div
        class="bg-[#1a0725] border border-[#440a50] rounded-xl shadow-xl w-full max-w-md mx-4 p-8 relative"
      >
        <button @click="closeDeleteModal" class="absolute top-4 right-4 text-white text-2xl">
          &times;
        </button>
        <h3 class="text-xl font-bold text-red-400 mb-4">Confirmer la suppression</h3>
        <p class="text-white mb-4">
          Pour supprimer votre compte, tapez
          <span class="font-mono bg-[#440a50] px-2 py-1 rounded">supprimer</span> ci-dessous :
        </p>
        <input
          v-model="deleteInput"
          type="text"
          placeholder="Tapez 'supprimer' pour confirmer"
          class="w-full px-3 py-2 mb-4 bg-[#2a0d35] border border-[#440a50] rounded-lg text-white placeholder-white/50 focus:outline-none focus:border-red-500 transition-colors"
        />
        <button
          :disabled="deleteInput !== 'supprimer' || deleting"
          @click="confirmDeleteAccount"
          class="w-full px-6 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-lg font-semibold shadow-lg hover:from-red-600 hover:to-pink-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="deleting">Suppression...</span>
          <span v-else>Supprimer définitivement</span>
        </button>
      </div>
    </div>
  </InAppLayout>
</template>
