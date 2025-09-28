import Vue from 'vue'
import axios from 'axios'

axios.defaults.baseURL = process.env.API_ROOT
axios.defaults.withCredentials = true
Vue.prototype.$ajax = axios
