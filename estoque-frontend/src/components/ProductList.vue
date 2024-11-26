<template>
    <div class="product-list">
      <h2>Lista de Produtos</h2>
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>Código</th>
            <th>Categoria</th>
            <th>Fornecedor</th>
            <th>Estoque</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.name }}</td>
            <td>{{ product.code }}</td>
            <td>{{ product.category.name }}</td>
            <td>{{ product.supplier.name }}</td>
            <td>{{ product.stock }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import api from '../services/api';
  
  export default {
    data() {
      return {
        products: [],
      };
    },
    mounted() {
      this.fetchProducts();
    },
    methods: {
      async fetchProducts() {
        try {
          const response = await api.get('/products');
          this.products = response.data;
        } catch (error) {
          console.error('Erro ao buscar produtos', error);
        }
      },
    },
  };
  </script>
  