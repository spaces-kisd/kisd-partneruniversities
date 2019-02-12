<template>
  <md-card md-with-hover @click.native="router.push(feature.properties.link_relative)">
    <!--     <md-ripple>
      <md-card-header>
        <div class="md-title">{{ feature.properties.title }}</div>
        <div class="md-subhead">It also have a ripple</div>
      </md-card-header>
      <md-card-content>Optio itaque ea, nostrum odio. Dolores, sed accusantium quasi non.</md-card-content>
    </md-ripple>-->
    <md-card-media v-if="feature.properties.thumbnail">
      <img :src="feature.properties.thumbnail" alt="People">
    </md-card-media>

    <md-card-header>
      <div class="md-title">{{ feature.properties.title }}</div>
      <div class="md-subhead">Subtitle here</div>
    </md-card-header>

    <md-card-content>{{ feature.properties.excerpt }} </md-card-content>

    <!--     <md-card-media-cover>
      <md-card-media md-ratio="16:9">
        <img :src="feature.properties.thumbnail" alt="Skyscraper">
      </md-card-media>

      <md-card-area>
        <md-card-header>
          <span class="md-title">{{ feature.properties.title }}</span>
          <span class="md-subhead">Inonesia</span>
        </md-card-header>

        <md-card-actions>
          <md-button>Neither this</md-button>
          <md-button>Or this</md-button>
        </md-card-actions>
      </md-card-area>
    </md-card-media-cover>-->
  </md-card>
</template>

<script>
//https://vuematerial.io/components/card/
export default {
  props: {
    feature: {
      type: Object
    }
  }, //['title', 'href', 'slug', 'data'],
  data() {
    return {
      router: this.$router
      //post: false
    };
  },
  components: {
    router: this.router
  },
  mounted() {
    //console.log(this)
  }
};
</script>