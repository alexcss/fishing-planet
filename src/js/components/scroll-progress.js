export default (count = 12) => ({
  count,
  filled: 0,

  init() {
    this.onScroll = this.onScroll.bind(this)
    this.onScroll()
    this.$el.classList.remove('-translate-x-50')
    window.addEventListener('scroll', this.onScroll, { passive: true })
    window.addEventListener('resize', this.onScroll, { passive: true })
  },

  destroy() {
    window.removeEventListener('scroll', this.onScroll)
    window.removeEventListener('resize', this.onScroll)
  },

  onScroll() {
    const doc = document.documentElement
    const max = doc.scrollHeight - window.innerHeight
    const progress = max > 0 ? doc.scrollTop / max : 0
    this.filled = Math.round(progress * this.count)
  },
})
