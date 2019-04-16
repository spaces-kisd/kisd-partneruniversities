<template>
  <md-content class="post-content" v-html="content"></md-content>
  <!-- {{post.post_content}} -->
  <!-- 	{{post}} -->
</template>

<script>
export default {
  data() {
    return {};
  },
  props: {
    content: "",
    options: {
      //these are the defaults, change if you want ;)
      showAuthor: true
    }
  }
};
</script>

<style>
.post-content > .alignfull {
  margin: 0 -16px 16px;
  max-width: 120%;
  width: auto;
}

.wp-block-quote,
.wp-block-quote {
  margin: 30px 0 35px;
  padding: 0 1.4em;
  border-left: 1px solid #ccc;
}

ul {
  padding-left: 1.4em;
}

.wp-block-quote p,
.wp-block-quote p {
  /* font-size: 1.24em; */
  font-size: 1.2em;
  font-style: normal;
  line-height: 1.4em;
  font-weight: bold;
  color: #ddd;
}

cite {
  font-style: normal;
  font-size: 0.8em;
}

.wp-block-embed-youtube,
.wp-block-embed-vimeo {
  margin: 0;
  padding-top: 59%;
  position: relative;
}
.wp-block-embed-youtube iframe,
.wp-block-embed-vimeo iframe {
  top: 0;
  position: absolute;
  width: 100%;
  height: 100%;
}
.user-button a {
  color: inherit !important;
  text-decoration: none !important;
}

.md-button.large {
  height: 46px;
}

.post-content .md-button {
  width: 100%;
  margin: 6px 8px 6px 0;
}

.md-chip {
  height: 28px;
  line-height: 28px;
}
</style>
