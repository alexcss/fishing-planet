import Swiper from 'swiper'
import { Navigation } from 'swiper/modules'
import 'swiper/css'

export default function introSlider() {
  return {
    swiper: null,
    videos: [],

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
      this.swiper = new Swiper(this.$refs.swiperContainer, {
        modules: [Navigation],
        loop: false,
        speed: 600,
        effect: 'fade',
        fadeEffect: {
          crossFade: true,
        },
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
      })
    },

    updatePagination(swiper) {
      if (this.$refs.currentSlide) {
        this.$refs.currentSlide.textContent = swiper.realIndex + 1
      }
    },

    handleVideoPlayback(swiper) {
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
