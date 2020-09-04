<template>
    <div></div>
</template>
<script>
/**
 * This just supports a route to show the drawer.
 * This way we can close the drawer with a link.
 */
import { mapGetters, mapActions, mapMutations } from 'vuex'
import * as types from '../store/mutation-types'

export default {
  mounted () {
    // console.log('mount home');
    this.$store.commit(types.VISIBLE_DRAWER, true)
  },
  beforeDestroy () {
    this.$store.commit(types.VISIBLE_DRAWER, false)
    this.$store.commit(types.VISIBLE_SINGLE, false)
  }
}
</script>

<style></style>
