<script setup>
import { useRouter } from 'vue-router'
import { useAuth } from '../services/auth'

const router = useRouter()
const { user, logout } = useAuth()

async function cerrarSesion() {
  await logout()
  router.push('/login')
}
</script>

<template>
  <header class="header">
    <div class="container">
      <h1>Servicios Web</h1>

      <nav aria-label="Navegación principal">
        <RouterLink to="/dashboard">
          Inicio
        </RouterLink>

        <RouterLink to="/servicios">
          Servicios
        </RouterLink>

        <span v-if="user" class="user-name">
          👤 {{ user.nombre }}
        </span>

        <button
          class="btn-logout"
          type="button"
          @click="cerrarSesion"
        >
          Cerrar sesión
        </button>
      </nav>
    </div>
  </header>
</template>
