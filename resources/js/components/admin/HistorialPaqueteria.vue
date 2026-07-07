<template>
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">📦 Historial de Paquetería</div>
    <div class="card-body p-0">
      <div v-if="cargando" class="text-center p-4">Cargando...</div>
      <div v-else-if="registros.length === 0" class="text-center p-4 text-muted">
        No hay registros
      </div>
      <div v-else class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="table-dark">
            <tr>
              <th>Fecha/Hora</th>
              <th>Remitente</th>
              <th>Destinatario</th>
              <th>Descripción</th>
              <th>Cantidad</th>
              <th>SMS</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in registros" :key="r.id">
              <td>{{ formatFecha(r.fecha_hora) }}</td>
              <td>{{ r.remitente }}</td>
              <td>{{ r.destinatario }}</td>
              <td>{{ r.descripcion }}</td>
              <td>{{ r.cantidad_paquetes }}</td>
              <td>
                <span :class="r.notificacion_enviada ? 'badge bg-success' : 'badge bg-danger'">
                  {{ r.notificacion_enviada ? 'Enviado' : 'Fallido' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="totalPaginas > 1" class="d-flex justify-content-center gap-2 p-3">
        <button @click="pagina--; cargar()" :disabled="pagina === 1"
                class="btn btn-outline-secondary btn-sm">← Anterior</button>
        <span class="align-self-center">Página {{ pagina }} de {{ totalPaginas }}</span>
        <button @click="pagina++; cargar()" :disabled="pagina === totalPaginas"
                class="btn btn-outline-secondary btn-sm">Siguiente →</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'HistorialPaqueteria',
  data() {
    return { registros: [], cargando: false, pagina: 1, totalPaginas: 1 }
  },
  mounted() { this.cargar() },
  methods: {
    async cargar() {
      this.cargando = true
      try {
        const res = await axios.get('/api/paqueteria', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
          params: { page: this.pagina }
        })
        this.registros = res.data.data
        this.totalPaginas = res.data.last_page
      } catch (e) { console.error(e) }
      finally { this.cargando = false }
    },
    formatFecha(f) {
      return new Date(f).toLocaleString('es-MX', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
      })
    },
  },
}
</script>