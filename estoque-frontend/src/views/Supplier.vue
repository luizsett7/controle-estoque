<template>
  <div class="container">
    <h2>Gerenciamento de Fornecedores</h2>

    <div v-if="errorMessage" class="error-message">
      <p>{{ errorMessage }}</p>
    </div>
    <div v-if="!errorMessage">
      <form v-if="userStore.user.role !== 'user'" @submit.prevent="editMode ? updateSupplier() : addSupplier()">
        <div>
          <label for="name">Nome:</label>
          <input v-model="currentSupplier.name" type="text" required />
        </div>
        <div>
          <label for="cnpj">CNPJ:</label>
          <input v-model="currentSupplier.cnpj" @input="formatCNPJ" maxlength="18" placeholder="00.000.000/0000-00"
            type="text" required />
        </div>
        <div>
          <label for="contact">Contato:</label>
          <input v-model="currentSupplier.contact" type="text" required />
        </div>
        <button type="submit">{{ editMode ? "Atualizar" : "Cadastrar" }}</button>
      </form>

      <h3>Fornecedores Cadastrados</h3>
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>CNPJ</th>
            <th>Contato</th>
            <th v-if="userStore.user.role !== 'user'">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="supplier in paginatedSuppliers" :key="supplier.id">
            <td>{{ supplier.name }}</td>
            <td>{{ formatCNPJDisplay(supplier.cnpj) }}</td>
            <td>{{ supplier.contact }}</td>
            <td v-if="userStore.user.role !== 'user'">
              <button @click="prepareEdit(supplier)">Editar</button>
              <button @click="deleteSupplier(supplier.id)">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="paginatedSuppliers.length > 0" class="pagination">
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
      errorMessage: "",
      suppliers: [],
      currentSupplier: {
        id: null,
        name: "",
        cnpj: "",
        contact: ""
      },
      editMode: false,
      currentPage: 1,
      itemsPerPage: 5
    };
  },
  async created() {
    await this.fetchSuppliers();
  },
  computed: {   
    paginatedSuppliers() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.suppliers.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.suppliers.length / this.itemsPerPage);
    }
  },
  methods: {
    async fetchSuppliers() {
      try {
        const response = await api.get("/suppliers");
        this.suppliers = response.data;
        this.currentPage = 1;
        console.log("Fornecedores recebidos:", response.data);
      } catch (error) {
        this.errorMessage = "Erro ao buscar fornecedores. Tente novamente mais tarde.";
        console.error("Erro ao buscar fornecedores:", error);
      }
    },

    async addSupplier() {
      try {
        const response = await api.post("/suppliers", this.currentSupplier);
        this.$swal('Fornecedor cadastrado com sucesso!');
        this.suppliers.push(response.data.supplier);
        this.resetForm();
      } catch (error) {
        this.errorMessage = "Erro ao cadastrar fornecedor. Tente novamente mais tarde.";
        console.error("Erro ao cadastrar fornecedor:", error);
      }
    },

    prepareEdit(supplier) {
      if (!supplier || !supplier.id) {
        console.error("Fornecedor inválido para edição:", supplier);
        return;
      }
      this.currentSupplier = { ...supplier };
      this.editMode = true;
    },

    async updateSupplier() {
      try {
        if (!this.currentSupplier.id) {
          console.error("ID do fornecedor não encontrado:", this.currentSupplier);
          return;
        }
        const response = await api.put(`/suppliers/${this.currentSupplier.id}`, this.currentSupplier);
        const index = this.suppliers.findIndex(supplier => supplier.id === this.currentSupplier.id);
        this.$swal('Fornecedor atualizado com sucesso!');

        if (index !== -1) {
          this.suppliers[index] = response.data;
        }
        this.resetForm();
      } catch (error) {
        this.errorMessage = "Erro ao atualizar fornecedor. Tente novamente mais tarde.";
        console.error("Erro ao atualizar fornecedor:", error);
      }
    },

    async deleteSupplier(id) {      
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
          await api.delete(`/suppliers/${id}`);
          await this.$swal('Excluído!', 'O fornecedor foi excluído com sucesso.', 'success');
          this.fetchSuppliers();
        }
      } catch (error) {
        await this.$swal('Erro!', 'Não foi possível excluir o fornecedor. Tente novamente mais tarde.', 'error');
        console.error('Erro ao excluir fornecedor', error);
      }
    },
    formatCNPJ() {
      let value = this.currentSupplier.cnpj.replace(/\D/g, "");
      value = value.replace(/^(\d{2})(\d)/, "$1.$2");
      value = value.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
      value = value.replace(/\.(\d{3})(\d)/, ".$1/$2");
      value = value.replace(/(\d{4})(\d)/, "$1-$2");
      this.currentSupplier.cnpj = value;
    },

    formatCNPJDisplay(cnpj) {
      if (!cnpj) return "";
      let value = cnpj.replace(/\D/g, "");
      value = value.replace(/^(\d{2})(\d)/, "$1.$2");
      value = value.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
      value = value.replace(/\.(\d{3})(\d)/, ".$1/$2");
      value = value.replace(/(\d{4})(\d)/, "$1-$2");
      return value;
    },
    resetForm() {
      this.errorMessage = "";
      this.currentSupplier = {
        id: null,
        name: "",
        cnpj: "",
        contact: ""
      };
      this.editMode = false;
    }
  }
};
</script>
