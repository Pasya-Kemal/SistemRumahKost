import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

import LoginView from '../views/LoginView.vue';
import AdminDashboard from '../views/admin/DashboardView.vue';
import OwnerDashboard from '../views/owner/DashboardView.vue';
import TenantDashboard from '../views/tenant/DashboardView.vue';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: LoginView,
        meta: { guestOnly: true }
    },

    {
        path: '/admin/dashboard',
        name: 'AdminDashboard',
        component: () => import('../views/admin/DashboardView.vue'),
        meta: { requiresAuth: true, requiredRole: 'admin' }
    },
    {
        path: '/admin/kamar',
        name: 'AdminKamar',
        component: () => import('../views/admin/KamarView.vue'), 
        meta: { requiresAuth: true, requiredRole: 'admin' }
    },
    {
        path: '/admin/penghuni',
        name: 'AdminPenghuni',
        component: () => import('../views/admin/PenghuniView.vue'), 
        meta: { requiresAuth: true, requiredRole: 'admin' }
    },
    {
        path: '/admin/pembayaran',
        name: 'AdminPembayaran',
        component: () => import('../views/admin/PembayaranView.vue'), 
        meta: { requiresAuth: true, requiredRole: 'admin' }
    },



    {
        path: '/owner/dashboard',
        name: 'OwnerDashboard',
        component: () => import('../views/owner/DashboardView.vue'), 
        meta: { requiresAuth: true, requiredRole: 'owner' }
    },
    {
        path: '/owner/kamar',
        name: 'OwnerKamar',
        component: () => import('../views/owner/KamarView.vue'), 
        meta: { requiresAuth: true, requiredRole: 'owner' }
    },
    {
        path: '/owner/laporan',
        name: 'OwnerLaporan',
        component: () => import('../views/owner/LaporanView.vue'), 
        meta: { requiresAuth: true, requiredRole: 'owner' }
    },



    {
        path: '/dashboard',
        name: 'TenantDashboard',
        component: () => import('../views/tenant/DashboardView.vue'), 
        meta: { requiresAuth: true, requiredRole: 'tenants' }
    },

    


    {
        path: '/pengaturan',
        name: 'PengaturanPusat',
        component: () => import('../views/shared/PengaturanView.vue'), 
        meta: { requiresAuth: true } 
    },
    {
        path: '/pengaturan/edit-profile',
        name: 'EditProfile',
        component: () => import('../views/shared/EditProfileView.vue'), 
        meta: { requiresAuth: true }
    },
    {
        path: '/pengaturan/ubah-password',
        name: 'UbahPassword',
        component: () => import('../views/shared/UbahPasswordView.vue'), 
        meta: { requiresAuth: true }
    },
    {
        path: '/pengaturan/appearance',
        name: 'AppearanceSetting',
        component: () => import('../views/shared/AppearanceView.vue'), 
        meta: { requiresAuth: true }
    },
    {
        path: '/pengaturan/security',
        name: 'AccountSecurity',
        component: () => import('../views/shared/AccountSecurityView.vue'), 
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;
    const userRole = authStore.role;

    if (to.meta.requiresAuth && !isAuthenticated) {
        return next('/login');
    }

    if (to.meta.requiredRole && to.meta.requiredRole !== userRole) {
        alert('Akses Ditolak! Anda tidak memiliki hak akses ke halaman ini.');

        if (userRole === 'admin') return next('/admin/dashboard');
        if (userRole === 'owner') return next('/owner/dashboard');
        return next('/dashboard');
    }

    if (to.meta.guestOnly && isAuthenticated) {
        if (userRole === 'admin') return next('/admin/dashboard');
        if (userRole === 'owner') return next('/owner/dashboard');
        return next('/dashboard');
    }

    next();
});

export default router;