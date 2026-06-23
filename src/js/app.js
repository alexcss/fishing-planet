import 'vite/modulepreload-polyfill'

if (import.meta.env.DEV) {
  import('@vite/client')
}

import.meta.glob(['../../src/**', '!**/*.js', '!**/*.scss', '!**/*.css', '!**/*.php', '!**/*.twig', '!**/screenshot.png', '!**/*.md'])

import Alpine from 'alpinejs'
import videoSlider from './components/sliders.js'
import playForFreePanel from './components/play-for-free-panel.js'
import frontPageHeader from './components/front-page-header.js'

window.Alpine = Alpine

Alpine.data('videoSlider', videoSlider)
Alpine.data('playForFreePanel', playForFreePanel)
Alpine.data('frontPageHeader', frontPageHeader)

Alpine.start()
