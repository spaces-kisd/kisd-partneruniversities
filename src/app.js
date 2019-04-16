require("./bootstrap.js");

import Vue from "vue";

import router from "./router";
import App from "./App.vue";
import store from "./store";
import * as types from "./store/mutation-types";

import "./assets/css/styles.css";

/* import VueMaterial from 'vue-material'; */
import { MdButton, MdContent, MdDrawer, MdCard, MdIcon, MdChips, MdProgress, MdList } from 'vue-material/dist/components'
import 'vue-material/dist/vue-material.min.css';
//import 'vue-material/dist/theme/default.css';
/* import 'vue-material/dist/theme/default-dark.css' */

Vue.use(MdButton);
Vue.use(MdContent);
Vue.use(MdDrawer);
Vue.use(MdCard);
Vue.use(MdIcon);
Vue.use(MdChips);
Vue.use(MdProgress);
Vue.use(MdList);

// custom theme for zero waste living lab.
Vue.material.theming.theme = 'z';

/* Vue.use(VueMaterial); */

import VueAwesomeSwiper from 'vue-awesome-swiper';

// require styles
import 'swiper/dist/css/swiper.css';

Vue.use(VueAwesomeSwiper);

new Vue({
  el: "#app",
  store,
  router,
  render: h => h(App),
  created() {

    this.$store.commit(types.RESET_LOADING_PROGRESS);

    this.$store.dispatch("fetchTaxonomy", 'categories');
    //http://wp.local/wp-json/wp/v2/solution_categories

    //@todo rename solution_categories to something sane.
    // the tax is currently named solutions and has a cutom api path (solution_categories).
    this.$store.dispatch("fetchTaxonomy", 'solution_categories');
    //this.$store.dispatch("get", );
    //this.$store.dispatch("getAllPages");
    this.$store.dispatch("getFrontPage");
  }
});
