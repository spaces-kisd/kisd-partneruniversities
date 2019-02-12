<template>
  <!--   <b-container class="bv-example-row pt-4">
    <template v-if="allPagesLoaded">
      <h1>{{ pageContent.title.rendered }}</h1>
      <div v-html="pageContent.content.rendered"></div>
    </template>
    <Loader v-else />
  </b-container>-->
  <md-card class="post" md-with-hover>
    <md-card-header v-if="allPagesLoaded">
      <h1>{{ pageContent.title.rendered }}</h1>
    </md-card-header>
    <Loader v-else/>
    <md-card-content>
      <div v-html="pageContent.content.rendered"></div>
    </md-card-content>
    <!--  <b-container class="bv-example-row pt-4"> -->
    <!--     <template>
      
   
    </template>
    </b-container>-->
  </md-card>
</template>

<script>
import Loader from "../partials/Loader.vue";
import { mapGetters } from "vuex";

export default {
  computed: {
    ...mapGetters({
      page: "page",
      allPagesLoaded: "allPagesLoaded"
    }),

    pageContent() {
      return this.page(this.$route.params.pageSlug);
    }
  },

  components: {
    Loader
  }
};
</script>
