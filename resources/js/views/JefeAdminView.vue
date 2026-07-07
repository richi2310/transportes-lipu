<template>
  <div class="min-vh-100 bg-light">
    <nav class="navbar navbar-dark bg-primary px-3">
      <span class="navbar-brand fw-bold">TRANSPORTES LIPU — Jefe de Administración</span>
      <div class="d-flex align-items-center gap-3">
        <span class="text-white">{{ nombre }}</span>
        <button @click="logout" class="btn btn-outline-light btn-sm">Salir</button>
      </div>
    </nav>
    <div class="container-fluid mt-3">
      <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
          <div class="card text-center shadow-sm">
            <div class="card-body">
              <div class="fs-1 fw-bold text-success">{{ conteo.entradas }}</div>
              <div class="text-muted small">Entradas hoy</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card text-center shadow-sm">
            <div class="card-body">
              <div class="fs-1 fw-bold text-warning">{{ conteo.salidas }}</div>
              <div class="text-muted small">Salidas hoy</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card text-center shadow-sm">
            <div class="card-body">
              <div class="fs-1 fw-bold text-primary">{{ conteo.total }}</div>
              <div class="text-muted small">Registros hoy</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card text-center shadow-sm">
            <div class="card-body">
              <div class="fs-1 fw-bold text-danger">{{ conteo.paquetes }}</div>
              <div class="text-muted small">Paquetes hoy</div>
            </div>
          </div>
        </div>
      </div>
      <PanelUnidades @actualizar="actualizarConteo" ref="panel"/>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import PanelUnidades from '../components/admin/PanelUnidades.vue'

export default {
  name: 'JefeAdminView',
  components: { PanelUnidades },
  data() {
    return {
      nombre: localStorage.getItem('nombre') || 'Jefe Admin',
      conteo: { entradas: 0, salidas: 0, total: 0, paquetes: 0 },
    }
  },
  mounted() {
    this.actualizarConteo()
    setInterval(() => {
      this.$refs.panel?.cargar()
      this.actualizarConteo()
    }, 30000)
  },
  methods: {
    async actualizarConteo() {
      try {
        const headers = { Authorization: `Bearer ${localStorage.getItem('token')}` }
        const hoy = new Date().toISOString().split('T')[0]
        const [unidades, paquetes] = await Promise.all([
          axios.get('/api/bitacora-unidades', { headers, params: { fecha: hoy, page: 1 } }),
          axios.get('/api/paqueteria', { headers }),
        ])
        const registros = unidades.data.data
        this.conteo.entradas = registros.filter(r => r.tipo === 'entrada').length
        this.conteo.salidas  = registros.filter(r => r.tipo === 'salida').length
        this.conteo.total    = registros.length
        this.conteo.paquetes = paquetes.data.data.filter(p => {
          return new Date(p.fecha_hora).toISOString().split('T')[0] === hoy
        }).length
      } catch (e) { console.error(e) }
    },
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