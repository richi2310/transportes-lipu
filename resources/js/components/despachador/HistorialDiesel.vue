<template>
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center flex-wrap gap-2">
      <span>Historial de Cargas de Diésel</span>
      <div class="d-flex gap-2 flex-wrap">
        <input v-model="filtroFecha" type="date" class="form-control form-control-sm"
               @change="cargar" style="width:170px"/>
        <button @click="limpiar" class="btn btn-outline-secondary btn-sm">Todos</button>
        <button @click="exportar" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Exportar Excel</button>
      </div>
    </div>
    <div class="card-body p-0">
      <div v-if="cargando" class="text-center p-4">Cargando...</div>
      <div v-else-if="registros.length === 0" class="text-center p-4 text-muted">No hay registros</div>
      <div v-else class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="table-dark">
            <tr>
              <th>Fecha/Hora</th>
              <th>Unidad</th>
              <th>Km Anterior</th>
              <th>Km Actual</th>
              <th>Litros</th>
              <th>Rendimiento</th>
              <th>Despachador</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in registros" :key="r.id">
              <td>{{ formatFecha(r.fecha_hora) }}</td>
              <td><strong>{{ r.unidad?.numero_unidad }}</strong></td>
              <td>{{ r.km_anterior }} km</td>
              <td>{{ r.km_actual }} km</td>
              <td>{{ r.litros_cargados }} lt</td>
              <td>{{ r.rendimiento_km_litro ?? '—' }} km/lt</td>
              <td>{{ r.despachador?.name }}</td>
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
  name: 'HistorialDiesel',
  data() {
    return { registros: [], cargando: false, filtroFecha: '', pagina: 1, totalPaginas: 1 }
  },
  mounted() { this.cargar() },
  methods: {
    async cargar() {
      this.cargando = true
      try {
        const params = { page: this.pagina }
        if (this.filtroFecha) params.fecha = this.filtroFecha
        const res = await axios.get('/api/diesel', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }, params
        })
        this.registros = res.data.data
        this.totalPaginas = res.data.last_page
      } catch (e) { console.error(e) }
      finally { this.cargando = false }
    },
    limpiar() { this.filtroFecha = ''; this.pagina = 1; this.cargar() },
    async exportar() {
      try {
        const token = localStorage.getItem('token')
        const params = this.filtroFecha
          ? `?fecha_inicio=${this.filtroFecha}&fecha_fin=${this.filtroFecha}`
          : ''
        const res = await fetch(`/api/excel/diesel${params}`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        const blob = await res.blob()
        const url = URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `reporte_diesel_${new Date().toISOString().slice(0,10)}.xlsx`
        a.click()
        URL.revokeObjectURL(url)
      } catch (e) {
        console.error('Error al exportar:', e)
      }
    },
    formatFecha(f) {
      return new Date(f).toLocaleString('es-MX', {
        day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
      })
    },
  },
}
</script>