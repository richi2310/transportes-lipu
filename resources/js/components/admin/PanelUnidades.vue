<template>
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center flex-wrap gap-2">
      <span><i class="bi bi-card-list"></i> Bitácora de Unidades</span>
      <div class="d-flex gap-2 flex-wrap">
        <input v-model="filtroFecha" type="date" class="form-control form-control-sm"
               @change="cargar" style="width:170px"/>
        <button @click="limpiar" class="btn btn-outline-secondary btn-sm">Hoy</button>
        <button @click="cargar" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-clockwise"></i> Actualizar</button>
      </div>
    </div>
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
              <th>Unidad</th>
              <th>Tipo</th>
              <th>Km Anterior</th>
              <th>Km Actual</th>
              <th>Diferencia</th>
              <th>Observaciones</th>
              <th>Guardia</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in registros" :key="r.id">
              <td>{{ formatFecha(r.fecha_hora) }}</td>
              <td><strong>{{ r.unidad?.numero_unidad }}</strong></td>
              <td>
                <span :class="r.tipo === 'entrada' ? 'badge bg-success' : 'badge bg-warning text-dark'">
                  {{ r.tipo }}
                </span>
              </td>
              <td>{{ r.km_anterior }} km</td>
              <td>{{ r.km_actual }} km</td>
              <td>{{ r.diferencia_km }} km</td>
              <td>{{ r.observaciones || '—' }}</td>
              <td>{{ r.guardia?.name }}</td>
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
  name: 'PanelUnidades',
  data() {
    return {
      registros: [],
      cargando: false,
      filtroFecha: new Date().toISOString().split('T')[0],
      pagina: 1,
      totalPaginas: 1,
    }
  },
  mounted() { this.cargar() },
  methods: {
    async cargar() {
      this.cargando = true
      try {
        const params = { page: this.pagina }
        if (this.filtroFecha) params.fecha = this.filtroFecha
        const res = await axios.get('/api/bitacora-unidades', {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }, params
        })
        this.registros = res.data.data
        this.totalPaginas = res.data.last_page
        this.$emit('actualizar')
      } catch (e) { console.error(e) }
      finally { this.cargando = false }
    },
    limpiar() {
      this.filtroFecha = new Date().toISOString().split('T')[0]
      this.pagina = 1
      this.cargar()
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