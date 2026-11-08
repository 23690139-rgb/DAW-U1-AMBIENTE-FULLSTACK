<script setup>
import { ref, onMounted } from 'vue'
import UsersTable from '../../components/usuarios/UsersTable.vue'
import UserForm from '../../components/usuarios/UserForm.vue'
import {
  listarUsuarios,
  registrarUsuario,
  actualizarUsuario,
  cambiarEstadoUsuario
} from '../../services/api'

const users = ref([])
const loading = ref(false)
const saving = ref(false)
const showForm = ref(false)
const selectedUser = ref(null)
const error = ref('')
const message = ref('')
const search = ref('')

const filteredUsers = ref([])

function filtrarUsuarios() {
  const term = search.value.toLowerCase().trim()

  filteredUsers.value = users.value.filter(user =>
    user.usuario.toLowerCase().includes(term) ||
    user.nombre.toLowerCase().includes(term) ||
    user.email.toLowerCase().includes(term) ||
    user.rol.toLowerCase().includes(term)
  )
}

async function cargarUsuarios() {
  loading.value = true
  error.value = ''

  try {
    const response = await listarUsuarios()
    users.value = response.data
    filtrarUsuarios()
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

function nuevoUsuario() {
  selectedUser.value = null
  showForm.value = true
  message.value = ''
}

function editarUsuario(user) {
  selectedUser.value = { ...user }
  showForm.value = true
  message.value = ''
}

function cancelarFormulario() {
  showForm.value = false
  selectedUser.value = null
}

async function guardarUsuario(data) {
  saving.value = true
  error.value = ''
  message.value = ''

  try {
    if (selectedUser.value) {
      await actualizarUsuario(selectedUser.value.id, data)
      message.value = 'Usuario actualizado correctamente.'
    } else {
      await registrarUsuario(data)
      message.value = 'Usuario registrado correctamente.'
    }

    cancelarFormulario()
    await cargarUsuarios()
  } catch (err) {
    error.value = err.message
  } finally {
    saving.value = false
  }
}

async function cambiarEstado(user) {
  const nuevoEstado = Number(user.activo) === 1 ? 0 : 1

  const confirmacion = window.confirm(
    `¿Deseas ${nuevoEstado === 1 ? 'activar' : 'desactivar'} al usuario ${user.usuario}?`
  )

  if (!confirmacion) return

  error.value = ''
  message.value = ''

  try {
    await cambiarEstadoUsuario(user.id, nuevoEstado)
    message.value = 'Estado del usuario actualizado.'
    await cargarUsuarios()
  } catch (err) {
    error.value = err.message
  }
}

onMounted(cargarUsuarios)
</script>

<template>
  <main class="users-page">
    <header class="page-heading">
      <div>
        <span class="eyebrow">ADMINISTRACIÓN</span>
        <h1>Gestión de usuarios</h1>
        <p>Administra las cuentas, roles y estados de los usuarios.</p>
      </div>

      <button class="new-button" @click="nuevoUsuario">
        + Nuevo usuario
      </button>
    </header>

    <section class="summary">
      <article class="summary-card">
        <span>Total de usuarios</span>
        <strong>{{ users.length }}</strong>
      </article>

      <article class="summary-card">
        <span>Usuarios activos</span>
        <strong>{{ users.filter(u => Number(u.activo) === 1).length }}</strong>
      </article>

      <article class="summary-card">
        <span>Usuarios inactivos</span>
        <strong>{{ users.filter(u => Number(u.activo) === 0).length }}</strong>
      </article>
    </section>

    <div v-if="message" class="success-message">
      {{ message }}
    </div>

    <div v-if="error" class="error-message">
      {{ error }}
    </div>

    <section v-if="showForm" class="form-section">
      <UserForm
        :user="selectedUser"
        :loading="saving"
        @submit="guardarUsuario"
        @cancel="cancelarFormulario"
      />
    </section>

    <section class="users-panel">
      <div class="panel-heading">
        <div>
          <h2>Usuarios registrados</h2>
          <p>Consulta y modifica las cuentas del sistema.</p>
        </div>

        <input
          v-model="search"
          @input="filtrarUsuarios"
          type="search"
          placeholder="Buscar usuario..."
          class="search-input"
        />
      </div>

      <UsersTable
        :users="filteredUsers"
        :loading="loading"
        @edit="editarUsuario"
        @toggle-status="cambiarEstado"
      />
    </section>
  </main>
</template>

<style scoped>
.users-page {
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

.users-panel {
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
