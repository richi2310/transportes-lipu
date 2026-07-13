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
          <div class="input-group">
            <input
              v-model="password"
              :type="mostrarPassword ? 'text' : 'password'"
              class="form-control form-control-lg"
              placeholder="••••••••"
              @keyup.enter="login"
            />
            <button
              type="button"
              class="btn btn-outline-secondary"
              @click="mostrarPassword = !mostrarPassword"
              tabindex="-1"
          >
            <svg v-if="!mostrarPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133
              13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879
              1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83
              1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12
              0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18"
             fill="currentColor" viewBox="0 0 16 16">
          <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028
            7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12
            0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828
            8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465
            1.755-.165.165-.337.328-.517.486l.708.709z"/>
          <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5
            2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5
            0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
          <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0
            0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121
            11.332 5.881 12.5 8 12.5c.716 0 1.39-.133
            2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0
            8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12
            .708-.708 12 12-.708.708z"/>
        </svg>
      </button>
    </div>
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
      mostrarPassword: false,
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
        if      (rol === 'guardia')      this.$router.push('/guardia')
        else if (rol === 'despachador')  this.$router.push('/despachador')
        else if (rol === 'operador')     this.$router.push('/operador')
        else if (rol === 'jefe_admin')   this.$router.push('/jefe-admin')
        else if (rol === 'jefe_diesel')  this.$router.push('/jefe-diesel')
        else if (rol === 'gerente_ops')  this.$router.push('/gerente')
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