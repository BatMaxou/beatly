<script setup lang="ts">
import { ref } from "vue";
import type { User } from "@/utils/types";
import { ressourceUrl } from "@/utils/tools";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";

interface Props {
  isVisible: boolean;
  user: User | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  close: [];
}>();

const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();

const sendingPassword = ref(false);

const closeModal = () => {
  emit("close");
};

const formatRole = (role: string) => {
  switch (role) {
    case "ROLE_PLATFORM":
      return "Admin";
    case "ROLE_ARTIST":
      return "Artiste";
    case "ROLE_USER":
      return "Utilisateur";
    default:
      return role.replace("ROLE_", "").toLowerCase();
  }
};

const getRoleColor = (role: string) => {
  switch (role) {
    case "ROLE_PLATFORM":
      return "bg-red-100 text-red-800";
    case "ROLE_ARTIST":
      return "bg-purple-100 text-purple-800";
    case "ROLE_USER":
      return "bg-blue-100 text-blue-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};

const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === "Escape" && props.isVisible) {
    closeModal();
  }
};

const sendNewPassword = async () => {
  if (!props.user?.email) {
    showError("Aucun email disponible pour cet utilisateur");
    return;
  }

  sendingPassword.value = true;
  try {
    await apiClient.user.forgotPassword({ email: props.user.email });
    showSuccess(`Nouveau mot de passe envoyé à ${props.user.email}`);
  } catch (error) {
    console.error("Erreur lors de l'envoi du mot de passe:", error);
    showError("Erreur lors de l'envoi du nouveau mot de passe");
  } finally {
    sendingPassword.value = false;
  }
};
</script>

<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
    @click.self="closeModal"
    @keydown="handleKeydown"
  >
    <div
      class="bg-[#1a0725] border border-[#440a50] rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden"
      @click.stop
    >
      <div class="p-6 border-b border-[#440a50] relative">
        <h2 class="text-xl font-semibold text-white text-center">Détail de l'utilisateur</h2>
        <button
          @click="closeModal"
          class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-[#440a50] transition-colors"
        >
          <span class="text-white text-xl">&times;</span>
        </button>
      </div>

      <div class="p-6 max-h-[70vh] overflow-y-auto scrollbar-custom">
        <div v-if="user" class="space-y-6">
          <!-- Avatar and basic info -->
          <div class="flex items-start space-x-6">
            <div class="relative">
              <div
                class="w-24 h-24 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full overflow-hidden"
              >
                <img
                  v-if="user.avatar"
                  :src="ressourceUrl + user.avatar"
                  :alt="user.name"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-white">
                  Avatar
                </div>
              </div>
            </div>

            <div class="flex-1">
              <div class="grid grid-cols-1 gap-4">
                <!-- Nom -->
                <div>
                  <label class="block text-sm font-medium text-white mb-2">Nom</label>
                  <p class="text-white text-lg">{{ user.name || "—" }}</p>
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-medium text-white mb-2">Email</label>
                  <p class="text-white text-lg">{{ user.email || "—" }}</p>
                </div>

                <!-- ID -->
                <div>
                  <label class="block text-sm font-medium text-white mb-2">ID</label>
                  <p class="text-white text-lg">{{ user.id || "—" }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Rôles et Actions -->
          <div class="flex items-start justify-between gap-6">
            <div class="flex-1">
              <label class="block text-sm font-medium text-white mb-2">Rôles</label>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="role in user.roles"
                  :key="role"
                  :class="['px-3 py-1 rounded text-sm font-medium', getRoleColor(role)]"
                >
                  {{ formatRole(role) }}
                </span>
              </div>
            </div>

            <div class="flex flex-col gap-2">
              <label class="block text-sm font-medium text-white mb-2">Actions</label>
              <button
                @click="sendNewPassword"
                :disabled="sendingPassword || !user.email"
                class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ sendingPassword ? "Envoi en cours..." : "Envoyer nouveau mot de passe" }}
              </button>
            </div>
          </div>

          <!-- Wallpaper si disponible -->
          <div v-if="user.wallpaper">
            <label class="block text-sm font-medium text-white mb-2">Wallpaper</label>
            <div class="w-full h-32 bg-gray-800 rounded-lg overflow-hidden">
              <img
                :src="ressourceUrl + user.wallpaper"
                alt="Wallpaper"
                class="w-full h-full object-cover"
              />
            </div>
          </div>

          <!-- Demande d'artiste si disponible -->
          <div v-if="user.artistRequest">
            <label class="block text-sm font-medium text-white mb-2">Demande d'artiste</label>
            <div class="bg-[#2a0d35] border border-[#440a50] rounded-lg p-4">
              <p class="text-white">Cet utilisateur a une demande d'artiste en cours</p>
            </div>
          </div>
        </div>
      </div>

      <div class="p-6 border-t border-[#440a50] bg-[#2a0d35]/50">
        <div class="flex justify-center">
          <button
            type="button"
            @click="closeModal"
            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.scrollbar-custom {
  scrollbar-width: thin;
  scrollbar-color: rgba(68, 10, 80, 0.8) rgba(68, 10, 80, 0.2);
}

.scrollbar-custom::-webkit-scrollbar {
  width: 8px;
}

.scrollbar-custom::-webkit-scrollbar-track {
  background: rgba(68, 10, 80, 0.2);
  border-radius: 4px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
  background: rgba(68, 10, 80, 0.8);
  border-radius: 4px;
}

.scrollbar-custom::-webkit-scrollbar-thumb:hover {
  background: rgba(90, 15, 96, 1);
}
</style>
