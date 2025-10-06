import { createRouter, createWebHistory } from 'vue-router';
import DashboardLayout from '@/pages/layouts/DashboardLayout.vue';
import DashboardHome from '@/pages/views/home.vue';
import UserList from '@/pages/views/Users.vue';
import Login from '@/pages/Login.vue';
import Swagger from '@/pages/Swagger.vue';
import Register from '@/pages/Register.vue';
import ForgotPassword from '@/pages/ForgotPassword.vue';
import ResetPassword from '@/pages/ResetPassword.vue';

const routes = [
  // Docs API (tidak butuh auth)
//   {
//     path: '/docs/api',
//     name: 'ApiDocs',
//     beforeEnter() {
//       window.location.href = '/docs/api';
//     }
//   },

  // Auth routes
  { path: '/login', name: 'Login', component: Login },
  { path: '/swagger', name: 'Swagger', component: Swagger },
  { path: '/register', name: 'Register', component: Register },
  { path: '/forgot-password', name: 'ForgotPassword', component: ForgotPassword },
  { path: '/reset-password', name: 'ResetPassword', component: ResetPassword },

  // Dashboard (butuh auth)
  {
    path: '/dashboard',
    component: DashboardLayout,
    meta: { requiresAuth: true }, // guard
    children: [
      { path: '', name: 'DashboardHome', component: DashboardHome },
      { path: 'users', name: 'UserList', component: UserList }
    ]
  },

  // Root redirect
  { path: '/', redirect: '/login' },

  // Catch-all 404 (letakkan paling akhir)
  //{ path: '/:catchAll(.*)', redirect: '/login' }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Global auth guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');

  // Hanya cek auth untuk route yang butuh
  if (to.meta.requiresAuth && !token) {
    return next({ name: 'Login' });
  }

  next();
});

export default router;