<template>
  <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow" style="width: 100%; max-width: 400px;">
      <div class="card-body p-4">
        <h4 class="card-title text-center mb-4 fw-bold">TRANSPORTES LIPU</h4>
        <p class="text-center text-muted mb-4">Sistema Operativo de Flota</p>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div class="mb-3">
          <label class="form-label">Correo electrónico</label>
          <input v-model="email" type="email" class="form-control form-control-lg"
                 placeholder="usuario@lipu.com" @keyup.enter="login"/>
        </div>
        <div class="mb-4">
          <label class="form-label">Contraseña</label>
          <input v-model="password" type="password" class="form-control form-control-lg"
                 placeholder="••••••••" @keyup.enter="login"/>
        </div>
        <button @click="login" class="btn btn-danger btn-lg w-100" :disabled="cargando">
          {{ cargando ? 'Ingresando...' : 'Ingresar' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'LoginView',
  data() {
    return {
      email: '',
      password: '',
      error: '',
      cargando: false,
    }
  },
  methods: {
    async login() {
      this.error = ''
      this.cargando = true
      try {
        const res = await axios.post('/api/login', {
          email: this.email,
          password: this.password,
        })
        localStorage.setItem('token', res.data.token)
        localStorage.setItem('rol', res.data.user.rol)
        localStorage.setItem('nombre', res.data.user.name)

        const rol = res.data.user.rol
        if (rol === 'guardia') this.$router.push('/guardia')
        else this.error = 'Módulo no disponible para este rol aún.'
      } catch (e) {
        this.error = e.response?.data?.message || 'Error al iniciar sesión'
      } finally {
        this.cargando = false
      }
    },
  },
}
</script>