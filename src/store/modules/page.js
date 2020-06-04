import api from '../../api';
import * as types from '../mutation-types';

// initial state
const state = {
  all: [],
  frontPage: {},
  loaded: false,
  page: null,
};

// getters
const getters = {
  allPages: state => state.all,
  allPagesLoaded: state => state.loaded,
  page: state => id => {
    let field = typeof id === 'number' ? 'id' : 'slug';
    let page = state.all.filter(page => page[field] === id);
    return (page[0]) ? page[0] : false;
  },
  pageContent: state => id => {
    let field = typeof id === 'number' ? 'id' : 'slug';
    let page = state.all.filter(page => page[field] === id);

    return (page[0]) ? page[0].content.rendered : false;
  },
  somePages: state => limit => {
    if (state.all.length < 1) {
      return false;
    }
    let all = [...state.all];
    return all.splice(0, Math.min(limit, state.all.length));
  },
  frontPage: state => {
    return state.frontPage;
  }
};

// actions
const actions = {
  getAllPages({ commit }) {
    this.get(
      'pages',
      { per_page: 10 },
      pages => {
        commit(types.STORE_FETCHED_PAGES, { pages });
        commit(types.PAGES_LOADED, true);
        commit(types.INCREMENT_LOADING_PROGRESS);
      }
    );
  },
  getFrontPage({ commit }) {
    api.getFrontPage(page => {
      commit(types.STORE_FETCHED_FRONTPAGE, { page });
      //commit(types.FRONTPAGE_LOADED, true);
    });
  },
};

// mutations
const mutations = {
  [types.STORE_FETCHED_PAGES](state, { pages }) {
    //console.log('loaded pages', pages);
    state.all = pages;
  },
  [types.STORE_FETCHED_FRONTPAGE](state, { page }) {
    //console.log('loaded', page);
    state.frontPage = page;
  },
  [types.PAGES_LOADED](state, val) {
    state.loaded = val;
  },
};

export default {
  state,
  getters,
  actions,
  mutations,
};
