<template>
  <div class="container">
    <h2>Gerenciamento de Categorias</h2>

    <div v-if="errorMessage" class="error-message">
      <p>{{ errorMessage }}</p>
    </div>
    <div v-if="!errorMessage">
      <form v-if="userStore.user.role !== 'user'" @submit.prevent="editMode ? updateCategory() : addCategory()">
        <div>
          <label for="name">Nome da Categoria:</label>
          <input name="name" v-model="currentCategory.name" type="text" required />
        </div>
        <button type="submit">{{ editMode ? "Atualizar" : "Cadastrar" }}</button>
      </form>

      <h3>Categorias Cadastradas</h3>
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th v-if="userStore.user.role !== 'user'">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in paginatedCategories" :key="category.id">
            <td>{{ category.name }}</td>
            <td v-if="userStore.user.role !== 'user'">
              <button @click="prepareEdit(category)">Editar</button>
              <button @click="deleteCategory(category.id)">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="paginatedCategories.length > 0" class="pagination">
        <button :disabled="currentPage === 1" @click="currentPage--">Anterior</button>
        <span>Página {{ currentPage }} de {{ totalPages }}</span>
        <button :disabled="currentPage === totalPages" @click="currentPage++">Próxima</button>
      </div>
    </div>
  </div>
</template>

<script>
import api from "../services/api";
import { useUserStore } from '../stores/user';

export default {
  setup() {
    const userStore = useUserStore();
    return { userStore };
  },
  data() {
    return {
      categories: [],
      currentCategory: { id: null, name: "" },
      editMode: false,
      errorMessage: "",
      currentPage: 1,
      itemsPerPage: 5
    };
  },
  computed: {
    paginatedCategories() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.categories.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.categories.length / this.itemsPerPage);
    }
  },
  async created() {
    await this.fetchCategories();
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await api.get("/categories");
        this.categories = response.data;
        this.currentPage = 1;
      } catch (error) {
        this.errorMessage = "Erro ao carregar as categorias. Tente novamente mais tarde.";
        console.error("Erro ao buscar categorias:", error);
      }
    },

    async addCategory() {
      try {
        const response = await api.post("/categories", this.currentCategory);
        this.$swal('Categoria cadastrada com sucesso!');
        this.categories.push(response.data.category);
        this.resetForm();
      } catch (error) {
        this.errorMessage = "Erro ao cadastrar categoria. Verifique os dados e tente novamente.";
        console.error("Erro ao cadastrar categoria:", error);
      }
    },

    prepareEdit(category) {
      this.currentCategory = { ...category };
      this.editMode = true;
    },

    async updateCategory() {
      try {
        const response = await api.put(`/categories/${this.currentCategory.id}`, this.currentCategory);
        const index = this.categories.findIndex((cat) => cat.id === this.currentCategory.id);
        this.$swal('Categoria atualizada com sucesso!');

        if (index !== -1) {
          this.categories[index] = response.data;
        }
        this.resetForm();
      } catch (error) {
        this.errorMessage = "Erro ao atualizar categoria. Tente novamente mais tarde.";
        console.error("Erro ao atualizar categoria:", error);
      }
    },

    async deleteCategory(id) {      
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
          await api.delete(`/categories/${id}`);
          await this.$swal('Excluída!', 'A categoria foi excluída com sucesso.', 'success');
          this.fetchCategories();
        }
      } catch (error) {
        await this.$swal('Erro!', 'Não foi possível excluir a categoria. Tente novamente mais tarde.', 'error');
        console.error('Erro ao excluir categoria', error);
      }
    },

    resetForm() {
      this.currentCategory = { id: null, name: "" };
      this.editMode = false;
      this.errorMessage = "";
    },
  },
};
</script>
