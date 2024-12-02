<template>
  <div class="container">
    <h2>Gerenciamento de Usuários</h2>

    <div v-if="errorMessage" class="error-message">
      <p v-if="userStore.user.role === 'user'">
        Você não tem permissão para acessar está página
      </p>
      <p v-else>{{ errorMessage }}</p>
    </div>
    <div v-if="!errorMessage">
      <button @click="toggleAddUserForm">
        {{ showAddUserForm ? 'Cancelar' : 'Adicionar Usuário' }}
      </button>

      <div class="wrap-form" v-if="showAddUserForm">
        <h3>{{ editMode ? 'Editar Usuário' : 'Adicionar Usuário' }}</h3>
        <form @submit.prevent="editMode ? updateUser() : addUser()">
          <div>
            <label for="name">Nome:</label>
            <input v-model="form.name" type="text" required />
          </div>
          <div>
            <label for="phone">Celular:</label>
            <input v-model="form.phone" type="text" />
          </div>
          <div>
            <label for="email">E-mail:</label>
            <input v-model="form.email" type="email" required />
            <span class="error" v-if="fieldErrors.email">{{ fieldErrors.email[0] }}</span>
          </div>
          <div>
            <label for="role">Papel:</label>
            <select v-model="form.role" required>
              <option value="admin">Administrador</option>
              <option value="manager">Gerente</option>
              <option value="user">Usuário</option>
            </select>
          </div>
          <div v-if="!editMode">
            <label for="password">Senha:</label>
            <input v-model="form.password" type="password" required />
            <span class="error" v-if="fieldErrors.password">{{ fieldErrors.password[0] }}</span>
          </div>
          <div v-if="!editMode">
            <label for="password_confirmation">Repita sua senha:</label>
            <input v-model="form.password_confirmation" type="password" required />
          </div>
          <button type="submit">{{ editMode ? 'Salvar Alterações' : 'Cadastrar' }}</button>
        </form>
      </div>
      <br>
      <h3>Usuários Cadastrados</h3>
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>Celular</th>
            <th>E-mail</th>
            <th>Papel</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in paginatedUsers" :key="user.id">
            <td>{{ user.name }}</td>
            <td>{{ user.phone }}</td>
            <td>{{ user.email }}</td>
            <td>
              <span v-if="user.role === 'admin'">Administrador</span>
              <span v-if="user.role === 'manager'">Gerente</span>
              <span v-if="user.role === 'user'">Usuário Comum</span>
            </td>
            <td>
              <button
                v-if="userStore.user.id !== user.id && userStore.isAuthorized && (userStore.user.role === 'admin' || (userStore.user.role === 'manager' && user.role !== 'admin'))"
                @click="prepareEdit(user)">Editar</button>
              <button
                v-if="userStore.user.id !== user.id && userStore.isAuthorized && (userStore.user.role === 'admin' || (userStore.user.role === 'manager' && user.role !== 'admin'))"
                @click="deleteUser(user.id)">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="paginatedUsers.length > 0" class="pagination">
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
    const userStore = useUserStore()

    return { userStore }
  },
  data() {
    return {
      errorMessage: "",
      fieldErrors: {},
      users: [],
      showAddUserForm: false,
      editMode: false,
      form: {
        id: null,
        name: '',
        phone: '',
        email: '',
        role: 'user',
        password: '',
        password_confirmation: ''
      },
      currentPage: 1,
      itemsPerPage: 5
    };
  },
  async created() {
    await this.fetchUsers();
  },
  computed: {
    paginatedUsers() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.users.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.users.length / this.itemsPerPage);
    }
  },
  methods: {
    async fetchUsers() {
      try {
        const response = await api.get('/users');
        this.users = response.data;
        this.currentPage = 1;
      } catch (error) {
        this.errorMessage = "Erro ao buscar usuários. Tente novamente mais tarde.";
        console.error('Erro ao buscar usuários', error);
      }
    },
    toggleAddUserForm() {
      this.showAddUserForm = !this.showAddUserForm;
      if (!this.showAddUserForm) {
        this.resetForm();
      }
    },
    resetForm() {
      this.errorMessage = "";
      this.form = {
        name: '',
        phone: '',
        email: '',
        role: 'user',
        password: '',
        password_confirmation: ''
      };
      this.editMode = false;
    },
    async addUser() {
      try {
        const response = await api.post('/users', this.form);
        this.$swal('Usuário cadastrado com sucesso!');
        this.users.push(response.data);
        this.toggleAddUserForm();
        this.fieldErrors = {};
        this.errorMessage = "";
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.fieldErrors = error.response.data.errors;
        } else {
          this.errorMessage = "Erro ao adicionar usuário. Tente novamente mais tarde.";
        }
        console.error('Erro ao adicionar usuário', error);
      }
    },
    prepareEdit(user) {
      this.form = { ...user, password: '', password_confirmation: '' };
      this.editMode = true;
      this.showAddUserForm = true;
    },
    async updateUser() {
      try {
        const payload = {
          name: this.form.name,
          phone: this.form.phone,
          email: this.form.email,
          password: this.form.password,
          role: this.form.role,
        };

        this.fieldErrors = {};

        const response = await api.put(`/users/${this.form.id}`, payload);
        this.$swal('Usuário atualizado com sucesso!');
        const index = this.users.findIndex(u => u.id === this.form.id);

        if (index !== -1) {
          this.users[index] = response.data;
        }

        this.toggleAddUserForm();
      } catch (error) {
        if (error.response) {
            if (error.response.status === 403) {
                this.$swal('Erro', error.response.data.error, 'error');
            } else if (error.response.status === 422) {
                this.fieldErrors = error.response.data.errors;
            } else {
                this.errorMessage = "Erro ao atualizar usuário. Tente novamente mais tarde.";
            }
        } else {
            this.errorMessage = "Erro ao atualizar usuário. Tente novamente mais tarde.";
        }
        console.error('Erro ao atualizar usuário', error);
      }
    },
    async deleteUser(id) {
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
          await api.delete(`/users/${id}`);
          await this.$swal('Excluído!', 'O usuário foi excluído com sucesso.', 'success');
          this.fetchUsers();
        }
      } catch (error) {
        await this.$swal('Erro!', 'Não foi possível excluir o usuário. Tente novamente mais tarde.', 'error');
        console.error('Erro ao excluir usuário', error);
      }
    },
  },
};
</script>
