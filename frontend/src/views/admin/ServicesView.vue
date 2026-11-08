<script setup>
import { ref, onMounted } from 'vue'
import ServicesTable from '../../components/servicios/ServicesTable.vue'
import ServiceForm from '../../components/servicios/ServiceForm.vue'
import {
  listarServicios,
  registrarServicio,
  actualizarServicio,
  cambiarEstadoServicio
} from '../../services/api'

const services = ref([])
const filteredServices = ref([])
const loading = ref(false)
const saving = ref(false)
const showForm = ref(false)
const selectedService = ref(null)
const error = ref('')
const message = ref('')
const search = ref('')

function filtrarServicios() {
  const term = search.value.toLowerCase().trim()

  filteredServices.value = services.value.filter(service =>
    service.nombre.toLowerCase().includes(term) ||
    service.descripcion.toLowerCase().includes(term)
  )
}

async function cargarServicios() {
  loading.value = true
  error.value = ''

  try {
    const response = await listarServicios()
    services.value = response.data
    filtrarServicios()
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

function nuevoServicio() {
  selectedService.value = null
  showForm.value = true
  message.value = ''
}

function editarServicio(service) {
  selectedService.value = { ...service }
  showForm.value = true
  message.value = ''
}

function cancelarFormulario() {
  showForm.value = false
  selectedService.value = null
}

async function guardarServicio(data) {
  saving.value = true
  error.value = ''
  message.value = ''

  try {
    if (selectedService.value) {
      await actualizarServicio(selectedService.value.id, data)
      message.value = 'Servicio actualizado correctamente.'
    } else {
      await registrarServicio(data)
      message.value = 'Servicio registrado correctamente.'
    }

    cancelarFormulario()
    await cargarServicios()
  } catch (err) {
    error.value = err.message
  } finally {
    saving.value = false
  }
}

async function cambiarEstado(service) {
  const nuevoEstado = Number(service.activo) === 1 ? 0 : 1

  const confirmacion = window.confirm(
    `¿Deseas ${nuevoEstado === 1 ? 'activar' : 'desactivar'} el servicio ${service.nombre}?`
  )

  if (!confirmacion) return

  error.value = ''
  message.value = ''

  try {
    await cambiarEstadoServicio(service.id, nuevoEstado)
    message.value = 'Estado del servicio actualizado.'
    await cargarServicios()
  } catch (err) {
    error.value = err.message
  }
}

onMounted(cargarServicios)
</script>

<template>
  <main class="services-page">
    <header class="page-heading">
      <div>
        <span class="eyebrow">ADMINISTRACIÓN</span>
        <h1>Gestión de servicios</h1>
        <p>Administra el catálogo de servicios de la aplicación.</p>
      </div>

      <button class="new-button" @click="nuevoServicio">
        + Nuevo servicio
      </button>
    </header>

    <section class="summary">
      <article class="summary-card">
        <span>Total de servicios</span>
        <strong>{{ services.length }}</strong>
      </article>

      <article class="summary-card">
        <span>Servicios activos</span>
        <strong>
          {{ services.filter(s => Number(s.activo) === 1).length }}
        </strong>
      </article>

      <article class="summary-card">
        <span>Servicios inactivos</span>
        <strong>
          {{ services.filter(s => Number(s.activo) === 0).length }}
        </strong>
      </article>
    </section>

    <div v-if="message" class="success-message">
      {{ message }}
    </div>

    <div v-if="error" class="error-message">
      {{ error }}
    </div>

    <section v-if="showForm" class="form-section">
      <ServiceForm
        :service="selectedService"
        :loading="saving"
        @submit="guardarServicio"
        @cancel="cancelarFormulario"
      />
    </section>

    <section class="services-panel">
      <div class="panel-heading">
        <div>
          <h2>Servicios registrados</h2>
          <p>Consulta y modifica los servicios disponibles.</p>
        </div>

        <input
          v-model="search"
          @input="filtrarServicios"
          type="search"
          placeholder="Buscar servicio..."
          class="search-input"
        />
      </div>

      <ServicesTable
        :services="filteredServices"
        :loading="loading"
        @edit="editarServicio"
        @toggle-status="cambiarEstado"
      />
    </section>
  </main>
</template>

<style scoped>
.services-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 30px 22px;
}

.page-heading {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  margin-bottom: 25px;
}

.eyebrow {
  color: #7c3aed;
  font-weight: 800;
  letter-spacing: 2px;
  font-size: 12px;
}

h1 {
  margin: 7px 0;
  color: #263b80;
  font-size: 30px;
}

.page-heading p,
.panel-heading p {
  color: #64748b;
  margin: 0;
}

.new-button {
  padding: 12px 18px;
  border: none;
  border-radius: 9px;
  background: #7c3aed;
  color: white;
  font-weight: bold;
  cursor: pointer;
}

.summary {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  margin-bottom: 25px;
}

.summary-card {
  padding: 22px;
  background: linear-gradient(135deg, #eef2ff, #f5f3ff);
  border-radius: 12px;
  border-left: 5px solid #7c3aed;
}

.summary-card span {
  display: block;
  color: #64748b;
}

.summary-card strong {
  display: block;
  margin-top: 8px;
  font-size: 28px;
  color: #263b80;
}

.services-panel {
  padding: 22px;
  background: white;
  border-radius: 14px;
  box-shadow: 0 4px 20px #0000000d;
}

.panel-heading {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  margin-bottom: 20px;
}

.panel-heading h2 {
  margin: 0 0 5px;
  color: #263b80;
}

.search-input {
  padding: 11px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  min-width: 230px;
}

.form-section {
  margin-bottom: 25px;
}

.success-message,
.error-message {
  padding: 12px;
  margin-bottom: 15px;
  border-radius: 8px;
}

.success-message {
  background: #dcfce7;
  color: #166534;
}

.error-message {
  background: #fee2e2;
  color: #991b1b;
}

@media (max-width: 700px) {
  .page-heading,
  .panel-heading {
    align-items: stretch;
    flex-direction: column;
  }

  .summary {
    grid-template-columns: 1fr;
  }

  .search-input {
    min-width: 0;
    width: 100%;
  }
}
</style>
