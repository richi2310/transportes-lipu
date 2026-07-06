<template>
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">Registro de Entrada / Salida de Unidad</div>
    <div class="card-body">

      <div v-if="exito" class="alert alert-success">{{ exito }}</div>
      <div v-if="error" class="alert alert-danger">{{ error }}</div>

      <div class="row g-3">
        <!-- Número de unidad -->
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

        <!-- Km anterior (solo lectura) -->
        <div class="col-12 col-md-4">
          <label class="form-label fw-semibold">Km Anterior (sistema)</label>
          <input :value="km_anterior !== null ? km_anterior + ' km' : '—'"
                 type="text" class="form-control form-control-lg bg-light" readonly/>
        </div>

        <!-- Km actual -->
        <div class="col-12 col-md-4">
          <label class="form-label fw-semibold">Km Actual</label>
          <input v-model.number="km_actual" type="number" class="form-control form-control-lg"
                 placeholder="Ej: 25500" :disabled="!unidad"/>
        </div>

        <!-- Tipo -->
        <div class="col-12 col-md-4">
          <label class="form-label fw-semibold">Tipo de Movimiento</label>
          <select v-model="tipo" class="form-select form-select-lg" :disabled="!unidad">
            <option value="entrada">Entrada</option>
            <option value="salida">Salida</option>
          </select>
        </div>

        <!-- Observaciones -->
        <div class="col-12 col-md-8">
          <label class="form-label fw-semibold">Observaciones (opcional)</label>
          <input v-model="observaciones" type="text" class="form-control form-control-lg"
                 placeholder="Sin novedad..." :disabled="!unidad"/>
        </div>

        <!-- Info unidad -->
        <div v-if="unidad" class="col-12">
          <div class="alert alert-info mb-0">
            <strong>{{ unidad.numero_unidad }}</strong> —
            {{ unidad.modelo }} | Placa: {{ unidad.placa }}
          </div>
        </div>

        <!-- Botón -->
        <div class="col-12">
          <button @click="registrar" class="btn btn-danger btn-lg w-100"
                  :disabled="!unidad || guardando">
            {{ guardando ? 'Guardando...' : '✔ Registrar ' + tipo }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'BitacoraUnidades',
  data() {
    return {
      numero_unidad: '',
      unidad: null,
      km_anterior: null,
      km_actual: null,
      tipo: 'entrada',
      observaciones: '',
      buscando: false,
      guardando: false,
      exito: '',
      error: '',
    }
  },
  methods: {
    async buscarUnidad() {
      if (!this.numero_unidad) return
      this.buscando = true
      this.exito = ''
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
        await axios.post('/api/bitacora-unidades', {
          numero_unidad: this.numero_unidad,
          tipo: this.tipo,
          km_actual: this.km_actual,
          observaciones: this.observaciones,
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        })
        this.exito = `✔ ${this.tipo.toUpperCase()} registrada correctamente para la unidad ${this.numero_unidad}`
        this.unidad = null
        this.km_anterior = null
        this.km_actual = null
        this.numero_unidad = ''
        this.observaciones = ''
      } catch (e) {
        this.error = e.response?.data?.message || 'Error al registrar'
      } finally {
        this.guardando = false
      }
    },
  },
}
</script>