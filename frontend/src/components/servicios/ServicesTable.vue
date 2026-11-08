<script setup>
defineProps({
  services: {
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
      Cargando servicios...
    </p>

    <table v-else>
      <thead>
        <tr>
          <th>ID</th>
          <th>Servicio</th>
          <th>Descripción</th>
          <th>Precio</th>
          <th>Duración</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="service in services" :key="service.id">
          <td>{{ service.id }}</td>
          <td>{{ service.nombre }}</td>
          <td>{{ service.descripcion }}</td>
          <td>{{ formatoPrecio(service.precio) }}</td>
          <td>{{ service.duracion_minutos }} min</td>
          <td>
            <span :class="Number(service.activo) === 1 ? 'active' : 'inactive'">
              {{ Number(service.activo) === 1 ? 'Activo' : 'Inactivo' }}
            </span>
          </td>
          <td>
            <button class="edit" @click="$emit('edit', service)">
              Editar
            </button>

            <button
              class="toggle"
              @click="$emit('toggle-status', service)"
            >
              {{ Number(service.activo) === 1 ? 'Desactivar' : 'Activar' }}
            </button>
          </td>
        </tr>

        <tr v-if="!services.length">
          <td colspan="7">No hay servicios registrados.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  methods: {
    formatoPrecio(precio) {
      return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
      }).format(Number(precio))
    }
  }
}
</script>

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
