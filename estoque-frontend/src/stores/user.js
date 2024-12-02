import { defineStore } from "pinia";
import api from "../services/api";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null, 
    token: localStorage.getItem("authToken") || null, 
    errorMessage: "", 
    isLoading: false, 
  }),
  actions: {
    setErrorMessage(message) {
      this.errorMessage = message;
    },
    async login(form) {
      this.isLoading = true;
      this.errorMessage = "";
      try {
        const response = await api.post("/login", form);

        console.log("Resposta da API:", response.data);

        if (response.data.token) {
          this.token = response.data.token;
          this.user = { email: form.email, role: response.data.user.role };

          localStorage.setItem("authToken", this.token);
          localStorage.setItem("user", JSON.stringify(this.user));
        } else {
          this.errorMessage = "Erro ao processar login. Tente novamente.";
        }
      } catch (error) {
        this.errorMessage = "Credenciais inválidas ou erro no servidor.";
        console.error("Erro ao fazer login:", error);
      } finally {
        this.isLoading = false; 
      }
    },
    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem("authToken");
      localStorage.removeItem("user");
    },
  },
  getters: {
    isAuthorized: (state) => {
      return state.user && ["admin", "manager"].includes(state.user.role); 
    },
  },
});
