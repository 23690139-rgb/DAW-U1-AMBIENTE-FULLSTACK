<script setup>
defineProps({
  users: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

defineEmits(['edit', 'toggle-status'])
</script>

<template>
  <div class="table-container">
    <p v-if="loading" class="message">
      Cargando usuarios...
    </p>

    <table v-else>
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuario</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.usuario }}</td>
          <td>{{ user.nombre }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.rol }}</td>
          <td>
            <span :class="user.activo ? 'active' : 'inactive'">
              {{ user.activo ? 'Activo' : 'Inactivo' }}
            </span>
          </td>
          <td>
            <button class="edit" @click="$emit('edit', user)">
              Editar
            </button>

            <button
              class="toggle"
              @click="$emit('toggle-status', user)"
            >
              {{ user.activo ? 'Desactivar' : 'Activar' }}
            </button>
          </td>
        </tr>

        <tr v-if="!users.length">
          <td colspan="7">No hay usuarios registrados.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.table-container {
  width: 100%;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

th, td {
  padding: 12px;
  border-bottom: 1px solid #e5e7eb;
  text-align: left;
  font-size: 14px;
}

th {
  background: #263b80;
  color: white;
}

.active {
  color: #15803d;
  font-weight: bold;
}

.inactive {
  color: #dc2626;
  font-weight: bold;
}

button {
  padding: 7px 10px;
  margin: 2px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.edit {
  background: #2563eb;
  color: white;
}

.toggle {
  background: #e5e7eb;
  color: #1f2937;
}

.message {
  padding: 20px;
}
</style>
