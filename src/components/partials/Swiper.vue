<template>
  <!-- ... -->
  <div v-bind:class="['swiper-container my-swiper' ]">
    <div class="swiper-wrapper">
      <!-- It is important to set "left" style prop on every slide -->
      <div
        v-for="(item, key) in getVisibleFeatures"
        :key="key"
        style="left:0"
        v-bind:class="[{ 'swiper-slide-active': isActiveSlide(key) }, 'swiper-slide', { 'hidden' : isHidden } ]"
      >
        <swiper-card :index="key" :feature="item"></swiper-card>
      </div>
    </div>
  </div>
  <!-- ... -->
</template>
<script>
/* import "swiper/dist/css/swiper.css";
import Swiper from "swiper/dist/js/swiper.esm.bundle"; */
import { mapGetters, mapState, mapMutations } from 'vuex'
import SwiperCard from './SwiperCard.vue'
import * as types from '../../store/mutation-types'

export default {
  data () {
    return {
      swiper: false,
      isHidden: true,
      virtualData: {
        slides: []
      }
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

      /** wait until vue has actually rendered the features. */
      // console.log('initSwiper', features, self.swiperDirection)
      self.swiper = new Swiper('.swiper-container', {
        init: true, // init things later...
        speed: 400,
        spaceBetween: 0,
        slidesPerView: 'auto',
        centeredSlides: 'true',
        slidesPerColumnFill: 'column',
        virtual: {
          slides: self.getVisibleFeatures,
          renderExternal (data) {
            // assign virtual slides data
            self.virtualData = data
          }
        },
        /* slideActiveClass: "swiper-slide-active", */
        direction: self.swiperDirection,
        // loop: true, // @see https://github.com/surmon-china/vue-awesome-swiper/issues/440.
        lazy: {
          loadPrevNext: true
        },
        mousewheelSensitivity: 2,
        mousewheel: {
          invert: true,
          forceToAxis: true
        }
        /* slideToClickedSlide: "true", */
        /*       breakpoints: {
          1024: {
            direction: "horizontal"
          },
          9000: {
            direction: "vertical"
          }
        }, */
      })

      setTimeout(() => {
        self.isHidden = false
        // self.swiper.changeDirection(self.swiperDirection);
        console.log('show swiper')
      }, 1000)

      // self.swiper.init();
      self.swiper.slideTo(3)

      self.swiper.on('slideChange', () => {
        self.$store.commit(types.FEATURE_SELECTED, self.swiper.realIndex)
      })
    },
    isActiveSlide (index) {
      return index == this.swiper.realIndex
    }
  },
  watch: {
    getAllFeatures (features) {
      if (this.swiper) {
        console.log('already setup')
        return
      }
      const self = this
      setTimeout(() => {
        self.initSwiper(features)
      }, 80)
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
