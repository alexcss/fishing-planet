export default function headerScroll() {
  return {
    _onScroll: null,

    init() {
      this._onScroll = () => {
        this.$el.classList.toggle('fp-scrolled', window.scrollY > 50)
      }
      this._onScroll()
      window.addEventListener('scroll', this._onScroll, { passive: true })
    },

    destroy() {
      window.removeEventListener('scroll', this._onScroll)
    },
  }
}
