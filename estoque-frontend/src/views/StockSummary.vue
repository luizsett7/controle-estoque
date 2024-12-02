<template>
  <div class="container">
    <h2>Relatório de Saldo de Estoque</h2>
    <div v-if="errorMessage" class="error-message">
      <p>{{ errorMessage }}</p>
    </div>
    <div class="wrap-stock-summary-tables" v-if="!errorMessage">
      <div>
        <h3>Saldo Atual</h3>
        <table>
          <thead>
            <tr>
              <th>Total de Estoque</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ totalStock }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div>
        <h3>Saldo por Categoria</h3>
        <table>
          <thead>
            <tr>
              <th>Categoria</th>
              <th>Total de Estoque</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in stockByCategory" :key="category.category_name">
              <td>{{ category.category_name }}</td>
              <td>{{ category.total_stock }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div>
        <h3>Saldo por Fornecedor</h3>
        <table>
          <thead>
            <tr>
              <th>Fornecedor</th>
              <th>Total de Estoque</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="supplier in stockBySupplier" :key="supplier.supplier_name">
              <td>{{ supplier.supplier_name }}</td>
              <td>{{ supplier.total_stock }}</td>
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
      totalStock: 0,
      stockByCategory: [],
      stockBySupplier: []
    };
  },
  async created() {
    await this.fetchStockSummary();
  },
  methods: {
    async fetchStockSummary() {
      try {
        const response = await api.get('/stock-summary');
        this.totalStock = response.data.total_stock;
        this.stockByCategory = response.data.stock_by_category;
        this.stockBySupplier = response.data.stock_by_supplier;
      } catch (error) {
        this.errorMessage = 'Erro ao buscar resumo de estoque. Tente novamente mais tarde.';
        console.error('Erro ao buscar resumo de estoque:', error);
      }
    }
  }
};
</script>