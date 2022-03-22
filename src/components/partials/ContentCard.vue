<template>
  <!-- todo: first load in private window. go on: get feature is doing the wrong thing with the wrong name. update. selected feature? who selects when? -->
  <md-card class="content-card" v-bind:class="[feature_img ? 'feature' : 'no_feature']">
    <md-card-media v-if="feature_img">
      <img :src="feature_img">
    </md-card-media>
    <md-button
      @click.native="router.push('/')"
      class="md-primary md-plain md-mini md-fab md-fab-top-right close"
    >
      <md-icon>close</md-icon>
    </md-button>

    <slot></slot>

    <transition
      name="loader-animation"
      enter-active-class="animated fadeIn"
      leave-active-class="animated fadeOut"
    >
      <md-progress-bar v-if="loading" md-mode="indeterminate"></md-progress-bar>
    </transition>
    <md-card-actions>
      <md-button v-if="edit_url" :href="unescape(edit_url)">Edit</md-button>
      <md-button @click.native="router.push('/')">Close</md-button>
    </md-card-actions>
  </md-card>
</template>

<script>
// @see https://vuematerial.io/components/card/
/**
 * @todo: select the right feature!
 */
import { mapGetters } from 'vuex'
import Loader from './Loader.vue'
import SETTINGS from '../../settings'
import PostContent from './PostContent.vue'
import * as types from '../../store/mutation-types.js'
import ProgressBar from './ProgressBar.vue'

export default {
  data () {
    return {
      router: this.$router
    }
  },
  props: ['feature_img', 'edit_url', 'loading'],
  methods: {
    unescape (url) {
      return _.unescape(url)
    }
  },
  mounted () {
    this.$store.commit(types.VISIBLE_SINGLE, true)
  },
  update () {
    this.$store.commit(types.VISIBLE_SINGLE, true)
  },
  beforeDestroy () {
    this.$store.commit(types.VISIBLE_SINGLE, false)
  },
  computed: {
    ...mapGetters([])
  },
  components: {
    Loader
  }
}
</script>
<style>
.card-title {
  margin-top: 10px;
}
.no_feature.content-card .md-card-header {
  margin-right: 50px !important;
}

.content-card .md-fab {
  margin: 0;
}

.md-fab.md-fab-top-right {
  right: 15px;
  top: 15px;
}

.md-chip {
  margin: 0 5px 6px 0 !important;
}

.content-card {
  padding-bottom: 0;
  width: calc(100vw - 40px);
  max-width: 620px;
  margin: calc(1% + 10px);
  margin-top: 100px;
  background: #fff;
}
.vertical .content-card {
  margin-bottom: 120px;
}

.content-card .md-card-media img {
  max-height: 60vh;
  object-fit: cover;
}

@media only screen and (max-width: 600px) {
  .content-card {
    width: 100%;
    margin: 0;
    margin-top: 200px;
  }
  .content-card .md-card-media img {
    max-height: 35vh;
  }
  .no_feature.content-card .md-card-header {
    padding-top: 25px;
    margin-right: 0px !important;
  }
  .close {
    top: -20px !important;
  }
}
</style>
