/**
 * 微信js-sdk
 * 参考文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141115
 */
import wx from 'weixin-js-sdk'
import axios from 'axios'
import store from './vuex'

const wxApi = {
  /**
     * [wxRegister 微信Api初始化]
     * @param  {Function} callback [ready回调函数]
     */
  wxRegister (data, option) { // data是微信配置信息，option是分享的配置内容
    console.log(data)
    wx.config({
      debug: false, // 开启调试模式
      appId: 'wx9c6b187f0150492c', // 必填，公众号的唯一标识
      timestamp: data.timestamp, // 必填，生成签名的时间戳
      nonceStr: data.nonceStr, // 必填，生成签名的随机串
      signature: data.signature, // 必填，签名，见附录1
      jsApiList: [
        // 'checkJsApi',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'onMenuShareAppMessage',
        'showMenuItems',
        'hideMenuItems',
        'hideAllNonBaseMenuItem',
        'updateAppMessageShareData', // 分享给朋友、分享到QQ
        'updateTimelineShareData' // 分享到朋友圈、分享到QQ空间
      ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    })
    // 处理验证失败的信息
    wx.error(function (res) {
      console.log('验证失败')
      console.log(res)
    })
    wx.ready(function () {
      const parameters = {
        title: option.title, // 分享标题
        link: option.link, // 分享链接
        imgUrl: option.imgUrl, // 分享图标
        desc: option.desc, // 分享描述
        success () {
          // 用户成功分享后执行的回调函数
          option.success()
        },
        cancel () {
          // 用户取消分享后执行的回调函数
          option.error()
        }
      }
      wx.updateTimelineShareData(parameters)
      wx.updateAppMessageShareData(parameters)
      wx.onMenuShareQQ(parameters)

      // wx.hideAllNonBaseMenuItem()
      // wx.showMenuItems({
      //   menuList: [
      //     'menuItem:share:appMessage',
      //     'menuItem:share:timeline',
      //     'menuItem:favorite',
      //     // "menuItem:originPage",
      //     'menuItem:refresh',
      //     // "menuItem:openWithQQBrowser",
      //     'menuItem:setFont'
      //   ], // 要显示的菜单项，所有menu项见附录3
      //   success: function () {
      //     console.log('yes')
      //   },
      //   fail: function (res) {
      //     console.log('no')
      //   }
      // })
      wx.hideMenuItems({
        menuList: [
          'menuItem:copyUrl',
          'menuItem:originPage',
          'menuItem:openWithBrowser',
          'menuItem:share:qq',
          'menuItem:share:QZone',
          'menuItem:share:weiboApp'
          // 'menuItem:openWithQQBrowser',
          // 'menuItem:openWithBrowser'
        ]
      })
    })
  },

  // 修改分享
  initWxApi (url, image, title, desc) {
    if (store.state.is_weixin) {
      // alert(location.href.split('#')[0])
      console.log('start to share')
      console.log('window: ', window, location)
      var that = this
      axios.get('/interface.php?a=weixinjiekou&url=' + window.location.href).then(res => {
        const data = res.data
        var imageHostObj = new RegExp(store.state.image_host, 'g')
        image = image === '' ? 'https://himg.hockeygarden.com/static/images/hockeypeak.png' : (image.replace(imageHostObj, 'himg.hockeygarden.com'))
        title = title === '' ? '' : '冰球巅峰：' + title

        const option = {
          title: title,
          desc: desc,
          link: url,
          imgUrl: image,
          success: function () { that.shareSuccess() },
          cancel: function () { that.shareCancel() }
        }
        console.log('option：', option)
        that.wxRegister(data, option) // data是微信配置信息，option是分享要配置的内容
      })
    }
  },
  shareSuccess () {
    console.log('share success')
  },
  shareCancel () {
    console.log('share cancel')
  }
}
export default wxApi
