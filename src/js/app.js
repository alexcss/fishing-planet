import 'vite/modulepreload-polyfill'

if (import.meta.env.DEV) {
  import('@vite/client')
}

import.meta.glob(['../../src/**', '!**/*.js', '!**/*.scss', '!**/*.css', '!**/*.php', '!**/*.twig', '!**/screenshot.png', '!**/*.md'])

import Alpine from 'alpinejs'
import introSlider from './components/intro-slider.js'

window.Alpine = Alpine

Alpine.data('introSlider', introSlider)

Alpine.start()
