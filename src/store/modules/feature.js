import api from '../../api'
import * as types from '../mutation-types'

// initial state
const state = {
  collection: {
    features: []
  },
  selected: [],
  loaded: false
}

// getters
const getters = {
  getFeatures: state => state.collection.features,
  featuresLoaded: state => state.loaded
}

// actions
const actions = {
  getFeatureCollection ({ commit }) {
    api.getFeatureCollection( features => {
      commit(types.STORE_FETCHED_FEATURES, { features })
      commit(types.FEATURES_LOADED, true)
      commit(types.INCREMENT_LOADING_PROGRESS)
    })
  }
}

// mutations
const mutations = {
  [types.STORE_FETCHED_FEATURES] (state, { features }) {
    console.log("mutate state.collection to", features)
    state.collection = features
  },

  [types.FEATURES_LOADED] (state, val) {
    state.loaded = val
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}
