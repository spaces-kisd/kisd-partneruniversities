/* tslint:disable */
import Vue from 'vue';
import * as _ from 'lodash';
import './assets/css/styles.css';

import {
  MdButton, MdContent, MdDrawer, MdCard, MdIcon, MdChips, MdProgress, MdList,
} from 'vue-material/dist/components';

import router from './router/index';
import App from './App.vue';
import store from './store/index';
import * as types from './store/mutation-types';

import 'vue-material/dist/vue-material.min.css';

require('./bootstrap');
import 'vue-material/dist/theme/default.css';
//import 'vue-material/dist/theme/default-dark.css'

/* Vue.use(VueMaterial); */

Vue.use(MdButton);
Vue.use(MdContent);
Vue.use(MdDrawer);
Vue.use(MdCard);
Vue.use(MdChips);
Vue.use(MdIcon);
Vue.use(MdProgress);
Vue.use(MdList);

/**
 * todo:remove custom theme.
 */
Vue.material.theming.theme = 'z';

new Vue({ // eslint-disable-line no-new
  router,
  store,
  render: (h) => h(App), // eslint-disable-line
  el: '#app',
  created() {
    this.$store.commit(types.RESET_LOADING_PROGRESS);

    this.$store.dispatch('fetchTaxonomy', 'categories');
    // http://wp.local/wp-json/wp/v2/solution_categories

    // @todo rename solution_categories to something sane.
    // the tax is currently named solutions and has a cutom api path (solution_categories).
    this.$store.dispatch('fetchTaxonomy', 'solution_categories');
    // this.$store.dispatch("get", );
    // this.$store.dispatch("getAllPages");
    this.$store.dispatch('getFrontPage');
  },
});
