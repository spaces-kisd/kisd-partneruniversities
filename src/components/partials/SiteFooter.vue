<template>
  <transition name="fade">
    <footer v-if="footer" class="site-footer" v-html="footer"></footer>
  </transition>
</template>

<script>
import axios from 'axios'
import SETTINGS from '../../settings'

export default {
  data () {
    return {
      footer: false
    }
  },
  computed: {},
  beforeMount () {
    this.getFooter()
  },
  methods: {
    getFooter () {
      axios
        .get('/wp-json/map/v1/menus/' + 'footer')
        .then((response) => {
          this.footer = response.data
        })
        .catch((e) => {
          console.log(e)
        })
    }
  },
  components: []
}
</script>
<style>
.menu-footer-container {
  padding-bottom: 15px;
  overflow: auto;
}
.menu-footer-container li {
  list-style: none;
  float: left;
  padding-right: 5px;
}

.menu-footer-container li~li:before {
  content: '  |  ';
  color: #666;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
