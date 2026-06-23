export default function frontPageHeader() {
  return {
    _observer: null,

    init() {
      const hero = document.querySelector('[data-hero-section]')
      if (!hero) return

      this._observer = new IntersectionObserver(
        ([entry]) => {
          this.$el.classList.toggle('lg:translate-y-0', !entry.isIntersecting)
        },
        { threshold: 0 }
      )

      this._observer.observe(hero)
    },

    destroy() {
      if (this._observer) {
        this._observer.disconnect()
        this._observer = null
      }
    },
  }
}
