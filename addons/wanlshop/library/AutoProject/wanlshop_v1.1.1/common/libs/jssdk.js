// #ifdef H5
const wechat = require("@/common/libs/jssdk/wechat");
import api from '@/common/request/request'; 
import config from '@/common/config/config';
// #endif
export default {
	//判断是否在微信中    
	isWechat: function() {
		var ua = window.navigator.userAgent.toLowerCase();
		if (ua.match(/micromessenger/i) == 'micromessenger') {
			return true;
		} else {
			return false;
		}
	},
	// 初始化
	initJssdk: function(callback) {
		// 记录进入app时的url
		if (typeof window.entryUrl === 'undefined' || window.entryUrl === '') {
			window.entryUrl = location.href.split('#')[0]
		}
		let isiOS = !!navigator.userAgent.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
		// 进行签名的时候  Android 不用使用之前的链接， ios 需要
		let signLink = isiOS ? window.entryUrl : location.href.split('#')[0];
		var uri = encodeURIComponent(signLink); //获取当前url然后传递给后台获取授权和签名信息
		//服务端进行签名
		api.get({
			url: '/wanlshop/wechat/config',
			data: {
				url: uri
			},
			success: res => {
				wechat.config(res);
				wechat.ready(function() {
					if (callback) {
						callback();
					}
				})
			}
		});
	},
	//在需要定位页面调用  
	getlocation: function(callback) {
		if (!this.isWechat()) {
			//console.log('不是微信客户端')  
			return;
		}
		this.initJssdk(function(res) {
			wechat.ready(function() {
				wechat.getLocation({
					type: 'gcj02', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'  
					success: function(res) {
						// console.log(res);  
						callback(res)
					},
					fail: function(res) {
						// console.log(res)
					},
					// complete:function(res){  
					//     console.log(res)  
					// }  
				});
			});
		});
	},
	openlocation: function(data, callback) { //打开位置  
		if (!this.isWechat()) {
			//console.log('不是微信客户端')  
			return;
		}
		this.initJssdk(function(res) {
			wechat.ready(function() {
				wechat.openLocation({ //根据传入的坐标打开地图  
					latitude: data.latitude,
					longitude: data.longitude
				});
			});
		});
	},
	chooseImage: function(callback) { //选择图片  
		if (!this.isWechat()) {
			//console.log('不是微信客户端')  
			return;
		}
		//console.log(data);  
		this.initJssdk(function(res) {
			wechat.ready(function() {
				wechat.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					sourceType: ['album'],
					success: function(res) {
						callback(res)
					}
				})
			});
		});
	},
	//微信支付  
	wxpay: function(params = {}) {
		if (!this.isWechat()) {
			console.log('不是微信客户端')  
			return;
		}
		this.initJssdk(function() {
			wechat.chooseWXPay({
				timestamp: params.data.timestamp, // 支付签名时间戳，注意微信wechat中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符  
				nonceStr: params.data.nonceStr, // 支付签名随机串，不长于 32 位  
				package: params.data.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）  
				signType: params.data.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'  
				paySign: params.data.paySign, // 支付签名  
				success: function(res) {
					// console.log(res);  
					// console.log('微信JSAPI返回支付成功')
					params.success(res)
				},
				cancel: function(res) {
					params.fail(res)
					// console.log('微信JSAPI返回支付失败')
				},
				// complete:function(res){  
				//     console.log(res)  
				// }  
			});

		});
	},
	//在需要自定义分享的页面中调用
	share: function(data) {
		if (!this.isWechat()) {
			//console.log('不是微信客户端')  
			return;
		}
		//每次都需要重新初始化配置，才可以进行分享  
		this.initJssdk(function() {
			var shareData = {
				title: data && data.title ? data.title : '我发现了一个很好的线上购物商城',
				desc: data && data.desc ? data.desc : '品质好而且很省钱如果自己在上面购买，每年可以省下1%~40%的钱',
				link: data && data.link ? data.link : window.location.href,
				imgUrl: data && data.imgUrl ? data.imgUrl : `${config.cdnurl}/assets/addons/wanlshop/img/common/logo.png`,
				success: function(res) {
					//用户点击分享后的回调，这里可以进行统计，例如分享送金币之类的  
				},
				cancel: function(res) {}
			};
			//分享给朋友接口  
			wechat.onMenuShareAppMessage(shareData);
			//分享到朋友圈接口  
			wechat.onMenuShareTimeline(shareData);
		});
	},
	// 选择通讯地址
	chooseAddress: function(callback) {
		if (!this.isWechat()) {
			//console.log('不是微信客户端')  
			return;
		}
		this.initJssdk(function() {
			wechat.openAddress({
				success: function(res) {
					callback(res)
				}
			})
		})
	}
}
