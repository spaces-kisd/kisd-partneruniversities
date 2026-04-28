<template>
  <transition name="fade">
    <footer
      v-if="footer"
      class="site-footer"
      v-html="footer"
    />
  </transition>
</template>

<script>
import { wpFetch } from '../../api'

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
      wpFetch('map/v1/menus/footer')
        .then((data) => {
          this.footer = data
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
