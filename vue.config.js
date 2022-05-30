const path = require("path");
module.exports = {
  filenameHashing: false,
  lintOnSave: process.env.NODE_ENV !== 'production',
  // https://cli.vuejs.org/config/#devserver-proxy
  devServer: {
    // writeToDisk: true, // by default files are only stored in ram
    // proxy: 'http://wp.local/',
    // host: 'http://wp.local/',
    // disableHostCheck: true,
  }
}
