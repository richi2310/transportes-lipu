<template>
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">Registro de Carga de Diésel</div>
    <div class="card-body">
      <div v-if="exito" class="alert alert-success">{{ exito }}</div>
      <div v-if="error" class="alert alert-danger">{{ error }}</div>

      <div class="row g-3">
        <div class="col-12 col-md-4">
          <label class="form-label fw-semibold">Número de Unidad</label>
          <div class="input-group">
            <input v-model="numero_unidad" type="text" class="form-control form-control-lg"
                   placeholder="U-001" @keyup.enter="buscarUnidad"/>
            <button @click="buscarUnidad" class="btn btn-secondary" :disabled="buscando">
              {{ buscando ? '...' : 'Buscar' }}
            </button>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label fw-semibold">Km Anterior (sistema)</label>
          <input :value="km_anterior !== null ? km_anterior + ' km' : '—'"
                 type="text" class="form-control form-control-lg bg-light" readonly/>
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label fw-semibold">Km Actual</label>
          <input v-model.number="km_actual" type="number" class="form-control form-control-lg"
                 placeholder="Ej: 26000" :disabled="!unidad"/>
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label fw-semibold">Litros Cargados</label>
          <input v-model.number="litros_cargados" type="number" step="0.1"
                 class="form-control form-control-lg" placeholder="Ej: 120.5" :disabled="!unidad"/>
        </div>

        <div v-if="rendimiento !== null" class="col-12 col-md-4">
          <label class="form-label fw-semibold">Rendimiento estimado</label>
          <input :value="rendimiento + ' km/lt'" type="text"
                 class="form-control form-control-lg bg-light" readonly/>
        </div>

        <div v-if="unidad" class="col-12">
          <div class="alert alert-info mb-0">
            <strong>{{ unidad.numero_unidad }}</strong> —
            {{ unidad.modelo }} | Placa: {{ unidad.placa }}
          </div>
        </div>

        <div class="col-12">
          <button @click="registrar" class="btn btn-dark btn-lg w-100"
                  :disabled="!unidad || guardando"><i class="bi bi-fuel-pump"></i>
            {{ guardando ? 'Guardando...' : 'Registrar Carga de Diésel' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'BitacoraDiesel',
  data() {
    return {
      numero_unidad: '', unidad: null, km_anterior: null,
      km_actual: null, litros_cargados: null,
      buscando: false, guardando: false, exito: '', error: '',
    }
  },
  computed: {
    rendimiento() {
      if (this.km_actual && this.km_anterior !== null && this.litros_cargados > 0) {
        const diff = this.km_actual - this.km_anterior
        if (diff > 0) return (diff / this.litros_cargados).toFixed(2)
      }
      return null
    },
  },
  methods: {
    async buscarUnidad() {
      if (!this.numero_unidad) return
      this.buscando = true
      this.error = ''
      this.unidad = null
      this.km_anterior = null
      try {
        const res = await axios.get(`/api/unidades/${this.numero_unidad}`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        })
        this.unidad = res.data
        this.km_anterior = res.data.ultimo_km
      } catch (e) {
        this.error = e.response?.data?.message || 'Unidad no encontrada'
      } finally {
        this.buscando = false
      }
    },
    async registrar() {
      this.guardando = true
      this.exito = ''
      this.error = ''
      try {
        const res = await axios.post('/api/diesel', {
          numero_unidad: this.numero_unidad,
          km_actual: this.km_actual,
          litros_cargados: this.litros_cargados,
        }, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } })

        const r = res.data.registro
        this.exito = `✔ Carga registrada. Rendimiento: ${r.rendimiento_km_litro ?? 'N/A'} km/lt`
        this.unidad = null
        this.km_anterior = null
        this.km_actual = null
        this.litros_cargados = null
        this.numero_unidad = ''
      } catch (e) {
        this.error = e.response?.data?.message || 'Error al registrar'
      } finally {
        this.guardando = false
      }
    },
  },
}
</script>