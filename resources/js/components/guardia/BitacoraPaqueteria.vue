<template>
  <div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">Registro de Paquetería Recibida</div>
    <div class="card-body">

      <div v-if="exito" class="alert alert-success">{{ exito }}</div>
      <div v-if="errorMsg" class="alert alert-danger">{{ errorMsg }}</div>
      <div v-if="smsAviso" class="alert alert-warning">{{ smsAviso }}</div>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold">Remitente</label>
          <input v-model="form.remitente" type="text" class="form-control form-control-lg"
                 placeholder="Empresa o persona que envía"/>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold">Destinatario</label>
          <input v-model="form.destinatario" type="text" class="form-control form-control-lg"
                 placeholder="Quien recibe el paquete"/>
        </div>
        <div class="col-12 col-md-8">
          <label class="form-label fw-semibold">Descripción</label>
          <input v-model="form.descripcion" type="text" class="form-control form-control-lg"
                 placeholder="Contenido del paquete"/>
        </div>
        <div class="col-12 col-md-4">
          <label class="form-label fw-semibold">Cantidad de Paquetes</label>
          <input v-model.number="form.cantidad_paquetes" type="number"
                 class="form-control form-control-lg" min="1" placeholder="1"/>
        </div>
        <div class="col-12">
          <button @click="registrar" class="btn btn-danger btn-lg w-100" :disabled="guardando"><i class="bi bi-box-seam"></i> 
            {{ guardando ? 'Registrando y enviando SMS...' : ' Registrar Paquete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'BitacoraPaqueteria',
  data() {
    return {
      form: { remitente: '', destinatario: '', descripcion: '', cantidad_paquetes: 1 },
      guardando: false,
      exito: '',
      errorMsg: '',
      smsAviso: '',
    }
  },
  methods: {
    async registrar() {
      this.guardando = true
      this.exito = ''
      this.errorMsg = ''
      this.smsAviso = ''
      try {
        const res = await axios.post('/api/paqueteria', this.form, {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        })
        this.exito = '✔ Paquete registrado correctamente'
        if (!res.data.notificacion_enviada) {
          this.smsAviso = '⚠ El registro fue guardado pero el SMS no pudo enviarse.'
        }
        this.form = { remitente: '', destinatario: '', descripcion: '', cantidad_paquetes: 1 }
      } catch (e) {
        this.errorMsg = e.response?.data?.message || 'Error al registrar'
      } finally {
        this.guardando = false
      }
    },
  },
}
</script>