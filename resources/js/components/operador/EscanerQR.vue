<template>
  <div>
    <!-- Configuración inicial -->
    <div v-if="!configurado" class="card shadow-sm mb-3">
      <div class="card-header bg-white fw-bold">Configurar Turno</div>
      <div class="card-body">
        <div v-if="error" class="alert alert-danger">{{ error }}</div>
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold">Selecciona la Unidad</label>
            <select v-model="unidad_id" class="form-select form-select-lg">
              <option value="">-- Selecciona --</option>
              <option v-for="u in unidades" :key="u.id" :value="u.id">
                {{ u.numero_unidad }} — {{ u.modelo }}
              </option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Selecciona la Ruta</label>
            <select v-model="ruta_id" class="form-select form-select-lg">
              <option value="">-- Selecciona --</option>
              <option v-for="r in rutas" :key="r.id" :value="r.id">
                {{ r.nombre_ruta }} — {{ r.hotel_destino }}
              </option>
            </select>
          </div>
          <div class="col-12">
            <button @click="iniciarTurno" class="btn btn-success btn-lg w-100"
                    :disabled="!unidad_id || !ruta_id">
              <i class="bi bi-play-circle"></i> Iniciar Turno y Activar Escáner
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Escáner QR -->
    <div v-if="configurado">
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-white fw-bold d-flex justify-content-between">
          <span><i class="bi bi-qr-code-scan"></i> Escáner de QR</span>
          <button @click="configurado = false" class="btn btn-outline-secondary btn-sm">
            Cambiar turno
          </button>
        </div>
        <div class="card-body">
          <div v-if="exitoScan" class="alert alert-success">{{ exitoScan }}</div>
          <div v-if="errorScan" class="alert alert-danger">{{ errorScan }}</div>

          <!-- Video de la cámara -->
          <div class="position-relative mb-3" style="max-width:400px; margin:0 auto;">
            <video ref="video" class="w-100 rounded border" autoplay playsinline></video>
            <canvas ref="canvas" class="d-none"></canvas>
            <!-- Marco de escaneo -->
            <div class="position-absolute top-50 start-50 translate-middle"
                 style="width:200px;height:200px;border:3px solid #198754;border-radius:8px;pointer-events:none;">
            </div>
          </div>

          <p class="text-center text-muted">Apunta la cámara al código QR del colaborador</p>

          <div class="text-center mt-2">
            <span class="badge bg-success">
              Unidad: {{ unidadNombre }} | Ruta: {{ rutaNombre }}
            </span>
          </div>
        </div>
      </div>

      <!-- Últimos registros del turno -->
      <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">
          Registros del turno ({{ registrosTurno.length }})
        </div>
        <div v-if="registrosTurno.length === 0" class="card-body text-muted text-center">
          Aún no hay registros en este turno
        </div>
        <ul v-else class="list-group list-group-flush">
          <li v-for="(r, i) in registrosTurno" :key="i"
              class="list-group-item d-flex justify-content-between">
            <span>{{ r.colaborador }}</span>
            <span class="text-muted small">{{ r.hora }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import jsQR from 'jsqr'

export default {
  name: 'EscanerQR',
  data() {
    return {
      configurado: false,
      unidad_id: '',
      ruta_id: '',
      unidades: [],
      rutas: [],
      stream: null,
      scanInterval: null,
      procesando: false,
      exitoScan: '',
      errorScan: '',
      error: '',
      registrosTurno: [],
    }
  },
  computed: {
    unidadNombre() {
      const u = this.unidades.find(u => u.id === this.unidad_id)
      return u ? u.numero_unidad : ''
    },
    rutaNombre() {
      const r = this.rutas.find(r => r.id === this.ruta_id)
      return r ? r.nombre_ruta : ''
    },
  },
  async mounted() {
    await this.cargarCatalogos()
  },
  beforeUnmount() {
    this.detenerCamara()
  },
  methods: {
    async cargarCatalogos() {
      try {
        const headers = { Authorization: `Bearer ${localStorage.getItem('token')}` }
        const [unidades, rutas] = await Promise.all([
          axios.get('/api/unidades', { headers }),
          axios.get('/api/rutas', { headers }),
        ])
        this.unidades = unidades.data
        this.rutas = rutas.data
      } catch (e) {
        this.error = 'Error al cargar catálogos'
      }
    },
    async iniciarTurno() {
      this.configurado = true
      await this.$nextTick()
      await this.iniciarCamara()
    },
    async iniciarCamara() {
      try {
        this.stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'environment' }
        })
        this.$refs.video.srcObject = this.stream
        this.scanInterval = setInterval(this.escanear, 500)
      } catch (e) {
        this.errorScan = 'No se pudo acceder a la cámara. Verifica los permisos.'
      }
    },
    detenerCamara() {
      if (this.stream) {
        this.stream.getTracks().forEach(t => t.stop())
      }
      if (this.scanInterval) {
        clearInterval(this.scanInterval)
      }
    },
    escanear() {
      if (this.procesando) return
      const video = this.$refs.video
      const canvas = this.$refs.canvas
      if (!video || video.readyState !== video.HAVE_ENOUGH_DATA) return

      canvas.width  = video.videoWidth
      canvas.height = video.videoHeight
      const ctx = canvas.getContext('2d')
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height)
      const code = jsQR(imageData.data, imageData.width, imageData.height)

      if (code) {
        this.procesarQR(code.data)
      }
    },
    async procesarQR(codigo) {
      this.procesando = true
      this.exitoScan = ''
      this.errorScan = ''
      try {
        const res = await axios.post('/api/aforo', {
          codigo_qr: codigo,
          unidad_id: this.unidad_id,
          ruta_id:   this.ruta_id,
        }, {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        })
        this.exitoScan = `✔ ${res.data.colaborador} — ${res.data.hotel}`
        this.registrosTurno.unshift({
          colaborador: res.data.colaborador,
          hora: new Date().toLocaleTimeString('es-MX'),
        })
        // Pausa de 2 segundos para no registrar duplicados
        setTimeout(() => { this.procesando = false }, 2000)
      } catch (e) {
        if (e.response?.status === 409) {
          this.errorScan = e.response.data.message
        } else {
          this.errorScan = '❌ ' + (e.response?.data?.message || 'Error al registrar')
        }
        setTimeout(() => { this.procesando = false }, 2000)
      }
    },
  },
}
</script>