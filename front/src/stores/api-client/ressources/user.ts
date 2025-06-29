import type { ApiClient } from "../model";

export type RegisterType = 'artist_register' | 'user_register';

export type RegisterData = {
  registerType: RegisterType,
  name: string,
  password: string,
  email: string,
};

type RegisterResponse = {
  status: boolean,
}

export default class Me {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async register(data: RegisterData): Promise<RegisterResponse> {
    return this.apiClient.post<{ status: number }>(`/register`, data)
      .then((response) => ({
          status: response.status === 201,
        })
      );
  }
}

