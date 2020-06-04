import api from '../../api';
import * as types from '../mutation-types';

// initial state
const state = {
  posts: [], //we store aaall the posts (and all post types here).
  recent: [],
  loaded: false,
};

// getters
const getters = {
  recentPosts: state => limit => {
    if (
      !limit ||
      !Number.isInteger(limit) ||
      typeof limit == 'undefined'
    ) {
      return state.recent;
    }
    let recent = state.recent;
    return recent.slice(0, limit);
  },

  recentPostsLoaded: state => state.loaded,

  /**
   * Slugs are unique for each taxonomy. So if you search for a post by slug you wouly have to
   * query {slug: 'abc', type: 'post'}
   * Query args are connected with an AND, so all must be true.
   */
  getPosts: state => query => {
    if (!state.posts.length) {
      return;
    }
    //console.log('query', query, state.posts);
    var keys = _.keys(query);
    let filtered = state.posts.filter((post) => {
      return keys.every(key => {
        if (post[key] instanceof Array) {
          //console.log('compare arr', post[key], 'with', query[key]);
          return _.includes(post[key], query[key]);
        }
        //console.log('compare', key, post[key], 'with', query[key]);
        return post[key] == query[key]
      })
    });
    return filtered;
  }

};

// actions
const actions = {
  fetchPostTypes({ commit }, query = {}) {
    /* if (this.getters.getPosts(this.state, query)) {
        return;
    } */
    const { type = 'posts' } = query;
    api.get(type, query, posts => {
      if (posts) {
        commit(types.STORE_FETCHED_POSTS, posts);
        /*  commit(types.POSTS_LOADED, true);
         commit(types.INCREMENT_LOADING_PROGRESS); */
      } else {
        console.log('get went wrong', posts);
      }
    });
  },
};

// mutations
const mutations = {
  [types.STORE_FETCHED_POSTS](state, posts) {
    console.log('commit STORE_FETCHED_POSTS', posts);
    posts.forEach(newPost => {

      var postExists = state.posts.findIndex(statePost => { return (statePost.id == newPost.id) });

      if (postExists == -1) {
        state.posts.push(newPost);
      } else {
        //console.log('We already have post', newPost.id,  newPost);
      }
    });
    //state.recent = posts.concat(state.posts);
  },
  [types.POSTS_LOADED](state, val) {
    state.loaded = val;
  },
};

export default {
  state,
  getters,
  actions,
  mutations,
};
