<template>
  <!-- ... -->
  <div
    ref="swiperRoot"
    :class="['swiper swiper-container my-swiper' ]"
  >
    <div class="swiper-wrapper">
      <!-- It is important to set "left" style prop on every slide -->
      <div
        v-for="(item, key) in getVisibleFeatures"
        :key="key"
        style="left:0"
        :class="[{ 'swiper-slide-active': isActiveSlide(key) }, 'swiper-slide', { 'hidden' : isHidden } ]"
      >
        <swiper-card
          :index="key"
          :feature="item"
        />
      </div>
    </div>
  </div>
  <!-- ... -->
</template>
<script>
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';
import { mapGetters, mapState, mapMutations } from 'vuex'
import SwiperCard from './SwiperCard.vue'
import * as types from '../../store/mutation-types'

export default {
  data () {
    return {
      swiper: false,
      isHidden: true
    }
  },
  beforeDestroy () {
    if (this.swiper && this.swiper.destroy) {
      this.swiper.destroy(true, true)
    }
  },
  mounted () {
    this.$store.dispatch('getFeatureCollection')
  },
  props: ['swiperDirection'],
  components: {
    // swiper: swiper,
    SwiperCard
  },
  computed: {
    ...mapState(['feature.collection']),
    ...mapGetters([
      'recentPosts',
      'getAllFeatures',
      'getFeatures',
      'getVisibleFeatureIds',
      'featuresLoaded',
      'recentPostsLoaded',
      'getSelected'
    ]),
    ...mapMutations({
      setSlide: types.FEATURE_SELECTED
    }),
    getVisibleFeatures () {
      // console.log('gaf', this.getAllFeatures)
      return this.getAllFeatures // .slice(0,25);
      /*       //getVisibleFeatures: state => state.collection.features.slice(0, 3),
      var vIds = this.getVisibleFeatureIds;
      var features = this.getFeatures( { "post_id": this.getVisibleFeatureIds} );
      //debugger;
      return features; */
    }
  },
  methods: {
    initSwiper (features) {
      const self = this
      const swiperRoot = self.$refs.swiperRoot

      if (!swiperRoot) {
        return
      }

      /** wait until vue has actually rendered the features. */
      // console.log('initSwiper', features, self.swiperDirection)
      self.swiper = new Swiper(swiperRoot, {
        speed: 400,
        spaceBetween: 0,
        slidesPerView: 'auto',
        centeredSlides: true,
        direction: self.swiperDirection,
        lazy: {
          loadPrevNext: true
        },
        mousewheel: {
          invert: true,
          forceToAxis: true
        },
        on: {
          slideChange () {
            self.$store.commit(types.FEATURE_SELECTED, self.swiper.realIndex)
          }
        }
      })

      setTimeout(() => {
        self.isHidden = false
        // self.swiper.changeDirection(self.swiperDirection);
        console.log('show swiper')
      }, 1000)

      if (self.swiper && self.swiper.slideTo) {
        self.swiper.slideTo(3)
      }
    },
    isActiveSlide (index) {
      return index == this.swiper.realIndex
    }
  },
  watch: {
    getAllFeatures (features) {
      if (!features || !features.length) {
        return
      }
      if (this.swiper) {
        console.log('already setup')
        return
      }
      this.$nextTick(() => {
        this.initSwiper(features)
      })
    },
    swiperDirection (val) {
      console.log('changed to', val, this.swiper)
      if (this.swiper) {
        this.swiper.changeDirection(val)
      }
    },
    getSelected (val) {
      if (!this.swiper) {
        return 0
      }
      if (this.swiper.realIndex != val) {
        this.swiper.slideToLoop(val)
      }
    }
  }
}
</script>

<style>
.my-swiper {
  margin-bottom: 25px;
  box-sizing: border-box;
}

.swiper-slide {
  opacity: 1;
  transform: scale(0.87);
  transition: all 0.3s;
}
.swiper-slide.hidden {
  opacity: 0;
}

.swiper-slide-active {
  transform: scale(1);
}

/** horizontal **/
.horizontal .my-swiper {
  right: 0;
  margin-top: 10px;
  max-width: 100%;
  width: 100vw;
}
.horizontal .swiper-slide {
  width: 320px;
  max-width: 62vw;
}

/** vertical **/
.vertical .my-swiper {
  position: fixed;
  top: 0;
  right: 0;
  height: 100vh;
  max-height: 100%;

}
.vertical .swiper-slide {
  height: 180px;
  width: 320px;
}
</style>
