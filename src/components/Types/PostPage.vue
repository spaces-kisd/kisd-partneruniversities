<template>
  <!--   <b-container class="bv-example-row pt-4">
    <template v-if="allPagesLoaded">
      <h1>{{ pageContent.title.rendered }}</h1>
      <div v-html="pageContent.content.rendered"></div>
    </template>
    <Loader v-else />
  </b-container>-->
  <content-card
    v-if="localContent"
    :loading="! localContent"
    :edit_url="localContent.edit_url"
    :feature_img="localContent.feature.large"
  >
    <md-card-header v-if="localContent">
      <div class="md-display-1">{{ localContent.title.rendered }}</div>
    </md-card-header>

    <md-card-content>
      <post-content :content="localContent.content.rendered" :options="{showAuthor: 'eee'}"/>
    </md-card-content>
  </content-card>
</template>

<script>
import Loader from "../partials/Loader.vue";
import { mapGetters } from "vuex";
import ContentCard from "../partials/ContentCard.vue";
import PostContent from "../partials/PostContent.vue";
import * as types from "../../store/mutation-types";

export default {
  data() {
    return {
      router: this.$router
    };
  },
  props: ['postType'], //either posts or pages. plural! passed via router.
  computed: {
    ...mapGetters(["getPosts"]),
    localContent() {
      let posts = this.getPosts({ slug: this.$route.params.slug });
      if ( ! _.isEmpty(posts) ){
        console.log(posts)
        this.$store.commit(types.INCREMENT_LOADING_PROGRESS);
        return _.first(posts);
      }
    }
  },
  mounted(){
    /* this.fetch(); */
    this.fetch();
  },
  //this is the right hook for a link change with the same component (not triggering when leaving the comp)
  beforeRouteUpdate(to, from, next) {
    next();
    this.fetch();
  },
  methods: {
    fetch(){
      if (_.isEmpty(this.localContent)) {
        this.$store.commit(types.RESET_LOADING_PROGRESS);
        console.log('RESET_LOADING_PROGRESS');
        this.$store.dispatch("fetchPostTypes", {
          type: this.postType,
          slug: this.$route.params.slug
        });
      }
    }
  },
  components: {
    Loader,
    ContentCard,
    PostContent
  }
};
</script>
