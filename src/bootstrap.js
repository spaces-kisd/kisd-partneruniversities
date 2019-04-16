import axios from "axios";
/* import mapboxgl from "mapbox-gl"; */

try {
  window._ = require('lodash');
  //window.$ = window.jQuery = require('jquery');
  axios.defaults.headers.common = {
    "X-WP-Nonce":
      typeof window.restAPI !== "undefined" ? window.restAPI.nonce : "",
    "X-Requested-With": "XMLHttpRequest"
  };
} catch (e) {
 //console.log(e);
}