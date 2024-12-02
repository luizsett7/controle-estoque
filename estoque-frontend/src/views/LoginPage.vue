<template>
  <div class="login-page">
    <h2>Login</h2>

    <form @submit.prevent="loginUser">
      <div>
        <label for="email">E-mail:</label>
        <input name="email" v-model="form.email" type="email" id="email" required />
      </div>

      <div>
        <label for="password">Senha:</label>
        <input name="password" v-model="form.password" type="password" id="password" required />
      </div>

      <div v-if="userStore.errorMessage" class="error-message">
        {{ userStore.errorMessage }}
      </div>

      <div>
        <button type="submit" :disabled="userStore.isLoading">
          {{ userStore.isLoading ? "Entrando..." : "Entrar" }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useUserStore } from "../stores/user";
import { useRouter } from "vue-router";

const userStore = useUserStore();
const router = useRouter();

const form = ref({
  email: "",
  password: "",
});

const loginUser = async () => {
  try {
    await userStore.login(form.value); 

    if (userStore.token) {
      console.log("Login bem-sucedido! Redirecionando...");
      router.push("/"); 
    } else {
      console.log("Login falhou:", userStore.errorMessage);
    }
  } catch (error) {
    console.error("Erro durante o login:", error);
  }
};
</script>

<style scoped>
.login-page {
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
  padding: 30px;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  text-align: center;
  font-family: 'Roboto', sans-serif;
}

h2 {
  font-size: 2rem;
  color: #4CAF50;
  margin-bottom: 30px;
}

input {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1em;
  transition: all 0.3s ease;
}

input:focus {
  border-color: #4CAF50;
  outline: none;
  box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
}

button {
  width: 100%;
  padding: 12px;
  background-color: #4CAF50;
  color: white;
  font-size: 1.2rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #45a049;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.error-message {
  color: red;
  margin-bottom: 15px;
  font-size: 0.9em;
  text-align: left;
}

@media (max-width: 600px) {
  .login-page {
    padding: 20px;
  }

  h2 {
    font-size: 1.8rem;
  }

  button {
    font-size: 1.1rem;
  }
}
</style>
