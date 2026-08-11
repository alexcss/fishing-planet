// wp.blocks.registerBlockStyle('core/image', {
//   name: 'full-width',
//   label: 'Full width image',
// })

wp.domReady(function () {
  wp.blocks.unregisterBlockStyle('core/image', 'rounded')
  wp.blocks.unregisterBlockStyle('core/separator', 'dots')
  wp.blocks.unregisterBlockStyle('core/separator', 'wide')
})
