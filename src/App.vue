<template>
  <div id="my-app" :class="['page-wrapper', horizontalOrVertial]">
    <transition
      name="loader-animation"
      enter-active-class="animated fadeIn"
      leave-active-class="animated fadeOut"
    >
      <progress-bar />
    </transition>
    <drawer :visible="visible_drawer" />

    <div :class="['logo-container top', { hidden : visible_drawer }]" style="z-index:1">
      <md-button
        aria-label="main-menu-button"
        class="md-icon-button md-raised md-primary"
        @click.native="router.push('/home')"
      >
        <md-icon>menu</md-icon>
      </md-button>
      <!-- md-display-1 -->
    </div>
    <div class="content-extend">
      <transition name="fade-slide-left" mode="out-in" appear>
        <router-view :key="$route.params.postSlug"></router-view>
      </transition>
    </div>

    <!-- <transition name="fade" mode="out-in"> -->
    <swiper v-bind:swiperDirection="horizontalOrVertial" />
    <!-- </transition> -->

    <solution-map />
  </div>
</template>

<script>
/**
 * todo: axios/isURLSameOrigin.js <- can we use this?
 *
 *
 * Think about performance (size), bundle.js
 * - mapbox.gl -> via cdn. it's okay if it loads later
 * - swiper -> via cdn.
 * - lodash
 * - vue material
 *
 * Move custom zerowaste-styles to a seperate css file and include it in the backend (gutenberg).
 *
 *
 */

import { mapGetters, mapActions, mapMutations } from "vuex";
import ProgressBar from "./components/partials/ProgressBar.vue";
import SolutionMap from "./components/partials/SolutionMap.vue";
import Drawer from "./components/partials/Drawer.vue";
import Swiper from "./components/partials/Swiper.vue";
import * as types from "./store/mutation-types.js";
import Header from "./components/partials/Header.vue";
// import Footer from "./components/partials/Footer.vue";

export default {
  data() {
    return {
      showLoader: true,
      router: this.$router,
      windowWidth: 0
    };
  },
  computed: {
    ...mapGetters(["frontPage", "visible_drawer", "visible_single"]),
    horizontalOrVertial() {
      let hv = this.windowWidth < 1280 ? "horizontal" : "vertical";
      console.log(hv);
      return hv;
    }
  },
  beforeMount() {
    /* this.$store.commit(types.VISIBLE_SINGLE, false);
    this.$store.commit(types.VISIBLE_DRAWER, false); */
  },
  methods: {
    updateWindowWidth(event) {
      this.windowWidth = document.documentElement.clientWidth;
    }
  },
  mounted() {
    this.$nextTick(function() {
      window.addEventListener(
        "resize",
        _.debounce(this.updateWindowWidth, 300)
      );
      //Init
      this.updateWindowWidth();
    });
    // close all the things on pressing esc.
    document.body.addEventListener("keyup", e => {
      if (e.keyCode === 27) {
        //esc.
        this.$router.push("/");
      }
    });
    window.addEventListener("click", event => {
      const { target } = event;
      let link = target.closest("a");

      //link.matches("a:not([href*='://'])")
      if (!link) return;
      if (!link.href) return;

      // some sanity checks taken from vue-router:
      // https://github.com/vuejs/vue-router/blob/dev/src/components/link.js#L106
      const {
        altKey,
        ctrlKey,
        metaKey,
        shiftKey,
        button,
        defaultPrevented
      } = event;
      // don't handle with control keys
      if (metaKey || altKey || ctrlKey || shiftKey) return;
      // don't handle when preventDefault called
      if (defaultPrevented) return;
      // don't handle right clicks
      if (button !== undefined && button !== 0) return;
      // don't handle if `link="_blank"`
      if (link && link.getAttribute) {
        const linkTarget = link.getAttribute("target");
        if (/\b_blank\b/i.test(linkTarget)) return;
      }

      const url = new URL(link.href);
      const to = url.pathname;

      // open external links and wp-admin-links in new window.
      if (
        link.matches("a[href*='/wp-admin/']") ||
        url.host != window.location.host
      ) {
        event.preventDefault();
        window.open(url, "_blank");
        return;
      }

      // don't handle same page links/anchors
      if (window.location.pathname !== to && event.preventDefault) {
        event.preventDefault();
        this.$router.push(to);
      }
    });
  },
  components: {
    ProgressBar,
    SolutionMap,
    Drawer,
    Swiper,
    appHeader: Header
    //appFooter: Footer
  },
  watch: {
    visible_drawer(val) {
      return val;
    }
  }
};
</script>
<style>
#my-app {
  display: flex;
  min-height: 100%;
  flex-direction: column;
}
#my-app.vertical {
  flex-direction: row;
}

#my-app > * {
  flex-shrink: 0;
}

#my-app .content-extend {
  flex: 1 0 auto;
  overflow: auto;
}

#my-app {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
  transition-delay: 2s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
  transition-delay: 2s;
}

.fade-slide-left-enter-active {
  transition: all 0.5s ease;
}
.fade-slide-left-leave-active {
  transition: all 0.5s ease;
}
.fade-slide-left-enter {
  transform: translateX(-700px);
  opacity: 0;
}
.fade-slide-left-leave-to {
  transform: translateX(1700px);
  opacity: 0;
}

.top {
  margin-top: 15px;
  margin-left: calc(1% + 26px);

  font-size: 20px;
  position: fixed;
}
.top .title {
  color: #555;
}
.collapse {
  position: absolute;
  width: 100px;
  height: 10px;
  right: 0;
}

.content {
  margin: 20px;
  max-width: 600px;
}

#my-app {
  max-width: 100vw;
}

#wpadminbar {
  display: none;
}
.site-content {
  margin: 0;
  max-width: 900px;
  padding: 1%;
  padding-top: 10%;
}
</style>
