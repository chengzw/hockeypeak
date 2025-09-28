// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
// import Vue from 'vue'
import App from './App'
import router from './router'
import store from './vuex'
import './axios'
import wxapi from '@/wxapi.js'
//

// import Vconsole from 'vconsole'
// const vConsole = new Vconsole()
// Vue.use(vConsole)

import VueWechatTitle from 'vue-wechat-title'
// import Video from 'video.js'
// import 'video.js/dist/video-js.css'
// Vue.prototype.$video = Video

// import hls from 'videojs-contrib-hls'
// import VideoPlayer from 'vue-video-player'
import 'video.js/dist/video-js.css'
// import 'vue-video-player/src/custom-theme.css'

import { Loading } from 'vux'

import preview from 'vue-photo-preview'
import 'vue-photo-preview/dist/skin.css'

Vue.prototype.wxapi = wxapi
Vue.use(VueWechatTitle)

if (process.env.NODE_ENV === 'development') {
  Vue.config.devtools = true
} else {
  Vue.config.devtools = false
}

// Vue.use(hls)
// Vue.use(VideoPlayer)

store.dispatch('init')
store.dispatch('setIsWeixin')
Vue.component('loading', Loading)
let options = {
  fullscreenEl: false, // 控制是否显示右上角全屏按钮
  closeEl: true, // 控制是否显示右上角关闭按钮
  tapToClose: true, // 点击滑动区域应关闭图库
  shareEl: false, // 控制是否显示分享按钮
  zoomEl: false, // 控制是否显示放大缩小按钮
  counterEl: true, // 控制是否显示左上角图片数量按钮
  arrowEl: true, // 控制如图的左右箭头（pc浏览器模拟手机时）
  tapToToggleControls: true, // 点击应切换控件的可见性
  clickToCloseNonZoomable: true // 点击图片应关闭图库，仅当图像小于视口的大小时
}
Vue.use(preview, options)

// // 设置cookie,增加到vue实例方便全局调用
// function setCookie (cname, cpwd, exdays) {
//   var exdate = new Date()// 获取时间
//   exdate.setTime(exdate.getTime() + 24 * 60 * 60 * 1000 * exdays)// 保存的天数
//   window.document.cookie = 'userName' + '=' + cname + ';path=/;expires=' + exdate.toGMTString()
//   window.document.cookie = 'userPwd' + '=' + cpwd + ';path=/;expires=' + exdate.toGMTString()
// }
// // 获取cookie、
// function getCookie (name) {
//   var arr
//   var reg = new RegExp('(^| )' + name + '=([^;]*)(;|$)')
//   if (arr = document.cookie.match(reg)) return (arr[2])
//   else return null
// }
// // 删除cookie
// function delCookie (name) {
//   setCookie(name, '', -1)
// }

// // 删除LocalData
// function delLocalData (name) {
//   // delCookie(name)
//   localStorage.removeItem(name)
// }

// 去掉两端空格
Vue.prototype.Trim = (str) => {
  return str.replace(/(^\s*)|(\s*$)/g, '')
}

// 保留小数
Vue.prototype.MathFloat = (num, bit = 0) => {
  if (bit === 0) {
    num = num > 0 ? Math.round(num) : 0
  } else if (bit > 0 && bit % 10 === 0) {
    num = num > 0 ? (Math.round(num * bit) / bit) : 0
  }
  return num
}

// 获取LocalData
Vue.prototype.getLocalData = (name) => {
  var d = localStorage.getItem(name)
  // if (d == null) {
  // d = getCookie(name)
  // }
  return d
}
// 设置LocalData
Vue.prototype.setLocalData = (name, value) => {
  // setCookie(name, value, 365)
  localStorage.setItem(name, value)
}

// 跳转页面
Vue.prototype.gotopage = (url) => {
  if (store.state.currPage !== url) {
    router.push(url)
  }
}

// 处理队伍logo
Vue.prototype.formatLogo = (str) => {
  if (str.substring(0, 4) === '/www') {
    str = 'http:/' + str
  }
  return str
}

// 获取新的图片链接
Vue.prototype.getNewImagePath = (picUrl) => {
  var result = ''
  if (picUrl && picUrl !== '') {
    var strPos = picUrl.indexOf('/images/')
    if (strPos > -1) {
      result = picUrl.substr(strPos)
      if (result.indexOf('/photos/') !== -1) {
        result = result.replace(/(\d+x+\d+\/)/g, '768x432/')
      }
      result = 'https://' + store.state.image_host + result
    } else {
      result = picUrl
    }
  } else {
    result = 'https://' + store.state.image_host + '/static/images/hockeypeak.png'
  }
  return result
}

// 格式化时间
Vue.prototype.formatSeconds = (value) => {
  let result = parseInt(value)
  let h = Math.floor(result / 3600) < 10 ? '0' + Math.floor(result / 3600) : Math.floor(result / 3600)
  let m = Math.floor((result / 60 % 60)) < 10 ? '0' + Math.floor((result / 60 % 60)) : Math.floor((result / 60 % 60))
  let s = Math.floor((result % 60)) < 10 ? '0' + Math.floor((result % 60)) : Math.floor((result % 60))
  result = (h === '00' ? '' : (h + ':')) + m + ':' + s
  return result
}

Vue.prototype.getNowTime = () => {
  var now = new Date()
  var year = now.getFullYear()
  var month = now.getMonth() + 1
  var day = now.getDate()
  if (month < 10) {
    month = '0' + month
  };
  if (day < 10) {
    day = '0' + day
  };
  //  如果需要时分秒，就放开
  var h = now.getHours()
  var m = now.getMinutes()
  var s = now.getSeconds()
  var formatDate = year + '-' + month + '-' + day + ' ' + h + ':' + m + ':' + s
  return formatDate
}

Vue.prototype.getDateStr = (today, addDayCount) => {
  let showDay = ['周日', '周一', '周二', '周三', '周四', '周五', '周六']
  var date
  if (today) {
    date = new Date(today)
  } else {
    date = new Date()
  }
  date.setDate(date.getDate() + addDayCount)// 获取AddDayCount天后的日期

  var y = date.getFullYear()
  var m = date.getMonth() + 1// 获取当前月份的日期
  var d = date.getDate()
  if (m < 10) {
    m = '0' + m
  };
  if (d < 10) {
    d = '0' + d
  };
  let day = date.getDay()
  // var week = showDay[day]

  var result = {
    date: y + '-' + m + '-' + d,
    week: showDay[day]
  }
  return result
}

Vue.prototype.getArrayIndexKeyByValue = (array, key, value) => {
  var index = -1
  for (var arrayIndex in array) {
    if (array[arrayIndex][key] === value) {
      index = arrayIndex
      break
    }
  }
  return Number(index)
}

/* eslint-disable no-new */
new Vue({
  el: '#app',
  store,
  router,
  components: { App },
  template: '<App/>'
})
