import SETTINGS from '../settings'
import * as _ from 'lodash'

function restBase () {
  return ((window.vueWp && window.vueWp.restUrl) || '/wp-json/').replace(/\/$/, '')
}

export function wpFetch (route) {
  const headers = { 'X-Requested-With': 'XMLHttpRequest' }
  if (window.vueWp && window.vueWp.apiNonce) {
    headers['X-WP-Nonce'] = window.vueWp.apiNonce
  }
  return fetch(`${restBase()}/${route}`, { headers }).then((res) => {
    if (!res.ok) throw new Error(res.statusText)
    return res.json()
  })
}

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
    wpFetch(SETTINGS.API_BASE_PATH + type + this.toQueryString(query))
      .then((data) => cb(data))
      .catch((e) => {
        console.log('something went wrong fetching:', e, 'type:', type, 'query', query)
      })
  },

  getById (id, postType = 'post', cb) {
    if (_.isNull(id) || !_.isNumber(id)) return false
    wpFetch(`${SETTINGS.API_BASE_PATH + postType}/${id}`)
      .then((data) => cb({ data }))
      .catch((e) => cb(e))
  },

  getFrontPage (cb) {
    wpFetch('map/v1/frontpage')
      .then((data) => cb(data))
      .catch((e) => cb(e))
  }
}
