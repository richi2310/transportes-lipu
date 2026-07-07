<template>
  <div>
    <div class="card shadow-sm mb-3">
      <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>📊 Reporte Semanal de Aforo</span>
        <div class="d-flex gap-2 flex-wrap">
          <input v-model="inicio" type="date" class="form-control form-control-sm" style="width:160px"/>
          <input v-model="fin" type="date" class="form-control form-control-sm" style="width:160px"/>
          <button @click="cargar" class="btn btn-primary btn-sm">Consultar</button>
          <button @click="exportar" class="btn btn-success btn-sm">⬇ Excel</button>
        </div>
      </div>
      <div class="card-body">
        <div v-if="cargando" class="text-center p-3">Cargando...</div>
        <div v-else-if="resumen.length === 0" class="text-center text-muted p-3">
          No hay registros en el periodo seleccionado
        </div>
        <div v-else>
          <h6 class="fw-bold mb-3">Resumen por ruta y unidad</h6>
          <div class="table-responsive mb-4">
            <table class="table table-bordered mb-0">
              <thead class="table-dark">
                <tr>
                  <th>Ruta</th>
                  <th>Unidad</th>
                  <th>Total pasajeros</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="r in resumen" :key="r.ruta_id + '-' + r.unidad_id">
                  <td>{{ r.ruta?.nombre_ruta }}</td>
                  <td>{{ r.unidad?.numero_unidad }}</td>
                  <td><strong>{{ r.total }}</strong></td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="table-secondary">
                  <td colspan="2"><strong>Total general</strong></td>
                  <td><strong>{{ totalGeneral }}</strong></td>
                </tr>
              </tfoot>
            </table>
          </div>
          <h6 class="fw-bold mb-3">Detalle de abordajes</h6>
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
              <thead class="table-dark">
                <tr>
                  <th>Fecha/Hora</th>
                  <th>Colaborador</th>
                  <th>Hotel</th>
                  <th>Ruta</th>
                  <th>Unidad</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="r in detalle" :key="r.id">
                  <td>{{ formatFecha(r.fecha_hora) }}</td>
                  <td>{{ r.colaborador?.nombre }} {{ r.colaborador?.apellidos }}</td>
                  <td>{{ r.colaborador?.empresa_hotel }}</td>
                  <td>{{ r.ruta?.nombre_ruta }}</td>
                  <td>{{ r.unidad?.numero_unidad }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ReporteAforo',
  data() {
    const hoy  = new Date()
    const lunes = new Date(hoy)
    lunes.setDate(hoy.getDate() - hoy.getDay() + 1)
    const domingo = new Date(lunes)
    domingo.setDate(lunes.getDate() + 6)
    return {
      inicio: lunes.toISOString().split('T')[0],
      fin:    domingo.toISOString().split('T')[0],
      resumen: [], detalle: [], cargando: false,
    }
  },
  computed: {
    totalGeneral() {
      return this.resumen.reduce((sum, r) => sum + parseInt(r.total), 0)
    },
  },
  mounted() { this.cargar() },
  methods: {
    async cargar() {
      this.cargando = true
      try {
        const res = await axios.get('/api/aforo/reporte-semanal', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
          params: { inicio: this.inicio, fin: this.fin }
        })
        this.resumen = res.data.resumen
        this.detalle = res.data.detalle
      } catch (e) { console.error(e) }
      finally { this.cargando = false }
    },
    async exportar() {
      try {
        const token = localStorage.getItem('token')
        const res = await fetch(`/api/excel/aforo?inicio=${this.inicio}&fin=${this.fin}`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        const blob = await res.blob()
        const url = URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `reporte_aforo_${this.inicio}_${this.fin}.xlsx`
        a.click()
        URL.revokeObjectURL(url)
      } catch (e) {
        console.error('Error al exportar:', e)
      }
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