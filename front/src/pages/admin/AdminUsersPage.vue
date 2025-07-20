<script setup lang="ts">
import { ref, onMounted } from "vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import type { User } from "@/utils/types";
import eyeDark from "@/assets/icons/eye-dark.svg";
import removeDark from "@/assets/icons/remove-dark.svg";
import eyeLight from "@/assets/icons/eye-light.svg";
import removeLight from "@/assets/icons/remove-light.svg";
import { useRouter } from "vue-router";
import BackButton from "@/components/navigation/BackButton.vue";
import { ressourceUrl } from "@/utils/tools";
import UserDetailModal from "@/components/modals/UserDetailModal.vue";

const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();
const router = useRouter();

const users = ref<User[]>([]);
const loading = ref(true);
const showUserModal = ref(false);
const selectedUser = ref<User | null>(null);

const currentPage = ref(1);
const totalItems = ref(0);
const itemsPerPage = ref(30);

const fetchUsers = async (page: number = 1) => {
  loading.value = true;
  try {
    const response = await apiClient.user.getAll(page);
    users.value = response.member || [];
    totalItems.value = response.totalItems || 0;
    currentPage.value = page;
  } catch (error) {
    console.error("Erreur lors de la récupération des utilisateurs:", error);
    showError("Erreur lors de la récupération des utilisateurs");
    users.value = [];
  } finally {
    loading.value = false;
  }
};

const viewUser = (user: User) => {
  selectedUser.value = user;
  showUserModal.value = true;
};

const closeUserModal = () => {
  showUserModal.value = false;
  selectedUser.value = null;
};

const deleteUser = async (user: User) => {
  if (!confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${user.name}" ?`)) {
    return;
  }

  try {
    const userId = user.id || user["@id"]?.split("/").pop();
    if (!userId) {
      throw new Error("ID de l'utilisateur introuvable");
    }

    await apiClient.user.delete(userId);
    showSuccess("Utilisateur supprimé avec succès !");
    await fetchUsers(currentPage.value);
  } catch (error) {
    console.error("Erreur lors de la suppression:", error);
    showError("Erreur lors de la suppression de l'utilisateur");
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

const goToNextPage = () => {
  fetchUsers(currentPage.value + 1);
};

const goToPreviousPage = () => {
  if (currentPage.value > 1) {
    fetchUsers(currentPage.value - 1);
  }
};

onMounted(() => fetchUsers(1));
</script>

<template>
  <InAppLayout type="admin" padding="p-10" :loading="loading">
    <div>
      <BackButton to="/admin" />
      <h1 class="text-3xl font-bold mb-6">Gestion des utilisateurs</h1>

      <div v-if="users.length === 0" class="text-gray-300">Aucun utilisateur trouvé.</div>
      <div v-else>
        <!-- Pagination info -->
        <div class="mb-4 text-gray-300">
          Page {{ currentPage }} - {{ users.length }} utilisateurs affichés ({{ totalItems }} au
          total)
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full bg-white text-black rounded-lg overflow-hidden">
            <thead>
              <tr class="text-left bg-[#3a1452]">
                <th class="px-4 py-3 text-white">Avatar</th>
                <th class="px-4 py-3 text-white">Nom</th>
                <th class="px-4 py-3 text-white">Email</th>
                <th class="px-4 py-3 text-white">Rôles</th>
                <th class="px-4 py-3 text-white">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="user in users"
                :key="user.id"
                class="border-b border-[#440a50] hover:bg-[#39124a] hover:text-white cursor-pointer transition-colors group"
                @click="viewUser(user)"
              >
                <td class="px-4 py-3">
                  <div
                    class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full overflow-hidden"
                  >
                    <img
                      v-if="user.avatar"
                      :src="ressourceUrl + user.avatar"
                      :alt="user.name"
                      class="w-full h-full object-cover"
                    />
                    <div
                      v-else
                      class="w-full h-full flex items-center justify-center text-white text-xs"
                    >
                      Avatar
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 font-semibold">{{ user.name || "—" }}</td>
                <td class="px-4 py-3">{{ user.email || "—" }}</td>
                <td class="px-4 py-3">
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="role in user.roles"
                      :key="role"
                      :class="['px-2 py-1 rounded text-xs font-medium', getRoleColor(role)]"
                    >
                      {{ formatRole(role) }}
                    </span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex space-x-2">
                    <button
                      @click.stop="viewUser(user)"
                      class="p-2 rounded hover:bg-blue-500 transition-colors"
                      title="Consulter"
                    >
                      <img :src="eyeDark" :class="['w-5 h-5', 'group-hover:hidden']" alt="Voir" />
                      <img
                        :src="eyeLight"
                        :class="['w-5 h-5', 'hidden group-hover:block']"
                        alt="Voir"
                      />
                    </button>
                    <button
                      @click.stop="deleteUser(user)"
                      class="p-2 rounded hover:bg-red-500 transition-colors"
                      title="Supprimer"
                    >
                      <img
                        :src="removeDark"
                        :class="['w-5 h-5', 'group-hover:hidden']"
                        alt="Supprimer"
                      />
                      <img
                        :src="removeLight"
                        :class="['w-5 h-5', 'hidden group-hover:block']"
                        alt="Supprimer"
                      />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination controls -->
        <div class="mt-6 flex justify-center items-center space-x-4">
          <button
            @click="goToPreviousPage"
            :disabled="currentPage === 1"
            class="px-4 py-2 bg-[#3a1452] text-white rounded hover:bg-[#4a1562] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Page précédente
          </button>

          <span class="text-white">Page {{ currentPage }}</span>

          <button
            @click="goToNextPage"
            :disabled="users.length < itemsPerPage"
            class="px-4 py-2 bg-[#3a1452] text-white rounded hover:bg-[#4a1562] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Page suivante
          </button>
        </div>
      </div>

      <UserDetailModal :is-visible="showUserModal" :user="selectedUser" @close="closeUserModal" />
    </div>
  </InAppLayout>
</template>
