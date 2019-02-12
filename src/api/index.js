import _ from "lodash";
import axios from "axios";
import SETTINGS from "../settings";

export default {
  getCategories(cb) {
    axios
      .get(
        SETTINGS.API_BASE_PATH +
          "categories?sort=name&hide_empty=true&per_page=50"
      )
      .then(response => {
        cb(response.data.filter(c => c.name !== "Uncategorized"));
      })
      .catch(e => {
        cb(e);
      });
  },

  getPages(cb) {
    axios
      .get(SETTINGS.API_BASE_PATH + "pages?per_page=10")
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      });
  },

  getPage(id, cb) {
    if (_.isNull(id) || !_.isNumber(id)) return false;
    axios
      .get(SETTINGS.API_BASE_PATH + "pages/" + id)
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      });
  },

  getPosts (limit, cb) {
    this.getPostType('posts', limit, cb);
    /* if (_.isEmpty(limit)) { let limit = 5 }
    
    axios.get(window.SETTINGS.API_BASE_PATH + 'posts?per_page='+limit)
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e)
      }) */
  },

  getPost (id, cb) {
    if (_.isNull(id) || !_.isNumber(id)) return false
    axios.get(window.SETTINGS.API_BASE_PATH + 'posts/'+id)
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      })
  },

  getPostType(name, limit, cb) {
    if (_.isEmpty(limit)) { let limit = 5 }
    let base_url = window.location.protocol + "//" + window.location.host + "/";
    axios.get(window.SETTINGS.API_BASE_PATH + name + '?per_page='+limit)
      .then(response => {
        //response.data.link_relative = response.data.link.replace(base_url, '');
        //console.log(response.data.link);
        cb(response.data);
      })
      .catch(e => {
        cb(e)
      });
  },

  getFeatureCollection( cb ) {
    axios.get("/wp-json/map/v1/features/solution").then(response => {
      console.log('api features', response.data)
      cb(response.data);
    })
    .catch(e => {
      cb(e)
    });
  }
}
