/* eslint-disable eqeqeq */
/* eslint-disable camelcase */
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    gameCurrTabName: 'schedule',
    header_name: '',
    // image_host: 'res.hockeypeak.com',
    image_host: 'www.hockeypeak.com',
    is_weixin: false,
    menu: [],
    currPage: '/',
    user: undefined,
    options: {
      email: undefined,
      head_pic: undefined,
      mobile: undefined,
      level: undefined,
      field_limit: undefined,
      province_id: undefined,
      city_id: undefined,
      district_id: undefined
    }
  },
  // 修改全局变量必须通过mutations中的方法
  // mutations只能采用同步方法
  mutations: {
    setReturnRoute (state, payload) {
      state.returnRoute = payload
    },
    setStudyPage (state, payload) {
      state.study_page = payload
    },
    setNewSelectTextbooks (state, payload) {
      state.new_selected_textbooks = payload
    },
    login (state, payload) {
      state.user = payload
      var str_options = localStorage.getItem('options')
      var options = JSON.parse(str_options)
      if (options != null) {
        state.options = options
      }
    },
    logout (state) {
      state.user = undefined
      // state.options = this.defaultOptions
    },
    setOptions (state, payload) {
      state.options = payload
    },
    update (state, payload) {
      state.tabbarIndex = payload
    },
    setLastViewItemId (state, payload) {
      state.last_view_item_id = payload
    },
    setLastStudyKpId (state, payload) {
      state.last_study_kp_id = payload
    },
    setItem (state, payload) {
      state.item = payload
    },
    setCurrPage (state, payload) {
      state.currPage = payload
    },
    setIsWeixin (state) {
      var ua = navigator.userAgent.toLowerCase()
      state.is_weixin = ua.match(/MicroMessenger/i) == 'micromessenger'
      // state.is_weixin = true
    }
  },
  // 异步方法用actions
  // actions不能直接修改全局变量，需要调用commit方法来触发mutation中的方法
  actions: {
    init (context) {
      // console.log("store init.")
      var name = localStorage.getItem('name')
      var user_id = localStorage.getItem('user_id')
      if (user_id && user_id > 0) {
        // console.log('session not null')
        context.commit('login', {
          user_id: user_id,
          name: name
        })
      }
    },
    setReturnRoute (context, payload) {
      context.commit('setReturnRoute', payload)
    },
    setStudyPage (context, payload) {
      context.commit('setStudyPage', payload)
    },
    setNewSelectTextbooks (context, payload) {
      context.commit('setNewSelectTextbooks', payload)
    },
    login (context, payload) {
      context.commit('login', payload)
    },
    logout (context) {
      context.commit('logout')
    },
    setOptions (context, payload) {
      context.commit('setOptions', payload)
    },
    updateTabbarIndex (context, payload) {
      context.commit('updateTabbarIndex', payload)
    },
    setLastViewItemId (context, payload) {
      context.commit('setLastViewItemId', payload)
    },
    setLastStudyKpId (context, payload) {
      context.commit('setLastStudyKpId', payload)
    },
    setItem (context, payload) {
      context.commit('setItem', payload)
    },
    setCurrPage (context, payload) {
      context.commit('setCurrPage', payload)
    },
    setIsWeixin (context) {
      context.commit('setIsWeixin')
    }
  }
})

export default store
