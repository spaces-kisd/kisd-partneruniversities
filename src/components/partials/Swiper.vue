
<template>
  <!-- swiper -->
  <swiper :options="swiperOption" class="my-swiper">
    <!-- <a :href="post.link">{{ post.title.rendered }}</a> -->
    <!--  <div class="swiper-wrapper" v-if="recentPostsLoaded"> -->
    <swiper-slide v-for="item, key in getFeatures" :key="item.id">
      <swiper-card :feature="item"></swiper-card>
    </swiper-slide>
    <!-- </div> -->
    <!-- <swiper-slide><preview-card title="abc"></preview-card></swiper-slide>
        <swiper-slide><preview-card title="asfdasdf"></preview-card></swiper-slide>
        <swiper-slide><preview-card title="asdfasdf"></preview-card></swiper-slide>
        <swiper-slide><preview-card title="fasdfc"></preview-card></swiper-slide>
        <swiper-slide><preview-card title="asdfc"></preview-card></swiper-slide>
        <swiper-slide><preview-card title="abc"></preview-card></swiper-slide>
        <swiper-slide><preview-card title="fasdc"></preview-card></swiper-slide>
        <swiper-slide><preview-card title="abc"></preview-card></swiper-slide>
        <swiper-slide><preview-card title="afwefwefwec"></preview-card></swiper-slide>
    <swiper-slide><preview-card title="abc"></preview-card></swiper-slide>-->
    <div class="swiper-pagination" slot="pagination"></div>
  </swiper>
</template>

<script>
// @see http://idangero.us/swiper/api/
// https://github.com/surmon-china/vue-awesome-swiper
import "swiper/dist/css/swiper.css";
import SwiperCard from "./SwiperCard";
import { swiper, swiperSlide } from "vue-awesome-swiper";
import { mapGetters, mapState } from "vuex";

export default {
  data() {
    return {
      swiperOption: {
        slidesPerView: 3,
        spaceBetween: 10,
        direction: "horizontal",
        centeredSlides: "true",
        slideToClickedSlide: "true",
        slideActiveClass: 'swiper-slide-active',
        /* pagination: {
          el: ".swiper-pagination",
          clickable: true*/
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev"
        },
        breakpoints: {
          9000: {
            slidesPerView: 5
          },
          1024: {
            slidesPerView: 5
          },
          768: {
            slidesPerView: 3
          },
          640: {
            slidesPerView: 2
          },
          320: {
            slidesPerView: 1,
            spaceBetween: 10
          }
        }
      }
    };
  },
  components: {
    //swiper: swiper,
    SwiperCard
  },
  computed: {
    ...mapState(['feature.collection']),
    ...mapGetters({
      isLoading: "isLoading",
      recentPosts: "recentPosts",
      getFeatures: "getFeatures",
      featuresLoaded: "featuresLoaded",
      recentPostsLoaded: "recentPostsLoaded"
    })
  },
  methods: {
    clickSlide: function(post) {
      console.log(post);
    }
  },
  mounted() {
    var component = this;

    console.log('this',this.$store.getters);

    this.$store.dispatch("getFeatureCollection");
    this.swiper = this.$el.swiper;
    this.swiper.on('resize', function(){
      console.log('feat', component.$store.getters.getFeatures );
      console.log(component.$el.offsetWidth);
    });
  },
  watch: {
    isLoading(val) {
      console.log('watching');
      if (val == false) {
        let self = this;
        setTimeout(function() {
          //console.log(self.post);
          console.log(document.querySelector(".my-swiper").swiper.update()); //.my-swiper.swiper.update());
          console.log("update!");
        }, 10);
      }
    }
  }
};
</script>
<style lang="scss">
.my-swiper {
  right: 0;
  position: absolute;
  bottom: 0;
  max-width: 100%;
  width: 100%;
  box-sizing: border-box;
  &.swiper-container-horizontal {
    width: 100vw;
  }
  &.swiper-container-vertical {
    max-width: 200px;
    right: 0;
    .swiper-slide {
      max-height: 40vh;
    }
  }
}
.swiper-slide .md-card {
  margin: 10px;
}
.swiper-slide {
  max-width: 100vw;
  /*height: 60px;*/
  text-align: center;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
}
.swiper-slide-active {
  /*background-color: red;*/
}
</style>
