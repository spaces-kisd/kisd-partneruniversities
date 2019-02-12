<template>
  <md-card class="post">
    <md-card-header v-if="post">
      <h1>A Post! {{ post.title.rendered }}</h1>
    </md-card-header>
    <Loader v-else/>
    <md-card-content>
      <div v-html="content"></div>
    </md-card-content>
    <md-card-actions>
      <md-button @click.native="router.push('/')">Back</md-button>
    </md-card-actions>
    <!--  <b-container class="bv-example-row pt-4"> -->
    <!--     <template>
      
   
    </template>
    </b-container>-->
  </md-card>
</template>

<script>
// @see https://vuematerial.io/components/card/
import Loader from "../partials/Loader.vue";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      post: false,
      content: "",
      router: this.$router
    };
  },

  computed: {},
  beforeMount() {
    this.getPost();
  },

  methods: {
    getPost: function() {
      axios
        .get(
          window.SETTINGS.API_BASE_PATH +
            "solutions?slug=" +
            this.$route.params.postSlug
        )
        .then(response => {
          this.post = response.data[0];
          this.content = this.post.content.rendered;
        })
        .catch(e => {
          console.log(e);
        });
    }
  },

  components: {
    Loader
  }
};
</script>
<style lang="scss">
.md-card.post {
  max-width: 800px;
  margin: 20px;
}
</style>
