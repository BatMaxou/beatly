import type { ApiClient } from "../model";

export type RegisterType = "artist_register" | "user_register";

export type RegisterData = {
  registerType: RegisterType;
  name: string;
  password: string;
  email: string;
};

type RegisterResponse = {
  status: boolean;
};

export type ForgotPasswordData = {
  email: string;
};

type ForgotPasswordResponse = {
  result: boolean;
};

export default class User {
  apiClient: ApiClient;

  constructor(apiClient: ApiClient) {
    this.apiClient = apiClient;
  }

  async register(data: RegisterData): Promise<RegisterResponse> {
    return this.apiClient.post<{ status: number }>(`/register`, data).then((response) => ({
      status: response.status === 201,
    }));
  }

  async forgotPassword(data: ForgotPasswordData): Promise<ForgotPasswordResponse> {
    const response = await this.apiClient
      .post<{ response: string; result: boolean }>(`/forgot-password`, data)
      .then((response) => ({
        result: response.result,
      }));
    return response;
  }
}
