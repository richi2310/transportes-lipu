<template>
  <div class="min-vh-100 bg-light">
    <nav class="navbar navbar-dark bg-success px-3">
      <span class="navbar-brand fw-bold">TRANSPORTES LIPU — Gerente de Operaciones</span>
      <div class="d-flex align-items-center gap-3">
        <span class="text-white">{{ nombre }}</span>
        <button @click="logout" class="btn btn-outline-light btn-sm">Salir</button>
      </div>
    </nav>
    <div class="container-fluid mt-3">
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <a class="nav-link" :class="{ active: tab === 'aforo' }"
             @click="tab = 'aforo'" href="#"><i class="bi bi-bar-chart-line"></i> Reporte de Aforo</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" :class="{ active: tab === 'paqueteria' }"
             @click="tab = 'paqueteria'" href="#"><i class="bi bi-box-seam"></i> Paquetería</a>
        </li>
      </ul>
      <ReporteAforo v-if="tab === 'aforo'" />
      <HistorialPaqueteria v-if="tab === 'paqueteria'" />
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import ReporteAforo from '../components/admin/ReporteAforo.vue'
import HistorialPaqueteria from '../components/admin/HistorialPaqueteria.vue'

export default {
  name: 'GerenteView',
  components: { ReporteAforo, HistorialPaqueteria },
  data() {
    return { tab: 'aforo', nombre: localStorage.getItem('nombre') || 'Gerente' }
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