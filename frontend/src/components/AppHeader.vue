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
      <div class="brand">
        <h1>Servicios Web</h1>
        <span>Panel de administración</span>
      </div>

      <nav aria-label="Navegación principal">
        <RouterLink to="/dashboard">
          Inicio
        </RouterLink>

        <RouterLink to="/servicios">
          Servicios
        </RouterLink>

        <RouterLink
          v-if="user?.rol === 'ADMIN_SISTEMA'"
          to="/admin/usuarios"
        >
          Usuarios
        </RouterLink>

        <RouterLink
          v-if="['ADMIN_SISTEMA', 'ADMIN_SERVICIOS'].includes(user?.rol)"
          to="/admin/servicios"
        >
          Administrar servicios
        </RouterLink>

        <div v-if="user" class="user-info">
          <span class="user-name">
            👤 {{ user.nombre }}
          </span>
          <span class="user-role">
            {{ user.rol }}
          </span>
        </div>

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

<style scoped>
.header {
  background: linear-gradient(120deg, #263b80, #6d28d9);
  color: white;
  padding: 15px 25px;
  box-shadow: 0 3px 12px #00000020;
}

.container {
  max-width: 1300px;
  margin: auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.brand h1 {
  margin: 0;
  font-size: 23px;
}

.brand span {
  font-size: 12px;
  color: #ddd6fe;
}

nav {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 18px;
}

nav a {
  color: white;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
}

nav a:hover,
nav a.router-link-active {
  color: #fde68a;
}

.user-info {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.user-name {
  font-size: 13px;
  font-weight: bold;
}

.user-role {
  font-size: 10px;
  color: #ddd6fe;
}

.btn-logout {
  padding: 9px 13px;
  border: none;
  border-radius: 7px;
  background: #fbbf24;
  color: #422006;
  font-weight: bold;
  cursor: pointer;
}

.btn-logout:hover {
  background: #f59e0b;
}

@media (max-width: 800px) {
  .container {
    align-items: flex-start;
    flex-direction: column;
  }

  nav {
    gap: 12px;
  }
}
</style>

