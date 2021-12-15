import Vue from 'vue'
import App from './App.vue'
import router from './router'
import vuetify from './plugins/vuetify'
import store from './store'
import axios from "axios";
import echo from 'laravel-echo'

window.Pusher = require ('pusher.js')

Vue.config.productionTip = false

Vue.use({
  install (Vue) {
    Vue.prototype.$api = axios.create({
      baseURL: process.env.VUE_APP_API_URL,
      withCredentials: true
    })
  }
})

new Vue({
  store,
  router,
  vuetify,
  render: h => h(App)
}).$mount('#app')
