<template>
  <div class="container">
    <h2>Gerenciamento de Produtos</h2>

    <div v-if="errorMessage" class="error-message">
      <p>{{ errorMessage }}</p>
    </div>
    <div v-if="!errorMessage">
      <button v-if="userStore.user.role !== 'user'" @click="toggleAddProductForm">
        {{ showAddProductForm ? 'Cancelar' : 'Adicionar Produto' }}
      </button>

      <div class="search-filters">
        <input v-model="filters.name" @input="searchProducts" placeholder="Buscar por nome"
          aria-label="Buscar por nome" />
        <input v-model="filters.code" @input="searchProducts" placeholder="Buscar por código"
          aria-label="Buscar por código" />
      </div>

      <div class="wrap-form" v-if="showAddProductForm">
        <h3>{{ editingProduct ? 'Editar Produto' : 'Cadastrar Novo Produto' }}</h3>
        <form @submit.prevent="editingProduct ? updateProduct() : addProduct()">
          <div>
            <label for="code">Código:</label>
            <input name="code" v-model="newProduct.code" type="text" id="code" required />
          </div>
          <div>
            <label for="name">Nome:</label>
            <input name="name" v-model="newProduct.name" type="text" id="name" required />
          </div>
          <div>
            <label for="description">Descrição:</label>
            <input name="description" v-model="newProduct.description" type="text" id="description" required />
          </div>
          <div>
            <label for="expiration_date">Data de validade:</label>
            <input name="expiration_date" v-model="newProduct.expiration_date" type="date" id="expiration_date"
              required />
          </div>
          <div>
            <label for="category">Categoria:</label>
            <select v-model="newProduct.category_id" id="category" required>
              <option value="" disabled>Selecione uma categoria</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div>
            <label for="supplier">Fornecedor:</label>
            <select v-model="newProduct.supplier_id" id="supplier" required>
              <option value="" disabled>Selecione um fornecedor</option>
              <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                {{ supplier.name }}
              </option>
            </select>
          </div>
          <div>
            <label for="price">Preço de Custo:</label>
            <input name="cost_price" v-model="newProduct.cost_price" @input="handleInputReal('cost_price', $event)"
              type="text" id="price" required />
          </div>
          <div>
            <label for="sale_price">Preço de Venda:</label>
            <input name="sale_price" v-model="newProduct.sale_price" @input="handleInputReal('sale_price', $event)"
              type="text" id="sale_price" required />
          </div>
          <div>
            <label for="stock">Estoque:</label>
            <input name="stock" v-model="newProduct.stock" @input="validateQuantity" type="number" id="stock"
              required />
          </div>
          <div>
            <label for="min_stock">Estoque Mínimo:</label>
            <input name="min_stock" v-model="newProduct.min_stock" @input="validateQuantity" type="number"
              id="min_stock" required />
          </div>
          <button type="submit">
            {{ editingProduct ? 'Salvar alterações' : 'Salvar' }}
          </button>
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
            <th v-if="userStore.user.role !== 'user'">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in paginatedProducts" :key="product.id">
            <td>{{ product.name }}</td>
            <td>{{ product.code }}</td>
            <td>{{ formatToReal(product.cost_price) }}</td>
            <td>{{ product.stock }}</td>
            <td v-if="userStore.user.role !== 'user'">
              <button @click="editProduct(product)" aria-label="Editar produto">
                Editar
              </button>
              <button @click="deleteProduct(product.id)" aria-label="Excluir produto">
                Excluir
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="paginatedProducts.length > 0" class="pagination">
        <button :disabled="currentPage === 1" @click="currentPage--">Anterior</button>
        <span>Página {{ currentPage }} de {{ totalPages }}</span>
        <button :disabled="currentPage === totalPages" @click="currentPage++">Próxima</button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api';
import { useUserStore } from '../stores/user';

