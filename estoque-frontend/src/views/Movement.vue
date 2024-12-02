<template>
    <div class="container">
        <h2>Gerenciamento de Estoque</h2>

        <div v-if="errorMessage" class="error-message">
            <p>{{ errorMessage }}</p>
        </div>
        <div v-if="!errorMessage">
            <button @click="toggleAddMovementForm">
                {{ showAddMovementForm ? 'Cancelar' : 'Adicionar Movimento' }}
            </button>

            <div class="wrap-form" v-if="showAddMovementForm">
                <form @submit.prevent="movement.id ? updateStockMovement() : registerStockMovement()">
                    <div>
                        <label for="product_id">Produto:</label>
                        <select id="product" v-model="movement.product_id" required>
                            <option v-for="product in products" :key="product.id" :value="product.id">
                                {{ product.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="stock">Estoque Atual:</label>
                        <input name="current_stock" type="number" :value="currentProductStock" disabled />
                    </div>

                    <div>
                        <label for="type">Tipo de Movimentação:</label>
                        <select id="moviment_type" v-model="movement.type" required>
                            <option value="entry">Entrada</option>
                            <option v-if="userStore.user.role !== 'user'" value="devolution">Devolução</option>
                            <option value="exit">Saída</option>
                            <option v-if="userStore.user.role !== 'user'" value="loss">Perda</option>
                        </select>
                    </div>

                    <div>
                        <label for="quantity">Quantidade:</label>
                        <input name="quantity" v-model="movement.quantity" @input="validateQuantity" type="number"
                            required />
                    </div>

                    <div>
                        <label for="price">Preço:</label>
                        <input name="price" v-model="movement.price" @input="handleInputReal('price', $event)"
                            type="text" required />
                    </div>

                    <div>
                        <label for="reason">Motivo:</label>
                        <input name="reason" v-model="movement.reason" type="text" required />
                    </div>

                    <button type="submit">{{ movement.id ? 'Atualizar Movimentação' : 'Registrar Movimentação'
                        }}</button>
                </form>
            </div>
            <br>
            <h3>Movimentações Recentes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Motivo</th>
                        <th>Usuário</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="movement in paginatedMovements" :key="movement.id">
                        <td>{{ movement.product.name }}</td>
                        <td>
                            <span v-if="movement.type === 'entry'">Entrada</span>
                            <span v-if="movement.type === 'devolution'">Devolução</span>
                            <span v-if="movement.type === 'exit'">Saída</span>
                            <span v-if="movement.type === 'loss'">Perda</span>
                            <span v-if="movement.type === 'manual'">Manual</span>
                        </td>
                        <td>{{ movement.quantity }}</td>
                        <td>{{ formatToReal(movement.price) }}</td>
                        <td>{{ movement.reason }}</td>
                        <td>{{ movement.user.name }}</td>
                        <td>{{ movement.created_at }}</td>
                        <td>
                            <button @click="editStockMovement(movement)">Editar</button>
                            <button @click="deleteStockMovement(movement.id)">Excluir</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="paginatedMovements.length > 0" class="pagination">
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
            showAddMovementForm: false,
            products: [],
            movements: [],
            movement: {
                id: null,
                product_id: '',
                type: 'entry',
                quantity: 0,
                price: 0,
                reason: ''
            },
            currentPage: 1,
            itemsPerPage: 5
        };
    },
    computed: {
        currentProductStock() {
            const selectedProduct = this.products.find(
                (product) => product.id === this.movement.product_id
            );
            return selectedProduct ? selectedProduct.stock : 0;
        },
        paginatedMovements() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.movements.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.movements.length / this.itemsPerPage);
        }
    },
    async created() {
        await this.fetchProducts();
        await this.fetchMovements();
    },
    methods: {
        validateQuantity() {
            if (this.movement.quantity < 0) {
                this.$swal('A quantidade não pode ser negativa.');
                this.movement.quantity = 0;
            } else if (this.movement.type === 'exit' || this.movement.type === 'loss') {
                const currentStock = this.currentProductStock;
                if (this.movement.quantity > currentStock) {
                    this.$swal(`A quantidade não pode ser maior que o estoque atual (${currentStock}).`);
                    this.movement.quantity = currentStock;
                }
            }
        },
        toggleAddMovementForm() {
            this.showAddMovementForm = !this.showAddMovementForm;
            if (!this.showAddMovementForm) this.resetForm();
        },
        async fetchProducts() {
            try {
                const response = await api.get('/products');
                this.products = response.data;
            } catch (error) {
                this.errorMessage = 'Erro ao carregar produtos. Tente novamente mais tarde.';
                console.error('Erro ao carregar produtos', error);
            }
        },
        async fetchMovements() {
            try {
                const response = await api.get('/movements');
                this.movements = response.data;
                this.currentPage = 1;
            } catch (error) {
                this.errorMessage = 'Erro ao carregar movimentações. Tente novamente mais tarde.';
                console.error('Erro ao carregar movimentações', error);
            }
        },
        async registerStockMovement() {
            try {
                if (this.movement.type === 'exit' && this.movement.quantity > this.currentProductStock) {
                    this.$swal('A quantidade de saída não pode ser maior que o estoque disponível.');
                    return;
                }
                const formattedMovement = {
                    ...this.movement,
                    price: this.movement.price == '' ? 0 : this.stripCurrency(this.movement.price)
                };
                const response = await api.post('/movements', formattedMovement);
                this.$swal('Movimentação cadastrada com sucesso!');
                await this.fetchMovements();
                await this.fetchProducts(); 
                this.resetForm();
            } catch (error) {
                if (error.response) {
                    this.errorMessage = error.response.data.message || 'Erro ao adicionar movimentação. Tente novamente mais tarde.';
                } else {
                    this.errorMessage = 'Erro de conexão ou desconhecido';
                }
                console.error('Erro ao adicionar movimentações', error);
            }
        },
        async updateStockMovement() {
            try {
                if (this.movement.type === 'exit' && this.movement.quantity > this.currentProductStock) {
                    this.$swal('A quantidade de saída não pode ser maior que o estoque disponível.');
                    return;
                }
                const formattedMovement = {
                    ...this.movement,
                    price: this.movement.price == '' ? 0 : this.stripCurrency(this.movement.price)
                };
                const response = await api.put(`/movements/${formattedMovement.id}`, formattedMovement);
                this.$swal('Movimentação atualizada com sucesso!');
                await this.fetchMovements();
                await this.fetchProducts();
                this.resetForm();
            } catch (error) {
                if (error.response) {
                    this.errorMessage = error.response.data.message || 'Erro ao atualizar movimentação. Tente novamente mais tarde.';
                } else {
                    this.errorMessage = 'Erro de conexão ou desconhecido';
                }
                console.error('Erro ao atualizar movimentação', error);
            }
        },
        async deleteStockMovement(movementId) {
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
                    await api.delete(`/movements/${movementId}`);
                    await this.$swal('Excluído!', 'A movimentação foi excluída com sucesso.', 'success');
                    this.fetchMovements();
                    this.fetchProducts();
                }
            } catch (error) {
                await this.$swal('Erro!', 'Não foi possível excluir a movimentação. Tente novamente mais tarde.', 'error');
                console.error('Erro ao excluir movimentação', error);
            }
        },
        editStockMovement(movement) {
            this.movement = { ...movement, price: this.formatToReal(movement.price) };
            this.showAddMovementForm = true;
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
            this.movement[field] = formatted;
        },
        stripCurrency(value) {
            let cleanedValue = value.replace(/[^\d,.-]/g, '');
            cleanedValue = cleanedValue.replace(',', 'temp')
                .replace(/\./g, '')
                .replace('temp', '.');

            const parsedValue = parseFloat(cleanedValue);
            return isNaN(parsedValue) ? 0 : parsedValue;
        },
        resetForm() {
            this.errorMessage = "";
            this.movement = {
                id: null,
                product_id: '',
                type: 'entry',
                quantity: 0,
                price: 0,
                reason: ''
            };
        }
    }
};
</script>
