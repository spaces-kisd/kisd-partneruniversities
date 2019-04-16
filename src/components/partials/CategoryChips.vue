<template>
  <div class="md-layout">
    <div v-for="category in categories">
      <div>
        <!-- https://vuematerial.io/components/chips -->
        <a v-if="getCat(category)" :href="getCat(category).link">
          <md-chip :class="[getCat(category).slug, 'category']" :title="getCat(category).description" md-clickable>{{ unescape(getCat(category).name) }}</md-chip>
        </a>
      </div>
    </div>
    <!--
	The way it's supposed to be:
	<div class="md-layout-item">
      <div>
        <p class="md-caption">Business Model</p>
        <md-chip>Something</md-chip>
        <md-chip>Something</md-chip>
        <md-chip>Something</md-chip>
      </div>
    </div>-->
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  props: ["categories"],
  computed: {
    ...mapGetters(["getCategory"])
  },
  methods: {
    getCat(catId) {
      return this.getCategory(catId);
    },
    unescape( $c ){
      return _.unescape($c);
    }
  }
};
</script>

