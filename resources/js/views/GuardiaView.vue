<template>
  <div class="min-vh-100 bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-danger px-3">
      <span class="navbar-brand fw-bold">TRANSPORTES LIPU</span>
      <div class="d-flex align-items-center gap-3">
        <span class="text-white">{{ nombre }}</span>
        <button @click="logout" class="btn btn-outline-light btn-sm">Salir</button>
      </div>
    </nav>

    <!-- Tabs -->
    <div class="container-fluid mt-3">
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
          <a class="nav-link" :class="{ active: tab === 'unidades' }"
             @click="tab = 'unidades'" href="#">🚌 Bitácora Unidades</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" :class="{ active: tab === 'paqueteria' }"
             @click="tab = 'paqueteria'" href="#">📦 Paquetería</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" :class="{ active: tab === 'historial' }"
             @click="tab = 'historial'" href="#">📋 Historial</a>
        </li>
      </ul>

      <!-- Tab: Bitácora Unidades -->
      <BitacoraUnidades v-if="tab === 'unidades'" />

      <!-- Tab: Paquetería -->
      <BitacoraPaqueteria v-if="tab === 'paqueteria'" />

      <!-- Tab: Historial -->
      <HistorialUnidades v-if="tab === 'historial'" />
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import BitacoraUnidades from '../components/guardia/BitacoraUnidades.vue'
import BitacoraPaqueteria from '../components/guardia/BitacoraPaqueteria.vue'
import HistorialUnidades from '../components/guardia/HistorialUnidades.vue'

export default {
  name: 'GuardiaView',
  components: { BitacoraUnidades, BitacoraPaqueteria, HistorialUnidades },
  data() {
    return {
      tab: 'unidades',
      nombre: localStorage.getItem('nombre') || 'Guardia',
    }
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