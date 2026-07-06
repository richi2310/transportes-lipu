import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import GuardiaView from '../views/GuardiaView.vue'

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: LoginView },
    { path: '/guardia', component: GuardiaView, meta: { requiresAuth: true, rol: 'guardia' } },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    const rol = localStorage.getItem('rol')

    if (to.meta.requiresAuth && !token) {
        next('/login')
    } else if (to.meta.rol && to.meta.rol !== rol) {
        next('/login')
    } else {
        next()
    }
})

export default router