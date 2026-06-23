export default () => ({
  isFooterVisible: false,

  init() {
    this.checkFooter()
    window.addEventListener('scroll', this.checkFooter.bind(this), { passive: true })
    window.addEventListener('resize', this.checkFooter.bind(this))
  },

  destroy() {
    window.removeEventListener('scroll', this.checkFooter.bind(this))
    window.removeEventListener('resize', this.checkFooter.bind(this))
  },

  checkFooter() {
    const footer = document.querySelector('[data-footer]')
    if (!footer) {
      this.isFooterVisible = false
      return
    }

    const footerRect = footer.getBoundingClientRect()
    const triggerPoint = window.innerHeight - this.$el.offsetHeight

    this.isFooterVisible = footerRect.top < triggerPoint
  },
})
