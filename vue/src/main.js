import Vue from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'

Vue.config.productionTip = false
axios.defaults.baseURL = 'http://localhost:8000/api/v1/'

new Vue({
  router,
  render: function (h) { return h(App) }
}).$mount('#app')
