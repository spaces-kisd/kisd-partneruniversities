import api from "../../api";
import * as types from "../mutation-types";
import SETTINGS from "../../settings";

// initial state
const state = {
  error: null,
  notice: null,
  loading: true,
  loading_progress: 0,
  visible_drawer: false,
  visible_single: false
};

// getters
const getters = {
  isLoading: state => state.loading_progress < 100,
  loadingProgress: state => state.loading_progress,
  loadingIncrement: state => {
    return 100 / SETTINGS.LOADING_SEGMENTS;
  },
  visible_drawer: state => state.visible_drawer,
  visible_single: state => state.visible_single
};

// actions
const actions = {};

// mutations
const mutations = {
  [types.INCREMENT_LOADING_PROGRESS](state, val) {
    state.loading_progress = Math.min(
      state.loading_progress + getters.loadingIncrement(),
      100
    );
  },

  [types.RESET_LOADING_PROGRESS](state) {
    state.loading_progress = 0;
  },
  [types.VISIBLE_DRAWER](state, visible) {
   //console.log('commit', types.VISIBLE_DRAWER, visible);
    state.visible_drawer = visible;
  },
  [types.VISIBLE_SINGLE](state, visible) {
   //console.log('commit', types.VISIBLE_SINGLE, visible);
    state.visible_single = visible;
  }
};

export default {
  state,
  getters,
  actions,
  mutations
};
