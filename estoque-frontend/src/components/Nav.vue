<script setup>
import { onMounted } from 'vue';
import { useUserStore } from '../stores/user'; 
import { useRouter } from 'vue-router';

const userStore = useUserStore();
const router = useRouter();

onMounted(() => {
  const storedUser = localStorage.getItem('user');
  if (storedUser) {
    userStore.user = JSON.parse(storedUser); 
  }
});

const logout = () => {
  userStore.logout();
  router.push('/login'); 
};
</script>

<template>
  <nav v-if="userStore.user">
    <RouterLink to="/">Home</RouterLink>
    <RouterLink to="/supplier">Fornecedores</RouterLink>
    <RouterLink to="/category">Categorias</RouterLink>
    <RouterLink to="/product">Produtos</RouterLink>
    <RouterLink to="/movement">Movimentos</RouterLink>  
    <RouterLink to="/stock-summary">Resumo</RouterLink>      
    <RouterLink v-if="userStore.isAuthorized" to="/user">Usuários</RouterLink>
    <button @click="logout">Logout</button>
  </nav>
</template>

<style scoped>
nav {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #4CAF50; /* Verde suave */
  padding: 10px 20px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra sutil */
  border-radius: 8px;
}

nav a {
  text-decoration: none;
  color: white;
  font-weight: bold;
  font-size: 1.1em;
  margin: 0 15px;
  padding: 8px 12px;
  border-radius: 4px;
  transition: background-color 0.3s ease, color 0.3s ease;
}

nav a:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

button {
  background-color: #f44336; 
  color: white;
  border: none;
  border-radius: 4px;
  padding: 8px 12px;
  font-size: 1.1em;
  font-weight: bold;
  margin-left: 15px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

button:hover {
  background-color: #d32f2f; 
}

button:active {
  transform: scale(0.95); 
}

@media (max-width: 768px) {
  nav {
    flex-direction: column; 
    padding: 15px;
  }

  nav a, button {
    margin: 10px 0;
  }
}
</style>
