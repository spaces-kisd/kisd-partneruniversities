<template>
  <md-drawer v-on:update:mdActive="active" :md-active="visible" class="md-scrollbar">
    <div class="wrapper" v-if="myFrontPage">
      <div class="drawer-thumbnail" v-if="myFrontPage.thumbnail" :style="{ backgroundImage: `url('${myFrontPage.thumbnail}')` }">
        <!-- <img :src="myFrontPage.thumbnail"> -->
      </div>
      <div class="drawer-content">
        <div class="md-display-2 drawer-title">{{myFrontPage.post_title}}</div>
        <post-content
          class="md-subheading"
          :content="myFrontPage.post_content"
          :options="{showAuthor: 'eee'}"
        />

      </div>
    </div>
    <div v-else><md-list />Loading...</div>
    <!-- <recent-posts :limit="10" /> -->
    <SiteFooter/>
  </md-drawer>
</template>

<script>
//todo: scrollbar? https://vuematerial.io/ui-elements/scrollbar
import { mapGetters, mapActions, mapMutations } from "vuex";
import PostContent from "./PostContent.vue";
import SiteFooter from "./SiteFooter.vue";
import * as types from "../../store/mutation-types.js";

export default {
  data() {
    return {};
  },
  methods: {
    active(val) {
      if (val == false) {
        this.$store.commit(types.VISIBLE_DRAWER, false);
        this.$router.push("/");
        //when we close the drawer on the home-site we move to a different url.
      }
    }
  },
  props: ["visible"],
  computed: {
    ...mapGetters(["frontPage", "allPagesLoaded"]),
    myFrontPage() {
      return this.frontPage;
    }
  },
  components: {
    PostContent,
    SiteFooter
  }
};
</script>
<style>
.drawer-title {
  padding-bottom: 15px;
}
.md-drawer {
  width: 800px;
  max-width: 100vw;
  overflow: auto;
}
.drawer-content {
  margin: 0 auto;
  padding: 30px 15px;
  max-width: 600px;
}
.wrapper {
  min-height: calc(100vh - 80px);
}

.drawer-thumbnail {
  width: 100%;
  min-height: 16vh;
  background-size: cover;
  background-position: 50% 40%;
  /*   display: flex;
  justify-content: center;
  align-items: center;  */
}
.drawer-thumbnail img {
  object-position: 50% 40%;
  object-fit: cover;
  width: 100%;
  height: 20vh;
}
</style>


