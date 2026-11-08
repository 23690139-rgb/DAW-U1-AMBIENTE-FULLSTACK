const API_URL = '/api'

async function solicitar(endpoint, opciones = {}) {
  const response = await fetch(`${API_URL}/${endpoint}`, {
    credentials: 'include',
    ...opciones,
    headers: {
      'Accept': 'application/json',
      ...(opciones.body ? { 'Content-Type': 'application/json' } : {}),
      ...opciones.headers
    }
  })

  const contentType = response.headers.get('content-type') || ''

  if (!contentType.includes('application/json')) {
    throw new Error('El servidor no devolvió JSON.')
  }

  const data = await response.json()

  if (response.status === 401) {
    throw new Error('La sesión no es válida.')
  }

  if (!response.ok) {
    throw new Error(data.message || 'Ocurrió un error en la solicitud.')
  }

  return data
}

// SERVICIOS

export async function obtenerServicios() {
  return solicitar('servicios.php')
}

export async function listarServicios() {
  return solicitar('servicios/index.php')
}

export async function registrarServicio(servicio) {
  return solicitar('servicios/store.php', {
    method: 'POST',
    body: JSON.stringify(servicio)
  })
}

export async function actualizarServicio(id, servicio) {
  return solicitar(`servicios/update.php?id=${id}`, {
    method: 'PUT',
    body: JSON.stringify(servicio)
  })
}

export async function cambiarEstadoServicio(id, activo) {
  return solicitar(`servicios/status.php?id=${id}`, {
    method: 'PATCH',
    body: JSON.stringify({ activo })
  })
}

// USUARIOS

export async function listarUsuarios() {
  return solicitar('usuarios/index.php')
}

export async function registrarUsuario(usuario) {
  return solicitar('usuarios/store.php', {
    method: 'POST',
    body: JSON.stringify(usuario)
  })
}

export async function actualizarUsuario(id, usuario) {
  return solicitar(`usuarios/update.php?id=${id}`, {
    method: 'PUT',
    body: JSON.stringify(usuario)
  })
}

export async function cambiarEstadoUsuario(id, activo) {
  return solicitar(`usuarios/status.php?id=${id}`, {
    method: 'PATCH',
    body: JSON.stringify({ activo })
  })
}

