import api from '../../api'
import * as types from '../mutation-types'
import axios from "axios";


// initial state
const state = {
  collection: {
    features: [],
  },
  visible: [],
  selected: 0,
  featuresLoaded: false
}

// getters
const getters = {
  getAllFeatures: state => state.collection.features,
  getSelected: state => state.selected,
  getSelectedFeature: state => {
    return state.collection.features[state.selected];
  },
  getVisibleFeatureIds: state => state.visible,
  featuresLoaded: state => state.featuresLoaded,
  selectFeature: state => query => {
    let index = state.collection.features.findIndex(function (elem) {
      if (_.isUndefined(elem.properties)) {
        return false;
      }
      return (elem.properties[key] == query[key]);
    });
    if (index > -1) {
      state.selected = index;
    } else {
      console.log('no feature found');
    }
  },
  /**
   * Slugs are unique for each taxonomy. So if you search for a post by slug you wouly have to 
   * query {slug: 'abc', type: 'post'}
   * Query args are connected with an AND, so all must be true.
   * Using an array conntect thigs with OR
   * query { id: [1,3,4,5]}
   */
  getFeatures: state => query => {
    if (!state.collection.features.length) {
      return;
    }
    //console.log('query', query, state.collection.features);
    var keys = _.keys(query);
    let filtered = state.collection.features.filter((post) => {
      return keys.every(key => {
        if (post.properties[key] instanceof Array) {
          //console.log('compare arr', post.properties[key], 'with', query[key]);
          return _.includes(post.properties[key], query[key]);
        }
        else if (query[key] instanceof Array) {
          //console.log('compare arr', post.properties[key], 'with', query[key]);
          return _.includes(query[key], post.properties[key]);
        }
        //console.log('compare', key, post.properties[key], 'with', query[key]);
        return post.properties[key] == query[key];
      })
    });

    return filtered.sort(function(a,b){
      return a.priority - b.priority;
    });
  }
}

// actions
const actions = {
  getFeatureCollection({ commit }) {
    axios
      .get("/wp-json/map/v1/features/solution")
      .then(response => {
        let features = response.data;
        commit(types.STORE_FETCHED_FEATURES, { features });
        commit(types.INCREMENT_LOADING_PROGRESS);
        commit(types.FEATURES_LOADED, true);
      })
      .catch(e => {
        console.log(e);
      });
  }
}

// mutations
const mutations = {
  [types.STORE_FETCHED_FEATURES](state, { features }) {
    //console.log("mutate state.collection to", features);
    state.collection = features;
    //state.features = features.features;
  },
  [types.STORE_VISIBLE_FEATURES](state, features ) {
    
    state.visible = features.slice();
/*     features.forEach(element => {
      if ( ! _.isUndefined(element.properties)) {
        state.visible.push( element.properties.post_id )
      }
    }); */
  },

  [types.STORE_SELECT_FEATURE_BY_POST_SLUG](state, slug) {
    if (!state.collection.features) {
      console.log('selecting features that are not loaded...', slug, state.collection.features);
      return;
    }
    //let field = typeof id === "number" ? "id" : "slug";
    /**
     * @todo add errorhandling: findIndex returns -1 if not found.
     */
    state.selected = state.collection.features.findIndex(feature => feature.properties.slug === slug);
  },

  [types.FEATURES_LOADED](state, val) {
    state.featuresLoaded = val;
  },

  [types.FEATURE_SELECTED](state, val) {
    if (!state.collection.features.length) {
      console.log('no features found');
    }
    /* deselect all features */
    /*     state.collection.features.map( (feature) => {
          feature.properties.selected = false;
          return feature;
        }); */

    /*
    let featureCount = state.collection.features.length;
    var remain = val % featureCount;
    let val = Math.floor(remain >= 0 ? remain : remain + featureCount); */

    //state.collection.features[val].properties.selected = true;
    state.selected = val;//parseInt(val);
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}
