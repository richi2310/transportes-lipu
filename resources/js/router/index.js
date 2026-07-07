import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import GuardiaView from '../views/GuardiaView.vue'
import DespachadorView from '../views/DespachadorView.vue'
import OperadorView from '../views/OperadorView.vue'
import JefeAdminView  from '../views/JefeAdminView.vue'
import JefeDieselView from '../views/JefeDieselView.vue'
import GerenteView    from '../views/GerenteView.vue'

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: LoginView },
    { path: '/guardia', component: GuardiaView,
      meta: { requiresAuth: true, rol: 'guardia' } },
    { path: '/despachador', component: DespachadorView,
      meta: { requiresAuth: true, rol: 'despachador' } },
    { path: '/operador', component: OperadorView,
      meta: { requiresAuth: true, rol: 'operador' } },
    { path: '/jefe-admin',  component: JefeAdminView,
      meta: { requiresAuth: true, rol: 'jefe_admin' } },
    { path: '/jefe-diesel', component: JefeDieselView,
      meta: { requiresAuth: true, rol: 'jefe_diesel' } },
    { path: '/gerente',     component: GerenteView,
      meta: { requiresAuth: true, rol: 'gerente_ops' } },
    ]  

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    const rol   = localStorage.getItem('rol')

    if (to.meta.requiresAuth && !token) {
        next('/login')
    } else if (to.meta.rol && to.meta.rol !== rol) {
        next('/login')
    } else {
        next()
    }
})

export default router