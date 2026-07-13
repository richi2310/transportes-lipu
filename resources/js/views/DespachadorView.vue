<template>
  <div class="min-vh-100 bg-light">
    <nav class="navbar navbar-dark bg-dark px-3">
      <span class="navbar-brand fw-bold">TRANSPORTES LIPU — Despachador</span>
      <div class="d-flex align-items-center gap-3">
        <span class="text-white">{{ nombre }}</span>
        <button @click="logout" class="btn btn-outline-light btn-sm">Salir</button>
      </div>
    </nav>
    <div class="container-fluid mt-3">
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <a class="nav-link" :class="{ active: tab === 'diesel' }"
             @click="tab = 'diesel'" href="#"><i class="bi bi-fuel-pump"></i> Carga de Diésel</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" :class="{ active: tab === 'historial' }"
             @click="tab = 'historial'" href="#"><i class="bi bi-clock-history"></i> Historial</a>
        </li>
      </ul>
      <BitacoraDiesel v-if="tab === 'diesel'" />
      <HistorialDiesel v-if="tab === 'historial'" />
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import BitacoraDiesel from '../components/despachador/BitacoraDiesel.vue'
import HistorialDiesel from '../components/despachador/HistorialDiesel.vue'

export default {
  name: 'DespachadorView',
  components: { BitacoraDiesel, HistorialDiesel },
  data() {
    return { tab: 'diesel', nombre: localStorage.getItem('nombre') || 'Despachador' }
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