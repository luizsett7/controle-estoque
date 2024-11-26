<template>
  <div class="home-page">
    <h2>Bem-vindo ao Sistema de Controle de Estoque</h2>
    
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
</template>

<script>
import api from '../services/api';

export default {
  data() {
    return {
      lowStockProducts: [],
      expiringProducts: []
    };
  },
  async created() {
    const lowStockResponse = await api.get('/products/low-stock');
    this.lowStockProducts = lowStockResponse.data;

    const expiringResponse = await api.get('/products/expiring');
    this.expiringProducts = expiringResponse.data;
  }
};
</script>

<style scoped>
.home-page {
  text-align: center;
  margin: 20px;
}

.table-section {
  margin-top: 30px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

table th, table td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}
</style>
