import { createRouter, createWebHistory } from 'vue-router';  
import HomePage from '../views/HomePage.vue';
import LoginPage from '../views/LoginPage.vue';
import User from '../views/User.vue';
import Product from '../views/Product.vue';
import Movement from '@/views/Movement.vue';
import Supplier from '@/views/Supplier.vue';
import Category from '@/views/Category.vue';
import StockSummary from '@/views/StockSummary.vue';

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
    path: '/user',
    name: 'user',
    component: User,
  },
  {
    path: '/product',
    name: 'product',
    component: Product,
  },
  {
    path: '/movement',
    name: 'movement',
    component: Movement,
  },
  {
    path: '/supplier',
    name: 'supplier',
    component: Supplier,
  },
  {
    path: '/category',
    name: 'category',
    component: Category,
  },
  {
    path: '/stock-summary',
    name: 'stock-summary',
    component: StockSummary,
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL), 
  routes,  
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('token'); 

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login'); 
  } else {
    next();
  }
});

export default router;  
