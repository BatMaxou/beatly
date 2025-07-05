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

export type ResetPasswordData = {
  token: string;
  password: string;
};

type ResetPasswordResponse = {
  result: boolean;
  message: string;
};

export type VerifyTokenData = {
  token: string;
};

type VerifyTokenResponse = {
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

  async resetPassword(data: ResetPasswordData): Promise<ResetPasswordResponse> {
    const response = await this.apiClient
      .post<{ message: string; result: boolean }>(`/reset-password`, data)
      .then((response) => ({
        message: response.message,
        result: response.result,
      }));
    return response;
  }

  async verifyToken(data: VerifyTokenData): Promise<VerifyTokenResponse> {
    try {
      const response = await this.apiClient
        .post<{ result: boolean }>(`/verify-token`, data)
        .then((response) => ({
          result: response.result,
        }));
      return response;
    } catch (error) {
      console.error("Erreur lors de la vérification du token:", error);
      return { result: false };
    }
  }
}
