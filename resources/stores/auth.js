import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('kn_user')) || null,
        token: localStorage.getItem('kn_token') || null,
        role: localStorage.getItem('kn_role') || null, 
    }),
    
    getters: {
        isAuthenticated: (state) => !!state.token,
        isRole: (state) => (roleKey) => state.role === roleKey,
    },
    
    actions: {
        loginSuccess(userData, userRole, apiToken) {
            this.user = userData;
            this.role = userRole; 
            this.token = apiToken;

            localStorage.setItem('kn_user', JSON.stringify(userData));
            localStorage.setItem('kn_role', userRole);
            localStorage.setItem('kn_token', apiToken);
        },

        logout() {
            this.user = null;
            this.role = null;
            this.token = null;
            
            localStorage.removeItem('kn_user');
            localStorage.removeItem('kn_role');
            localStorage.removeItem('kn_token');
        }
    }
});