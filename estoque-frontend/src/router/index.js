import { createRouter, createWebHistory } from 'vue-router';  // Vue 3
import HomePage from '../views/HomePage.vue';
import LoginPage from '../views/LoginPage.vue';
import RegisterPage from '../views/RegisterPage.vue';
import ProductManagement from '../views/ProductManagement.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomePage,
  },
  {
    path: '/login',
    name: 'login',
    component: LoginPage,
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterPage,
  },
  {
    path: '/product-management',
    name: 'product-management',
    component: ProductManagement,
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL), 
  routes,  
});

export default router;  
