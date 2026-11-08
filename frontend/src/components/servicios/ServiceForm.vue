<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  service: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit', 'cancel'])

const form = reactive({
  nombre: '',
  descripcion: '',
  precio: '',
  duracion_minutos: 60,
  activo: 1
})

watch(
  () => props.service,
  (service) => {
    Object.assign(form, {
      nombre: service?.nombre ?? '',
      descripcion: service?.descripcion ?? '',
      precio: service?.precio ?? '',
      duracion_minutos: service?.duracion_minutos ?? 60,
      activo: service ? Number(service.activo) : 1
    })
  },
  { immediate: true }
)

function submitForm() {
  emit('submit', {
    ...form,
    precio: Number(form.precio),
    duracion_minutos: Number(form.duracion_minutos),
    activo: Number(form.activo)
  })
}
</script>

<template>
  <form class="service-form" @submit.prevent="submitForm">
    <h2>{{ service ? 'Editar servicio' : 'Registrar servicio' }}</h2>

    <label>Nombre del servicio</label>
    <input
      v-model.trim="form.nombre"
      type="text"
      placeholder="Ej. Desarrollo Web"
      required
      maxlength="120"
    />

    <label>Descripción</label>
    <textarea
      v-model.trim="form.descripcion"
      placeholder="Describe las características del servicio"
      required
      rows="4"
    ></textarea>

    <label>Precio (MXN)</label>
    <input
      v-model="form.precio"
      type="number"
      min="0"
      step="0.01"
      placeholder="8500.00"
      required
    />

    <label>Duración en minutos</label>
    <input
      v-model="form.duracion_minutos"
      type="number"
      min="1"
      step="1"
      required
    />

    <label>Estado</label>
    <select v-model.number="form.activo">
      <option :value="1">Activo</option>
      <option :value="0">Inactivo</option>
    </select>

    <div class="actions">
      <button type="submit" :disabled="loading">
        {{ loading ? 'Guardando...' : 'Guardar servicio' }}
      </button>

      <button
        type="button"
        class="cancel"
        @click="emit('cancel')"
      >
        Cancelar
      </button>
    </div>
  </form>
</template>

<style scoped>
.service-form {
  display: grid;
  gap: 10px;
  padding: 22px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 15px #00000012;
}

h2 {
  margin: 0 0 10px;
  color: #263b80;
}

label {
  font-weight: 600;
  color: #374151;
}

input,
textarea,
select {
  width: 100%;
  padding: 11px;
  border: 1px solid #d1d5db;
  border-radius: 7px;
  font: inherit;
}

textarea {
  resize: vertical;
}

.actions {
  display: flex;
  gap: 10px;
  margin-top: 12px;
}

button {
  padding: 10px 16px;
  border: none;
  border-radius: 7px;
  background: #263b80;
  color: white;
  cursor: pointer;
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

button.cancel {
  background: #6b7280;
}
</style>
