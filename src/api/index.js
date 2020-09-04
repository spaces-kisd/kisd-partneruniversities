import axios from 'axios'
import SETTINGS from '../settings'
import * as _ from 'lodash'

export default {

  toQueryString (obj) {
    const str = []
    for (const p in obj) {
      if (obj.hasOwnProperty(p)) {
        str.push(`${encodeURIComponent(p)}=${encodeURIComponent(obj[p])}`)
      }
    }
    return (str.length) ? `?${str.join('&')}` : ''
  },

  // rename to fetch?
  get (type = 'posts', query = { per_page: 10 }, cb) {
    if (_.includes(['post', 'page'], type)) {
      console.log(`Use plural here, ${type}s` + ` instead of ${type}`)
    }
    axios
      .get(
        SETTINGS.API_BASE_PATH +
        type +
        this.toQueryString(query)
      )
      .then((response) => {
        cb(response.data)
      })
      .catch((e) => {
        console.log('something went wrong fetching:', e, 'type:', type, 'query', query)
      })
  },
  getById (id, postType = 'post', cb) {
    if (_.isNull(id) || !_.isNumber(id)) return false
    axios
      .get(
        `${SETTINGS.API_BASE_PATH +
        postType}/${
          id}`
      )
      .then((response) => {
        cb(response)
      })
      .catch((e) => {
        cb(e)
      })
  },

  getFrontPage (cb) {
    axios
      .get('/wp-json/map/v1/frontpage')
      .then((response) => {
        cb(response.data)
      })
      .catch((e) => {
        cb(e)
      })
  }
}
