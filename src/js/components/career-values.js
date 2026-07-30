export default function careerValues(total = 0) {
  return {
    active: 0,
    total: total,

    init() {
      this.$watch('active', (value) => {
        this.scrollToActive(value)
      })
    },

    prev() {
      this.active = this.active > 0 ? this.active - 1 : this.total - 1
    },

    next() {
      this.active = this.active < this.total - 1 ? this.active + 1 : 0
    },

    scrollToActive(index) {
      const container = this.$refs.letters
      const button = container ? container.children[index] : null

      if (container && button) {
        container.scrollTo({
          left: button.offsetLeft - container.offsetLeft - (container.clientWidth / 2) + (button.clientWidth / 2),
          behavior: 'smooth'
        })
      }
    }
  }
}
