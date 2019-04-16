
<template>
  <!-- swiper -->
  <swiper :options="swiperOption" ref="mySwiper" :class="['my-swiper swiper-container-horizontal', {isVisible : visible} ]">
    <swiper-slide v-for="item, key in getVisibleFeatures" :key="item.id">
      <swiper-card :index="key" :feature="item"></swiper-card>
    </swiper-slide>
    <!-- <div class="swiper-pagination" slot="pagination"></div> -->
<!--     <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div> -->
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
    return {
      visible: false,
      swiperOption: {
        init: false,
        /* effect: 'coverflow', */
        /* slidesPerView: 5, */
        slidesPerView: 'auto',
        slidesPerColumnFill: 'column',
        spaceBetween: 0,
        direction: "horizontal",
        centeredSlides: "true",
        loop: false, // @see https://github.com/surmon-china/vue-awesome-swiper/issues/440.
        //loopedSlides: 1,
        slideToClickedSlide: "true",
        slideActiveClass: "swiper-slide-active",
        lazy: {
          loadPrevNext: true,
        },
        mousewheel: {
          invert: true,
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev"
        },
/*         breakpoints: {
          9000: {
            slidesPerView: 7
          },
          1920: {
            slidesPerView: 6
          },
          1680: {
            slidesPerView: 4
          },
          1024: {
            slidesPerView: 4
          },
          768: {
            slidesPerView: 3
          },
          640: {
            slidesPerView: 2
          },
          320: {
            slidesPerView: 2,
            spaceBetween: 10
          }
        } */
      }
    };
  },
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
    getVisibleFeatures: function(){

      return this.getAllFeatures;
/*       //getVisibleFeatures: state => state.collection.features.slice(0, 3),
      var vIds = this.getVisibleFeatureIds;
      var features = this.getFeatures( { "post_id": this.getVisibleFeatureIds} );
      //debugger;
      return features; */
    },
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

    swiper.on('init', function() { 
      setTimeout(function(){
        component.visible = true;
      }, 2000); 
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


    swiper.on("slideChangeTransitionEnd", ()=> {
     //console.log('update');
      //swiper.update();
    });
  },
  watch: {
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
  position: absolute;
  box-sizing: border-box;

}
.swiper-container.moved .swiper-wrapper {
  margin-top: -100vw;
}

.swiper-slide { 
  transform: scale(0.85);
  transition: all 0.3s;
}
.swiper-slide-active {
  transform: scale(1.0);
  /* margin: 0 10px !important; */
}

/** horizontal **/

.swiper-container-horizontal {

  right: 0;
  bottom: 0;
  max-width: 100%;
  width: 100%;
  width: 100vw;

}

.swiper-container-horizontal .swiper-slide {
  width: 320px;
  max-width: 75vw;
}

.md-card.swiper-card {
  max-width: 100%;
}

.swiper-container .swiper-wrapper {
  margin-bottom: 20px;
}

/** vertical **/
.my-swiper.swiper-container-vertical {
  /* max-width: 200px; */
  right: 0;
  height: 100vh;
  max-height: 100%;
  margin-right: 40px;
}
.swiper-container-vertical .swiper-slide {
  height: 180px;
  width: 300px
}


</style>
