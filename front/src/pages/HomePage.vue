<script setup lang="ts">
import {onBeforeMount, ref} from "vue";
import InAppLayout from "@/components/layout/InAppLayout.vue";
import { useApiClient } from "@/stores/api-client";
import { useUserStore } from "@/stores/user";
import PlaylistPlayable from "@/components/cards/PlaylistPlayableCard.vue";
import albumDefaultIcon from "@/assets/icons/disc-dark.svg"

const { apiClient } = useApiClient();
const recentlyListened = ref(["", ""]);
const lastPlaylist = ref(["", ""]);
const recommendationList = ref(["", ""]);
const hitsList = ref(["", ""]);
const categoryList = ref(["", ""]);
const mostFavoriteList = ref(["", ""]);
const loading = ref(false);
const userStore = useUserStore();

onBeforeMount(async () => {
  loading.value = true;
  loading.value = false
  // const categorieList = await apiClient.categorie.categories(userStore.user.token).then((response) => {
  //     // if (response.categories) {
  //     //   categoriesList.value = response.categories;
  //     // }
  //   });
});

</script>

<template>
  <InAppLayout :loading="loading">
    <!-- Écoutes récentes si il y en a -->
     <div v-if="recentlyListened.length > 0" class="p-4">
      <h2 class="text-white text-3xl font-bold mb-4">Écoutes récentes</h2>
    </div>

    <div v-if="lastPlaylist.length > 0" class="p-4">
      <h2 class="text-white text-3xl font-bold mb-4">Dernières playlists</h2>
      <div class="flex flex-row justify-start gap-16">
        <PlaylistPlayable
          :playlistCover="albumDefaultIcon"
          playlistName="Ma première playlist !"
        />
        <PlaylistPlayable
          :playlistCover="albumDefaultIcon"
          playlistName="Ma première playlist !"
        />
        <PlaylistPlayable
          :playlistCover="albumDefaultIcon"
          playlistName="Ma première playlist !"
        />
        <PlaylistPlayable
          :playlistCover="albumDefaultIcon"
          playlistName="Ma première playlist !"
        />
      </div>
    </div>

    <div v-if="recommendationList.length > 0" class="p-4">
      <h2 class="text-white text-3xl font-bold mb-4">D'après vos écoutes...</h2>
    </div>

    <div v-if="hitsList.length > 0" class="p-4">
      <h2 class="text-white text-3xl font-bold mb-4">Hits du moment !</h2><!-- Les + écoutés  -->
    </div>

    <div v-if="categoryList.length > 0" class="p-4">
      <h2 class="text-white text-3xl font-bold mb-4">Pour une envie particulière</h2><!-- Les + écoutés  -->
    </div>

    <div v-if="mostFavoriteList.length > 0" class="p-4">
      <h2 class="text-white text-3xl font-bold mb-4">Ils font l'unanimité</h2><!-- Les + écoutés  -->
    </div>
  </InAppLayout>
</template>
