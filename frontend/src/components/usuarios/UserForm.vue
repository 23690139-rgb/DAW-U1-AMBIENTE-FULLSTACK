<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  user: {
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
  usuario: '',
  nombre: '',
  email: '',
  password: '',
  rol: 'CLIENTE',
  activo: 1
})

watch(
  () => props.user,
  (user) => {
    Object.assign(form, {
      usuario: user?.usuario ?? '',
      nombre: user?.nombre ?? '',
      email: user?.email ?? '',
      password: '',
      rol: user?.rol ?? 'CLIENTE',
      activo: user ? Number(user.activo) : 1
    })
  },
  { immediate: true }
)

function submitForm() {
  emit('submit', { ...form })
}
</script>

<template>
  <form class="user-form" @submit.prevent="submitForm">
    <h2>{{ user ? 'Editar usuario' : 'Registrar usuario' }}</h2>

    <label>Nombre de usuario</label>
    <input v-model.trim="form.usuario" required />

    <label>Nombre completo</label>
    <input v-model.trim="form.nombre" required />

    <label>Correo electrónico</label>
    <input v-model.trim="form.email" type="email" required />

    <label>
      Contraseña
      <small v-if="user">(Déjala vacía para conservar la actual)</small>
    </label>
    <input
      v-model="form.password"
      type="password"
      :required="!user"
      :minlength="user ? undefined : 8"
      placeholder="Mínimo 8 caracteres"
    />

    <label>Rol</label>
    <select v-model="form.rol" required>
      <option value="ADMIN_SISTEMA">Administrador del sistema</option>
      <option value="ADMIN_SERVICIOS">Administrador de servicios</option>
      <option value="CLIENTE">Cliente</option>
    </select>

    <label>Estado</label>
    <select v-model.number="form.activo">
      <option :value="1">Activo</option>
      <option :value="0">Inactivo</option>
    </select>

    <div class="actions">
      <button type="submit" :disabled="loading">
        {{ loading ? 'Guardando...' : 'Guardar usuario' }}
      </button>

      <button type="button" class="cancel" @click="emit('cancel')">
        Cancelar
      </button>
    </div>
  </form>
</template>

<style scoped>
.user-form {
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

small {
  display: block;
  font-weight: normal;
  color: #6b7280;
}

input, select {
  width: 100%;
  padding: 11px;
  border: 1px solid #d1d5db;
  border-radius: 7px;
  font: inherit;
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
