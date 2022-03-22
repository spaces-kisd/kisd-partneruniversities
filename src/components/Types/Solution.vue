<template>
  <!-- todo: first load in private window. go on: get feature is doing the wrong thing with the wrong name. update. selected feature? who selects when? -->
  <content-card
    v-if="fProps"
    :feature_img="getFeatureImage"
    :loading="loading"
    :edit_url="editUrl"
    class="solution-card"
  >
    <md-card-header>
      <div class="card-title md-display-1">{{ fProps.title }}</div>
      <div class="card-full-name md-subheading">{{ fProps.full_name }}</div>
    </md-card-header>
    <md-card-content>
      <md-card-area v-if="postCats">
        <category-chips :categories="postCats" />
      </md-card-area>
      <md-card-area>
        <post-content
          v-if="myPost"
          :content="myPost.content.rendered"
          :options="{ showAuthor: 'eee' }"
        />
      </md-card-area>
      <md-card-area>
        <span v-if="fProps.location_name">
          <md-icon>location_on</md-icon>
          {{ fProps.location_name }}
        </span>
      </md-card-area>
      <md-card-area>
        <span v-if="fProps.website">
          <md-icon>language</md-icon>
          <a :href="fProps.website" target="_blank">{{ fProps.website }}</a>
        </span>
      </md-card-area>
      <md-card-area>
        <span v-if="fProps.contact">
          <md-icon>mail_outline</md-icon>
          {{ fProps.contact }}
        </span>
      </md-card-area>
      <md-card-area class="solution-props">
        <!--  https://material.io/tools/icons/?style=baseline -->
        <span v-if="fProps.department">
          <b>Department/s:</b>
          {{ fProps.department }}
        </span>
        <!-- <span v-if="fProps.erasmus_code">
          <md-icon>vpn_key</md-icon>
          {{fProps.erasmus_code}}
        </span> -->
      </md-card-area>
      <md-card-area>
        <span v-if="fProps.deadline">
          <b>Application deadline:</b> {{ fProps.deadline }}
        </span>
      </md-card-area>
      <md-card-area>
        <users-on-post :post-id="fProps.post_id" />
      </md-card-area>
    </md-card-content>
  </content-card>
</template>

<script>
// @see https://vuematerial.io/components/card/
/**
 * @todo: select the right feature!
 */
import { mapGetters } from 'vuex'
import Loader from '../partials/Loader.vue'
import SETTINGS from '../../settings'
import PostContent from '../partials/PostContent.vue'
import * as types from '../../store/mutation-types.js'
import ProgressBar from '../partials/ProgressBar.vue'
import ContentCard from '../partials/ContentCard.vue'
import CategoryChips from '../partials/CategoryChips.vue'
import UsersOnPost from '../partials/UsersOnPost.vue'

export default {
  data () {
    return {
      router: this.$router,
      edit: '',
      loading: true
    }
  },
  computed: {
    ...mapGetters(['getFeatures', 'getPosts', 'featuresLoaded']),
    myPost () {
      const posts = this.getPosts({ slug: this.$route.params.postSlug })
      if (!_.isEmpty(posts)) {
        this.loading = false
        return _.first(posts)
      }
      console.log('No post found for slug', this.$route.params.postSlug, posts)
    },
    editUrl () {
      if (this.myPost) {
        return this.myPost.edit_url
      }
    },
    postCats () {
      console.log(this.myPost)
      if (!_.isUndefined(this.myPost)) {
        return this.myPost.solution_categories
      }
      return false
    },
    fProps () {
      const feature = this.getFeatures({ slug: this.$route.params.postSlug })
      // console.log('ftr', feature);
      if (!_.isUndefined(feature)) {
        if (feature.length != 0) {
          return _.first(feature).properties
        }
      }
      console.log(
        'feature was not found',
        feature,
        'for slug',
        this.$route.params.postSlug
      )
      this.loading = false
      return {
        title: 'Looks like this solution does not exist.',
        full_name: 'Check out another one :)'
      }
      // return this.getFeature({ slug: this.$route.params.postSlug });
    },
    getFeatureImage () {
      if (this.myPost) {
        return this.myPost.feature.large
      }
      return this.fProps.thumbnail
    }
  },
  components: {
    Loader,
    PostContent,
    ProgressBar,
    ContentCard,
    CategoryChips,
    UsersOnPost
  },
  mounted () {
    console.log('solution mounted')
    this.init()
  },
  update () {
    console.log('solution updated')
    this.init()
  },
  beforeRouteUpdate (to, from, next) {
    next()
    this.init()
  },
  methods: {
    init () {
      if (!this.myPost) {
        this.$store.dispatch('fetchPostTypes', {
          type: 'solutions',
          slug: this.$route.params.postSlug
        })
      }
      this.$store.commit(
        types.STORE_SELECT_FEATURE_BY_POST_SLUG,
        this.$route.params.postSlug
      )
    }
  }
}
</script>
<style>
.solution-props {
  margin-top: 20px;
}
.solution-props span ~ span:before {
  content: "  |  ";
  padding: 5px;
  color: #666;
}
@media only screen and (max-width: 860px) {
  .content-card.solution-card {
    margin-top: 40vh;
  }
  .content-card .md-card-media img {
    max-height: 45vh;
    object-fit: cover;
  }
}
</style>
