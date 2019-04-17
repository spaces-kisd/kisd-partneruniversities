
<template>
  <!-- swiper -->
  <swiper
    :options="swiperOption"
    ref="mySwiper"
    :class="['my-swiper', {isVisible : true} ]"
  >
    <swiper-slide v-for="item, key in getVisibleFeatures" :key="item.id">
      <swiper-card :index="key" :feature="item"></swiper-card>
    </swiper-slide>
 <!--    <div class="swiper-scrollbar" slot="scrollbar"></div> -->
    <!-- <div class="swiper-pagination" slot="pagination"></div> -->
  </swiper>
</template>

<script>
// @see http://idangero.us/swiper/api/
// https://github.com/surmon-china/vue-awesome-swiper
import "swiper/dist/css/swiper.css";
import SwiperCard from "./SwiperCard.vue";
import { swiper, swiperSlide } from "vue-awesome-swiper";
import { mapGetters, mapState, mapMutations } from "vuex";
import * as types from "../../store/mutation-types";

export default {
  data() {
    // see: https://idangero.us/swiper/api/#mousewheel
    return {
      visible: false,
      swiperOption: {
        init: false,
        /* effect: 'coverflow', */
        /* slidesPerView: 5, */
        slidesPerView: "auto",
        slidesPerColumnFill: "column",
        spaceBetween: 0,
        direction: 'horizontal', //'horizontal'
        centeredSlides: "true",
        loop: false, // @see https://github.com/surmon-china/vue-awesome-swiper/issues/440.
        //observer: true,
/*         scrollbar: {
          el: ".swiper-scrollbar",
          draggable: true
        }, */
        speed: 500,
        effect: 'scroll',
        //loopedSlides: 1,
        slideToClickedSlide: "true",
        slideActiveClass: "swiper-slide-active",
        lazy: {
          loadPrevNext: true
        },
        mousewheelControl: true,
        mousewheel: {
          invert: true,
          forceToAxis: true,
          /*  sensitivity:0.5, */
        },
        //freeMode: true,
        pagination: {
          el: ".swiper-pagination",
          clickable: true
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev"
        },
        breakpoints: {
          1024: {
            direction: 'horizontal'
          },
          9000: {
            direction: 'vertical'
          }
        }
      }
    };
  },
  props: ['swiperDirection'],
  components: {
    //swiper: swiper,
    SwiperCard
  },
  computed: {
    ...mapState(["feature.collection"]),
    ...mapGetters([
      "recentPosts",
      "getAllFeatures",
      "getFeatures",
      //"getVisibleFeatures",
      "getVisibleFeatureIds",
      "featuresLoaded",
      "recentPostsLoaded",
      "getSelected"
    ]),
    ...mapMutations({
      setSlide: types.FEATURE_SELECTED
    }),
    getVisibleFeatures: function() {
      return this.getAllFeatures;
      /*       //getVisibleFeatures: state => state.collection.features.slice(0, 3),
      var vIds = this.getVisibleFeatureIds;
      var features = this.getFeatures( { "post_id": this.getVisibleFeatureIds} );
      //debugger;
      return features; */
    }
  },
  methods: {
    clickSlide: function(post) {
      //console.log(post);
    },
    getSwiper: context => {
      return context.$refs.mySwiper.swiper;
    } //this.$el.swiper;
  },
  mounted() {
    var component = this;
    //console.log('this',this.$store.getters);
    this.$store.dispatch("getFeatureCollection");

    var swiper = this.getSwiper(this);

    swiper.on("init", function() {
      setTimeout(function() {
        component.visible = true;
      }, 1000);
    });
    // init Swiper
    swiper.init();
    swiper.slideTo(3);
    swiper.on("resize", function() {
      //console.log('feat', component.$store.getters.getFeatures );
      //console.log(component.$el.offsetWidth);
    });

    swiper.on("slideChange", () => {
      component.$store.commit(types.FEATURE_SELECTED, swiper.realIndex);
    });
    swiper.on("slideChange", () => {
      component.$store.commit(types.FEATURE_SELECTED, swiper.realIndex);
    });

    swiper.on("slideChangeTransitionEnd", () => {
      //console.log('update');
      //swiper.update();
    });
  },
  watch: {
    swiperDirection: function(val){
      console.log('changed to', val, this.getSwiper(this));
      //this.getSwiper(this).changeDirection(val);
      //this.getSwiper(this).params.direction = 'vertical';
      //this.getSwiper(this).update();

    },
    getSelected(val) {
      if (this.getSwiper(this).realIndex != val) {
        //this.getSwiper(this).slideTo(val);
        this.getSwiper(this).slideToLoop(val);
      }
    }
  }
};
</script>
<style>
.my-swiper {
  transition: opacity 5s;
  opacity: 0;
  transition-timing-function: linear;
  transition-duration: 0.5s;
  transition-property: opacity;
  transition-delay: 0.5s;
}
.my-swiper.isVisible {
  opacity: 1;
  z-index: 999;
  transition-delay: 0.5s;
/*   position: absolute; */
  box-sizing: border-box;
}
/* .swiper-container.moved .swiper-wrapper {
  margin-top: -100vw;
} */


.swiper-slide {
  transform: scale(0.85);
  transition: all 0.3s;
}
.swiper-slide-active {
  transform: scale(1);
  /* margin: 0 10px !important; */
}

/** horizontal **/
.horizontal .my-swiper {
  margin-top: 200px;
  right: 0;
  max-width: 100%;
  width: 100%;
  width: 100vw;
}

.horizontal .swiper-slide {
  width: 320px;
  max-width: 62vw;
}

.md-card.swiper-card {
  max-width: 100%;
}

.swiper-container .swiper-wrapper {
  margin-bottom: 20px;
}

/** vertical **/
.vertical .my-swiper {
  /* max-width: 200px; */
  position: fixed;
  top: 0;
  right: 0;
  /* height: 100vh; */
  max-height: 100%;
  padding-right: 40px;
}
.vertical .swiper-slide {
  height: 180px;
  width: 300px;
}
</style>
