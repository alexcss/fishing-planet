import Swiper from 'swiper'
import { Navigation, EffectFade, Autoplay } from 'swiper/modules'

export default function videoSlider(options = {}) {
  return {
    swiper: null,
    videos: [],
    options: options,

    init() {
      this.initVideos()
      this.initSwiper()
    },

    initVideos() {
      const slides = this.$refs.swiperContainer.querySelectorAll('[data-slide]')

      slides.forEach((slide, index) => {
        const video = slide.querySelector('video')
        if (video) {
          this.videos[index] = video

          if (index === 0) {
            video.play().catch(() => {})
          }
        }
      })
    },

    initSwiper() {
      const defaultOptions = {
        modules: [Navigation, EffectFade, Autoplay],
        loop: false,
        speed: 600,
        autoHeight: false,
        navigation: {
          nextEl: this.$refs.nextBtn,
          prevEl: this.$refs.prevBtn,
        },
        on: {
          init: (swiper) => {
            this.updatePagination(swiper)
          },
          slideChange: (swiper) => {
            this.updatePagination(swiper)
            this.handleVideoPlayback(swiper)
          },
        },
      }

      const swiperOptions = { ...defaultOptions, ...this.options }

      this.swiper = new Swiper(this.$refs.swiperContainer, swiperOptions)
    },

    updatePagination(swiper) {
      if (this.$refs.currentSlide) {
        this.$refs.currentSlide.textContent = swiper.realIndex + 1
      }
    },

    handleVideoPlayback(swiper) {
      if (this.videos.length === 0) return

      this.videos.forEach((video, index) => {
        if (video) {
          if (index === swiper.realIndex) {
            video.play().catch(() => {})
          } else {
            video.pause()
          }
        }
      })
    },
  }
}
