// @vitest-environment jsdom

import { mount } from '@vue/test-utils'
import { describe, expect, it } from 'vitest'
import ServiceCard from './ServiceCard.vue'

describe('ServiceCard', () => {
  const servicio = {
    id: 1,
    nombre: 'Desarrollo Web',
    descripcion: 'Desarrollo de aplicaciones web empresariales.',
    precio: 8500
  }

  it('muestra la información del servicio', () => {
    const wrapper = mount(ServiceCard, {
      props: { servicio }
    })

    expect(wrapper.text()).toContain('Desarrollo Web')
    expect(wrapper.text()).toContain(
      'Desarrollo de aplicaciones web empresariales.'
    )
    expect(wrapper.text()).toContain('8500')
  })

  it('emite el evento seleccionar al presionar el botón', async () => {
    const wrapper = mount(ServiceCard, {
      props: { servicio }
    })

    await wrapper.get('button').trigger('click')

    expect(wrapper.emitted('seleccionar')).toBeTruthy()
    expect(wrapper.emitted('seleccionar')[0]).toEqual([servicio])
  })
})