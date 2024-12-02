<template>
  <div class="container">
    <h2>Sistema de Controle de Estoque</h2>
    <h4>Bem-vindo, {{ user?.name || 'Visitante' }}</h4>
    <div v-if="errorMessage" class="error-message">
      <p>{{ errorMessage }}</p>
    </div>
    <div class="wrap-home-tables" v-if="!errorMessage">
      <div class="table-section">
        <h3>Produtos com Estoque Baixo</h3>
        <table>
          <thead>
            <tr>
              <th>Nome</th>
              <th>Código</th>
              <th>Estoque</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in lowStockProducts" :key="product.id">
              <td>{{ product.name }}</td>
              <td>{{ product.code }}</td>
              <td>{{ product.stock }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="table-section">
        <h3>Produtos Próximos do Vencimento</h3>
        <table>
          <thead>
            <tr>
              <th>Nome</th>
              <th>Data de Validade</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in expiringProducts" :key="product.id">
              <td>{{ product.name }}</td>
              <td>{{ product.expiration_date }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';

export default {
  data() {
    return {
      errorMessage: '',
      lowStockProducts: [],
      expiringProducts: [],
      user: null
    };
  },
  async created() {
    try {
      const lowStockResponse = await api.get('/products/low-stock');
      this.lowStockProducts = lowStockResponse.data;
    } catch (error) {
      this.errorMessage = 'Erro ao buscar produtos. Tente novamente mais tarde.';
      console.error('Erro ao buscar o usuário logado:', error);
    }

    try {
      const expiringResponse = await api.get('/products/expiring');
      this.expiringProducts = expiringResponse.data;
    } catch (error) {
      this.errorMessage = 'Erro ao buscar produtos. Tente novamente mais tarde.';
      console.error('Erro ao buscar o usuário logado:', error);
    }

    await this.fetchLoggedInUser();
  },
  methods: {
    async fetchLoggedInUser() {
      try {
        const response = await api.get('/user-logged-in');
        this.user = response.data;
      } catch (error) {
        this.errorMessage = 'Erro ao buscar o usuário logado. Tente novamente mais tarde.';
        console.error('Erro ao buscar o usuário logado:', error);
      }
    },
  },
};
</script>
