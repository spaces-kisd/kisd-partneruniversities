import api from '../../api'
import * as types from '../mutation-types'

// initial state
// todo: add a: posts of category loaded?!
const state = {
  all: [],
  loaded: false
}

// getters
const getters = {
  // Returns an array all categories
  allCategories: state => state.all,
  getCategory: state => catId => {
    return state.all.find( function(el){
      return (el.id == catId);
    })
  },
  allCategoriesLoaded: state => state.loaded,
  getCategories: state => query => {
    if (!state.all.length) {
      return;
    }
    //console.log('cats state', state.all, query);
    var keys = _.keys(query);
    let filtered = state.all.filter((single) => {
      return keys.every( key => { return single[key] == query[key] } )
    });
    return filtered;
  }
}

// actions
const actions = {
  // http://wp.local/wp-json/wp/v2/categories?include=2,23
  fetchTaxonomy ({ commit }, taxonomy = 'categories') {
    api.get(
      taxonomy,
      { sort: 'name', hide_empty: 'false', per_page: 100 },
      categories => {
        commit(types.STORE_FETCHED_CATEGORIES, { categories })
        commit(types.CATEGORIES_LOADED, true)
        commit(types.INCREMENT_LOADING_PROGRESS)
      }
    )
  }
}

// mutations
const mutations = {
  [types.STORE_FETCHED_CATEGORIES] (state, { categories }) {
    if ( !_.isUndefined(categories)){
      state.all = categories.concat(state.all);
    } else {
      console.log( 'no cats fetched.')
    }
    //state.all = categories
  },
  [types.CATEGORIES_LOADED] (state, bool) {
    state.loaded = bool
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}