export default {
  setup() {
    const userStore = useUserStore();
    return { userStore };
  },
  data() {
    return {
      errorMessage: "",
      showAddProductForm: false,
      editingProduct: null,
      newProduct: this.getDefaultProduct(),
      products: [],
      categories: [],
      suppliers: [],
      filters: {
        name: '',
        code: ''
      },
      currentPage: 1,
      itemsPerPage: 5
    };
  },
  computed: {
    filteredProducts() {
      return this.products.filter(product =>
        product.name.toLowerCase().includes(this.filters.name.toLowerCase()) &&
        product.code.toLowerCase().includes(this.filters.code.toLowerCase())
      );
    },
    paginatedProducts() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.products.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.products.length / this.itemsPerPage);
    }
  },
  async created() {
    await this.searchProducts();
    await this.fetchCategories();
    await this.fetchSuppliers();
  },
  methods: {
    getDefaultProduct() {
      return {
        name: '',
        code: '',
        category_id: '',
        supplier_id: '',
        cost_price: 0,
        sale_price: 0,
        stock: 0,
        min_stock: 0,
        expiration_date: '',
        description: ''
      };
    },
    toggleAddProductForm() {
      this.showAddProductForm = !this.showAddProductForm;
      if (!this.showAddProductForm) this.resetForm();
    },
    async searchProducts() {
      try {
        const response = await api.get('/products', { params: this.filters });
        this.products = response.data;
        this.currentPage = 1;
      } catch (error) {
        this.errorMessage = "Erro ao buscar produtos. Tente novamente mais tarde.";
        console.error('Erro ao buscar produtos', error);
      }
    },
    async addProduct() {
      try {
        const formattedProduct = {
          ...this.newProduct,
          cost_price: this.newProduct.cost_price == '' ? 0 : this.stripCurrency(this.newProduct.cost_price),
          sale_price: this.newProduct.sale_price == '' ? 0 : this.stripCurrency(this.newProduct.sale_price)
        };
        const response = await api.post('/products', formattedProduct);
        this.$swal('Produto cadastrado com sucesso!');
        this.products.push(response.data);
        this.resetForm();
      } catch (error) {
        this.errorMessage = "Erro ao adicionar produtos. Tente novamente mais tarde.";
        console.error('Erro ao adicionar produto', error);
      }
    },
    editProduct(product) {
      const formattedProduct = {
        ...product,
        cost_price: this.formatToReal(product.cost_price),
        sale_price: this.formatToReal(product.sale_price)
      };
      this.editingProduct = formattedProduct;
      this.newProduct = { ...formattedProduct };
      this.showAddProductForm = true;
    },
    async updateProduct() {
      try {
        const formattedProduct = {
          ...this.newProduct,
          cost_price: this.newProduct.cost_price == '' ? 0 : this.stripCurrency(this.newProduct.cost_price),
          sale_price: this.newProduct.sale_price == '' ? 0 : this.stripCurrency(this.newProduct.sale_price)
        };
        const response = await api.put(`/products/${this.editingProduct.id}`, formattedProduct);
        const index = this.products.findIndex(p => p.id === this.editingProduct.id);
        this.$swal('Produto atualizado com sucesso!');

        if (index !== -1) this.products[index] = response.data;
        this.resetForm();
      } catch (error) {
        this.errorMessage = "Erro ao atualizar produtos. Tente novamente mais tarde.";
        console.error('Erro ao atualizar produto', error);
      }
    },
    async deleteProduct(productId) {      
      try {
        const result = await this.$swal({
          title: 'Tem certeza?',
          text: 'Esta ação não pode ser desfeita.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sim, excluir!',
          cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
          await api.delete(`/products/${productId}`);
          await this.$swal('Excluído!', 'O produto foi excluída com sucesso.', 'success');
          this.searchProducts();
        }
      } catch (error) {
        await this.$swal('Erro!', 'Não foi possível excluir o produto. Tente novamente mais tarde.', 'error');
        console.error('Erro ao excluir produto', error);
      }
    },
    resetForm() {
      this.newProduct = this.getDefaultProduct();
      this.editingProduct = null;
      this.errorMessage = "";
    },
    async fetchCategories() {
      try {
        const response = await api.get('/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Erro ao buscar categorias', error);
      }
    },
    async fetchSuppliers() {
      try {
        const response = await api.get('/suppliers');
        this.suppliers = response.data;
      } catch (error) {
        console.error('Erro ao buscar fornecedores', error);
      }
    },
    formatToReal(value) {
      if (!value) return 'R$ 0,00';

      let onlyNumbers = value.toString().replace(/[^\d]/g, '');

      let numericValue = parseFloat(onlyNumbers) / 100;

      return numericValue.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
      });
    },
    handleInputReal(field, event) {
      const formatted = this.formatToReal(event.target.value);
      this.newProduct[field] = formatted;
    },
    stripCurrency(value) {
      let cleanedValue = value.replace(/[^\d,.-]/g, '');
      cleanedValue = cleanedValue.replace(',', 'temp')
        .replace(/\./g, '')
        .replace('temp', '.');

      const parsedValue = parseFloat(cleanedValue);
      return isNaN(parsedValue) ? 0 : parsedValue;
    },
    validateQuantity() {
      if (this.newProduct.stock < 0) {
        this.$swal('A quantidade de estoque não pode ser negativa.');
        this.newProduct.stock = 0;
      } else if (this.newProduct.min_stock < 0) {
        this.$swal('A quantidade mínima de estoque não pode ser negativa.');
        this.newProduct.min_stock = 0;
      }
    },
  }
};
</script>

<style scoped>
.search-filters {
  display: flex;
  justify-content: center;
  margin: 20px 0;
  gap: 10px;
}

.search-filters input {
  padding: 10px;
  font-size: 1em;
  border: 1px solid #ddd;
  border-radius: 4px;
  width: 200px;
  transition: border-color 0.3s ease;
}

.search-filters input:focus {
  border-color: #4CAF50;
  outline: none;
}

@media (max-width: 768px) {
  .search-filters {
    flex-direction: column;
    gap: 5px;
  }

  table {
    font-size: 0.9em;
  }

  form {
    width: 90%;
    padding: 15px;
  }
}
</style>