import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import Buyer from '../views/Buyer.vue'
import Login from '../views/Login.vue'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            redirect: '/login'
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/belanja',
            name: 'buyer',
            component: Buyer
        },
        {
            path: '/admin',
            name: 'admin',
            component: Dashboard,
            meta: { role: 'admin' }
        },
    ]
})

router.beforeEach((to) => {
    const user = JSON.parse(sessionStorage.getItem('warung_user') || 'null')

    if (to.path === '/login' && user?.role) {
        return user.role === 'admin' ? '/admin' : '/belanja'
    }

    if (!to.meta.role) return true
    if (!user) return '/login'
    if (user.role !== to.meta.role) return user.role === 'admin' ? '/admin' : '/belanja'
    return true
})

export default router