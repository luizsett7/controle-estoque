<template>
    <div class="product-management">
      <h2>Gestão de Produtos</h2>
  
      <button @click="showAddProductForm = !showAddProductForm">
        {{ showAddProductForm ? 'Cancelar' : 'Adicionar Produto' }}
      </button>
  
      <div v-if="showAddProductForm">
        <h3>Cadastrar Novo Produto</h3>
        <form @submit.prevent="addProduct">
          <div>
            <label for="name">Nome:</label>
            <input v-model="newProduct.name" type="text" required />
          </div>
          <div>
            <label for="code">Código:</label>
            <input v-model="newProduct.code" type="text" required />
          </div>
          <div>
            <label for="category">Categoria:</label>
            <input v-model="newProduct.category" type="text" required />
          </div>
          <div>
            <label for="price">Preço:</label>
            <input v-model="newProduct.price" type="number" required />
          </div>
          <div>
            <label for="stock">Estoque:</label>
            <input v-model="newProduct.stock" type="number" required />
          </div>
          <button type="submit">Salvar</button>
        </form>
      </div>
  
      <h3>Produtos Cadastrados</h3>
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>Código</th>
            <th>Preço</th>
            <th>Estoque</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.name }}</td>
            <td>{{ product.code }}</td>
            <td>{{ product.price }}</td>
            <td>{{ product.stock }}</td>
            <td>
              <button @click="editProduct(product)">Editar</button>
              <button @click="deleteProduct(product.id)">Excluir</button>
            </td>
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
        showAddProductForm: false,
        newProduct: {
          name: '',
          code: '',
          category: '',
          price: 0,
          stock: 0
        },
        products: []
      };
    },
    async created() {
      const response = await api.get('/products');
      this.products = response.data;
    },
    methods: {
      async addProduct() {
        try {
          await api.post('/products', this.newProduct);
          this.products.push(this.newProduct); 
          this.newProduct = { name: '', code: '', category: '', price: 0, stock: 0 }; // Limpa o formulário
          this.showAddProductForm = false; 
        } catch (error) {
          console.error('Erro ao adicionar produto', error);
        }
      },
      editProduct(product) {        
        console.log('Editando produto', product);
      },
      async deleteProduct(id) {
        try {
          await api.delete(`/products/${id}`);
          this.products = this.products.filter(product => product.id !== id); 
        } catch (error) {
          console.error('Erro ao excluir produto', error);
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .product-management {
    text-align: center;
  }
  
  button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    cursor: pointer;
  }
  
  button:hover {
    background-color: #0056b3;
  }
  
  table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
  }
  
  table th, table td {
    padding: 10px;
    border: 1px solid #ddd;
  }
  </style>
  