import axios from "axios";
/* import mapboxgl from "mapbox-gl"; */

try {
  window._ = require('lodash');

  axios.defaults.headers.common = {
    "X-WP-Nonce": window.vueWp.apiNonce, // authenticate wordpress with a nonce.
    "X-Requested-With": "XMLHttpRequest"
  };
  axios.defaults.baseURL = window.vueWp.path; // for network installations of wp.
} catch (e) {
 //console.log(e);
}