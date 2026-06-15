import 'vite/modulepreload-polyfill'

if (import.meta.env.DEV) {
  import('@vite/client')
}

import.meta.glob(['../../src/**', '!**/*.js', '!**/*.scss', '!**/*.css', '!**/*.php', '!**/*.twig', '!**/screenshot.png', '!**/*.md'])

import Alpine from 'alpinejs'
import videoSlider from './components/sliders.js'

window.Alpine = Alpine

Alpine.data('videoSlider', videoSlider)

Alpine.start()
