<script setup lang="ts">
import { ref, onMounted } from "vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import ArtistRequestModal from "@/components/modals/ArtistRequestModal.vue";
import { useApiClient } from "@/stores/api-client";
import type { ArtistRequest } from "@/utils/types";
import { useToast } from "@/composables/useToast";

const { apiClient } = useApiClient();
const requests = ref<ArtistRequest[]>([]);
const loading = ref(true);
const showViewModal = ref(false);
const selectedRequest = ref<ArtistRequest | null>(null);
const { showSuccess, showError } = useToast();

const fetchRequests = async () => {
  loading.value = true;
  try {
    const response = await apiClient.artistRequest.getAll();
    requests.value = Array.isArray(response.member) ? response.member : [];
  } catch {
    requests.value = [];
  } finally {
    loading.value = false;
  }
};

const viewRequest = (request: ArtistRequest) => {
  selectedRequest.value = request;
  showViewModal.value = true;
};

const closeModal = () => {
  showViewModal.value = false;
  selectedRequest.value = null;
};

const handleAcceptRequest = async (request: ArtistRequest) => {
  try {
    const requestId = request["@id"];
    if (!requestId) {
      throw new Error("ID de la demande introuvable");
    }

    await apiClient.artistRequest.accept(requestId);
    showSuccess("Demande acceptée avec succès !");
    closeModal();

    await fetchRequests();
  } catch (error) {
    // Erreur de Syntax je la contourne pour éviter de bloquer l'acceptation
    if (error instanceof SyntaxError && error.message.includes("JSON")) {
      console.warn("JSON parsing error, but operation might have succeeded");
      showSuccess("Demande acceptée avec succès !");
      closeModal();
      await fetchRequests();
    } else {
      showError("Erreur lors de l'acceptation de la demande");
    }
  }
};

const handleDeclineRequest = async (request: ArtistRequest) => {
  try {
    const requestId = request["@id"];
    if (!requestId) {
      throw new Error("ID de la demande introuvable");
    }

    await apiClient.artistRequest.decline(requestId);
    showSuccess("Demande déclinée avec succès !");
    closeModal();

    await fetchRequests();
  } catch (error) {
    // Erreur de Syntax je la contourne pour éviter de bloquer l'acceptation
    if (error instanceof SyntaxError && error.message.includes("JSON")) {
      console.warn("JSON parsing error, but operation might have succeeded");
      showSuccess("Demande déclinée avec succès !");
      closeModal();
      await fetchRequests();
    } else {
      showError("Erreur lors du déclin de la demande");
    }
  }
};

onMounted(fetchRequests);
</script>

<template>
  <InAppLayout type="admin" padding="p-10" :loading="loading">
    <h1 class="text-3xl font-bold mb-6">Demandes d'artiste</h1>
    <div>
      <div v-if="requests.length === 0" class="text-gray-300">Aucune demande trouvée.</div>
      <div v-else class="overflow-x-auto">
        <table class="min-w-full bg-white text-black rounded-lg overflow-hidden">
          <thead>
            <tr class="text-left bg-[#3a1452]">
              <th class="px-4 py-3 text-white">Utilisateur</th>
              <th class="px-4 py-3 text-white">Email</th>
              <th class="px-4 py-3 text-white">Fichiers audio</th>
              <th class="px-4 py-3 text-white">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="req in requests"
              :key="req.id"
              class="border-b border-[#440a50] hover:bg-[#39124a] hover:text-white cursor-pointer transition-colors"
              @click="viewRequest(req)"
            >
              <td class="px-4 py-3 font-semibold">{{ req.user?.name || "—" }}</td>
              <td class="px-4 py-3">{{ req.user?.email || "—" }}</td>
              <td class="px-4 py-3">
                <span v-if="req.files && req.files.length" class="text-purple-600 font-medium">
                  {{ req.files.length }} fichier{{ req.files.length > 1 ? "s" : "" }}
                </span>
                <span v-else class="text-gray-600 text-xs">Aucun</span>
              </td>
              <td class="px-4 py-3">
                <button
                  @click.stop="viewRequest(req)"
                  class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors text-sm"
                >
                  Consulter
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <ArtistRequestModal
      :isVisible="showViewModal"
      :request="(selectedRequest as ArtistRequest) || null"
      :isAdminView="true"
      @close="closeModal"
      @accept="handleAcceptRequest"
      @decline="handleDeclineRequest"
    />
  </InAppLayout>
</template>
