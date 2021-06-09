<template>
  <content-card>
    <md-card-header>
      <div v-if="currentCategory" class="card-title md-display-1">{{unescape(currentCategory.name)}}</div>
    </md-card-header>
    <md-card-content>
      <md-card-area>
        <p v-if="currentCategory">{{currentCategory.description}}</p>
        <!-- <md-divider v-if="currentCategory.description"/> -->
      </md-card-area>
    </md-card-content>
    <md-list v-if="categoryPosts" class="cat-posts md-triple-line">
      <md-list-item v-for="item, key in categoryPosts" :key="item.id" :to="item.link_relative">
        <!--             <md-avatar>
              <img class="cat-feature" v-if="item.feature" :src="item.feature">
        </md-avatar>-->
        <div class="cat-feature-container" v-if="item.feature">
          <img class="cat-feature" :src="item.feature.thumbnail" />
        </div>
        <!--             <div class="cat-feature-text">
              <a class="md-subheading" :href="item.link">{{item.title.rendered}}</a>
              <div v-html="item.excerpt.rendered"></div>
        </div>-->
        <div class="md-list-item-text">
          <div class="md-title">{{item.title.rendered}}</div>
          <span v-if="item.full_name">{{item.full_name}}</span>
          <div v-html="item.excerpt.rendered"></div>
          <!-- <p>I'll be in your neighborhood doing errands this week. Do you want to meet?</p> -->
        </div>
      </md-list-item>
      <!-- <md-divider class="md-inset"></md-divider> -->
    </md-list>
  </content-card>
</template>

<script>
// lists: https://vuematerial.io/components/list
import { mapGetters } from 'vuex'
import Loader from '../partials/Loader.vue'
import ContentCard from '../partials/ContentCard.vue'

export default {
  data () {
    return {
      router: this.$router,
      loadingDispatched: false
    }
  },
  methods: {
    unescape ($c) {
      return _.unescape($c)
    }
  },
  computed: {
    ...mapGetters(['page', 'allPagesLoaded', 'getPosts', 'getCategories']),
    currentCategory () {
      return _.first(
        this.getCategories({ slug: this.$route.params.categorySlug })
      )
    },
    categoryPosts () {
      const cat = this.currentCategory
      if (!_.isUndefined(cat)) {
        // var postType = cat.
        const { id } = cat

        // todo: refactor! think about the naming of the taxonomy! the post-type name is here: wp:post_type
        if (cat.taxonomy == 'category') {
          if (!this.loadingDispatched) {
            this.loadingDispatched = true
            this.$store.dispatch('fetchPostTypes', {
              type: 'posts', // type: "solutions",
              categories: id,
              per_page: 99
            })
          }
          var getPosts = this.getPosts({
            categories: id
          })
        } else {
          if (!this.loadingDispatched) {
            this.loadingDispatched = true
            this.$store.dispatch('fetchPostTypes', {
              type: 'solutions',
              categories: id,
              per_page: 99
            })
          }
          var getPosts = this.getPosts({
            solution_categories: id
          })
          if (!_.isUndefined(getPosts)) {
            getPosts.sort((a, b) => b.priority - a.priority)
          }
          console.log(getPosts)
        }

        return getPosts
      }
    }
  },
  beforeRouteUpdate (to, from, next) {
    next()
  },
  components: {
    Loader,
    ContentCard
  }
}
</script>
<style>
</style>
