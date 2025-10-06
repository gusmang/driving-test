import { createRouter, createWebHistory } from 'vue-router';
import DashboardLayout from '@/pages/layouts/DashboardLayout.vue';
import DashboardHome from '@/pages/views/home.vue';
import UserList from '@/pages/views/Users.vue';
import Login from '@/pages/Login.vue';
import Register from '@/pages/Register.vue';
import ForgotPassword from '@/pages/ForgotPassword.vue';
import ResetPassword from '@/pages/ResetPassword.vue';

const routes = [
  // Auth routes (login/register) terpisah
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/forgot-password', component: ForgotPassword },
  { path: '/reset-password', component: ResetPassword },
  // Dashboard route, butuh auth
  {
    path: '/dashboard',
    component: DashboardLayout,
    meta: { requiresAuth: true }, // guard
    children: [
      { path: '', name: 'DashboardHome', component: DashboardHome },
      { path: 'users', name: 'UserList', component: UserList }
    ]
  },

  // Redirect root ke login
  { path: '/', redirect: '/login' }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    if (to.meta.requiresAuth && !token) {
      return next({ name: 'Login' });
    }
    next();
  });
  
  export default router;