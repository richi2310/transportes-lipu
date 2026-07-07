<template>
  <div class="min-vh-100 bg-light">
    <nav class="navbar navbar-dark bg-success px-3">
      <span class="navbar-brand fw-bold">TRANSPORTES LIPU — Operador</span>
      <div class="d-flex align-items-center gap-3">
        <span class="text-white">{{ nombre }}</span>
        <button @click="logout" class="btn btn-outline-light btn-sm">Salir</button>
      </div>
    </nav>
    <div class="container-fluid mt-3">
      <EscanerQR />
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import EscanerQR from '../components/operador/EscanerQR.vue'

export default {
  name: 'OperadorView',
  components: { EscanerQR },
  data() {
    return { nombre: localStorage.getItem('nombre') || 'Operador' }
  },
  methods: {
    async logout() {
      try {
        await axios.post('/api/logout', {}, {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        })
      } finally {
        localStorage.clear()
        this.$router.push('/login')
      }
    },
  },
}
</script>