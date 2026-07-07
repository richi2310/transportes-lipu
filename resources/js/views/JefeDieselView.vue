<template>
  <div class="min-vh-100 bg-light">
    <nav class="navbar navbar-dark bg-dark px-3">
      <span class="navbar-brand fw-bold">TRANSPORTES LIPU — Jefe de Diésel</span>
      <div class="d-flex align-items-center gap-3">
        <span class="text-white">{{ nombre }}</span>
        <button @click="logout" class="btn btn-outline-light btn-sm">Salir</button>
      </div>
    </nav>
    <div class="container-fluid mt-3">
      <HistorialDiesel />
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import HistorialDiesel from '../components/despachador/HistorialDiesel.vue'

export default {
  name: 'JefeDieselView',
  components: { HistorialDiesel },
  data() {
    return { nombre: localStorage.getItem('nombre') || 'Jefe Diésel' }
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