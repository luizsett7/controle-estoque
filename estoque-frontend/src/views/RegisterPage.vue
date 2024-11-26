<template>
    <div class="register-page">
      <h2>Cadastro</h2>
      <form @submit.prevent="registerUser">
        <div>
          <label for="name">Nome:</label>
          <input v-model="form.name" type="text" id="name" required />
        </div>
        <div>
          <label for="email">E-mail:</label>
          <input v-model="form.email" type="email" id="email" required />
        </div>
        <div>
          <label for="password">Senha:</label>
          <input v-model="form.password" type="password" id="password" required />
        </div>
        <div>
          <label for="password_confirmation">Confirmar Senha:</label>
          <input v-model="form.password_confirmation" type="password" id="password_confirmation" required />
        </div>
        <div>
          <button type="submit">Cadastrar</button>
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
          name: '',
          email: '',
          password: '',
          password_confirmation: ''
        }
      };
    },
    methods: {
      async registerUser() {
        try {
          const response = await api.post('/register', this.form);
          this.$router.push('/login'); 
        } catch (error) {
          console.error('Erro ao cadastrar usuário', error);
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .register-page {
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
    background-color: #28a745;
    color: white;
    border: none;
    cursor: pointer;
  }
  </style>
  