<template>
  <div v-bind:class="['progress loader', {loading: showLoader }]">
    <div
      class="progress-bar"
      role="progressbar"
      :style="loaderStyle"
      aria-valuenow="100"
      aria-valuemin="0"
      aria-valuemax="100"
    ></div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data () {
    return {
      showLoader: true
    }
  },
  computed: {
    ...mapGetters(['loadingProgress', 'isLoading']),
    loaderStyle () {
      return `width: ${this.loadingProgress}%;`
    }
  },
  watch: {
    // watch the value of isLoading and once it's false hide the loader
    isLoading (val) {
      console.log('isLoading', val)
      // self.showLoader = val;
      const self = this
      if (val == false) {
        setTimeout(() => {
          self.showLoader = false
        }, 1500)
      } else {
        setTimeout(() => {
          self.showLoader = true
        }, 100)
      }
    }
  }
}
</script>
<style>
.loader {
  /*   transition-delay: 2s;
  transition: all 0.1s; */
  position: fixed;
  top: 0;
  width: 100%;
  height: 0px;
  z-index: 1000000;
}
.loader.loading {
  height: 4px;
}
.loading .progress-bar {
  transition: all 1.3s;
}
.loader .progress-bar {
  height: 100%;

  background-color: var(--md-theme-z-primary, orange-red);
}

/* .loader-animation-leave-active {
  transition: delay 1s;
} */
</style>
