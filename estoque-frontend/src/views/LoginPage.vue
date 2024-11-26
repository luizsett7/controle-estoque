<template>
    <div class="login-page">
      <h2>Login</h2>
  
      <form @submit.prevent="loginUser">
        <div>
          <label for="email">E-mail:</label>
          <input v-model="form.email" type="email" id="email" required />
        </div>
  
        <div>
          <label for="password">Senha:</label>
          <input v-model="form.password" type="password" id="password" required />
        </div>
  
        <div>
          <button type="submit">Entrar</button>
        </div>
      </form>
    </div>
  </template>
  
  <script>
  import api from '../services/api'; 
  
  export default {
    data() {
      return {
        form: {
          email: '',
          password: ''
        }
      };
    },
    methods: {
      async loginUser() {
        try {
          const response = await api.post('/login', this.form);
          localStorage.setItem('authToken', response.data.token);
          this.$router.push('/'); 
        } catch (error) {
          console.error('Erro ao fazer login', error);
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .login-page {
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
  }
  
  input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
  }
  
  button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
  }
  </style>
  