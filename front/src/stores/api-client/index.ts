import { ref, type Ref } from "vue"
import { defineStore } from "pinia"

import { ApiClient } from "./model"

export type ApiClientStore = {
  apiClient: Ref<ApiClient>
}

export const useApiClient = defineStore('api-client', (): ApiClientStore => {
  const apiClient: Ref<ApiClient> = ref(new ApiClient())

  return {
    apiClient,
  }
})
