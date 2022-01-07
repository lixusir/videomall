<template>
	<view>
		<view class="auth">
			<view class="wanl-title">请输入账号</view>
			<form @submit="formSubmit">
				<view class="auth-group radius-bock bg-gray wlian-grey-light">
					<input placeholder="请输入手机号 / 用户名" placeholder-class="placeholder" name="account" type="text" maxlength="16"
					 confirm-type="next" @input="onKeyInput"></input>
				</view>
				<view class="auth-button flex flex-direction">
					<button form-type="submit" class="cu-btn sl radius-bock bg-orange" :disabled="submitDisabled">下一步</button>
					<!-- #ifdef MP-WEIXIN -->
					<view class="wanl-weixin-btn-info margin-tb-bj text-center text-sm">或</view>
					<button type="primary" class="cu-btn sl radius-bock bg-no" open-type="getPhoneNumber" @getphonenumber="decryptPhoneNumber">获取手机号一键登录</button>
					<!-- #endif -->
				</view>
				<!-- 同意服务条款 -->
				<checkbox-group :class="checked == 1 ? 'shake-horizontal' : ''" class="auth-clause" @change="CheckboxChange">
					<checkbox class="orange" :class="checked == 2 ? 'checked' : ''" :checked="checked == 2 ? true : false" value="2" />
					<view>
						我已阅读<text @tap="onDetails($store.state.common.appConfig.user_agreement, '用户协议')">用户协议</text>及<text @tap="onDetails($store.state.common.appConfig.privacy_protection, '隐私保护')">隐私权保护声明</text>
					</view>
				</checkbox-group>
			</form>
		</view>
		<view class="auth-foot">
			<view class="oauth">
				<view :class="item.name" class="cu-avatar lg round bg-white" v-for="(item, key) in providerList" @tap="tologin(item)" :key="key" ></view>
			</view>
			<view class="menu text-grey">
				<text @tap="register">注册</text>
				<text @tap="help">帮助</text>
			</view>
		</view>
	</view>
</template>
<script>
	import graceChecker from '@/common/graceChecker';
	export default {
		data() {
			return {
				submitDisabled: true,
				providerList: [],
				loginRes: {},
				pageroute: '',
				checked: 0
			};
		},
		onLoad(option) {
			// #ifdef H5
			// 微信内置浏览器登录
			if(this.$jssdk.isWechat()){
				let local = window.location.href; // 获取页面url
				let code = this.getUrlCode().code; // 截取code
				// 获取之前的code
				let oldCode = uni.getStorageSync('wanlshop:code');
				// 如果没有code，就去请求获取code
				if (code == null || code === '' || code == 'undefined' || code == oldCode) {
					let uri = encodeURIComponent(local); 
					let page = this.$wanlshop.prePage().$mp.page;
					uni.setStorageSync('wanlshop:code', 0); // 设置旧的code为0，避免死循环
					window.location.href = `https://open.weixin.qq.com/connect/oauth2/authorize?appid=${this.$wanlshop.config('appid')}&redirect_uri=${uri}&response_type=code&scope=snsapi_userinfo&state=${encodeURIComponent(`/${page.route}?${this.queryParams(page.options)}`)}#wechat_redirect`
				} else {
					let pageroute = this.getUrlCode().state; // 截取state
					uni.setStorageSync('wanlshop:code', code); // 保存最新code
					this.$api.post({
						url: '/wanlshop/user/third',
						loadingTip: '微信登录中...',
						data: {
							platform: 'h5_weixin',
							code: code,
							client_id: uni.getStorageSync("wanlshop:chat_client_id") ? uni.getStorageSync("wanlshop:chat_client_id") : null
						},
						success: res => {
							this.$store.dispatch('user/login', res);
							this.$store.dispatch('cart/login');
							this.$store.dispatch('chat/get');
							uni.reLaunch({url: decodeURIComponent(pageroute)});
						}
					});
				}
			}else{
				let page = this.$wanlshop.prePage().$mp.page;
				this.pageroute = encodeURIComponent(`/${page.route}?${this.queryParams(page.options)}`);
			}
			// 未来版本开发
			// this.providerList = [{
			// 	name: 'wlIcon-QQ',
			// 	platform: 'web_qq'
			// }];
			// #endif
			
			// 获取第三方登录
			// #ifndef H5
			let page = this.$wanlshop.prePage().$mp.page;
			this.pageroute = encodeURIComponent(`/${page.route}?${this.queryParams(page.options)}`);
			uni.getProvider({
				service: 'oauth',
				success: (res) => {
					this.providerList = res.provider.map((value) => {
						let providerName = '';
						let platform = '';
						switch (value) {
							case 'weixin':
								providerName = 'wlIcon-WeChat';
								// #ifdef MP
								platform = 'mp_weixin';
								// #endif
								// #ifdef APP-PLUS
								platform = 'app_weixin';
								// #endif
								break;
							case 'qq':
								providerName = 'wlIcon-QQ'
								// #ifdef MP
								platform = 'mp_qq';
								// #endif
								// #ifdef APP-PLUS
								platform = 'app_qq';
								// #endif
								break;
							case 'sinaweibo':
								providerName = 'wlIcon-WeiBo'
								platform = 'app_weibo';
								break;
							case 'xiaomi':
								providerName = 'wlIcon-Xiaomi'
								platform = 'app_xiaomi';
								break;
							case 'apple':
								providerName = 'wlIcon-Apple'
								platform = 'apple';
								break;
						}
						return {
							id: value,
							name: providerName,
							platform: platform
						}
					});
				},
				fail: (error) => {
					console.log('获取登录通道失败', error);
				}
			});
			// #endif
			
			// 防止刷新登录态，uni.login code提前获取
			// #ifdef MP-WEIXIN
			uni.login({
				provider: 'weixin',
				success: res => {
					this.loginRes = res;
				},
				fail: err => {
					this.$wanlshop.msg(err.msg);
				}
			});
			// #endif
		},
		methods: {
			CheckboxChange(e) {
				this.checked = e.detail.value;
			},
			// 第三方登录
			tologin(provider) {
				uni.showLoading({
				    title: '登录中'
				});
				// #ifndef H5
				uni.login({
					provider: provider.id,
					// #ifdef MP-ALIPAY
					scopes: 'auth_user', //支付宝小程序需设置授权类型
					// #endif
					success: (loginRes) => {
						this.$api.post({
							url: '/wanlshop/user/third',
							data: {
								platform: provider.platform,
								loginData: loginRes,
								client_id: uni.getStorageSync("wanlshop:chat_client_id") ? uni.getStorageSync("wanlshop:chat_client_id") : null
							},
							success: res => {
								uni.hideLoading();
								if (res.binding == 0) {
									this.$wanlshop.to(`/pages/user/auth/perfect?third_id=${res.third_id}&platform=${provider.platform}&url=${this.pageroute}`);
								}else{
									this.$store.dispatch('user/login', res);
									this.$store.dispatch('cart/login');
									this.$store.dispatch('chat/get');
									uni.reLaunch({url: decodeURIComponent(this.pageroute)});
								}
							}
						});
						// 隐藏键盘
						uni.hideKeyboard();
					},
					fail: err => {
						this.$wanlshop.msg(err.msg);
					}
				});
				// #endif
				// #ifdef H5
				this.$api.post({
					url: '/wanlshop/user/third_web',
					data: {
						platform: provider.platform,
						client_id: uni.getStorageSync("wanlshop:chat_client_id")?uni.getStorageSync("wanlshop:chat_client_id") : null
					},
					success: res => {
						uni.hideLoading();
						this.$store.dispatch('user/login', res);
						this.$store.dispatch('cart/login');
						this.$store.dispatch('chat/get');
						// 返回页面
						uni.reLaunch({url: decodeURIComponent(this.pageroute)});
					}
				});
				// #endif
			},
			onKeyInput(e) {
				this.submitDisabled = false
			},
			// 号码登录，暂时支持微信小程序，后续版本逐渐开发
			decryptPhoneNumber(e){
				if (e.detail.errMsg != "getPhoneNumber:fail user deny") {
					this.$api.post({
						url: '/wanlshop/user/phone',
						data: {
							encryptedData: e.detail.encryptedData,
							iv: e.detail.iv,
							code: this.loginRes.code,
							client_id: uni.getStorageSync("wanlshop:chat_client_id")?uni.getStorageSync("wanlshop:chat_client_id") : null
						},
						success: res => {
							this.$store.dispatch('user/login', res);
							this.$store.dispatch('cart/login');
							// 返回页面
							uni.reLaunch({url: decodeURIComponent(this.pageroute)});
						}
					});
				}
			},
			// 账户登录
			formSubmit(e) {
				if(this.checked == 2){
					//将下列代码加入到对应的检查位置
					//定义表单规则
					let rule = [{
						name: 'account',
						checkType: 'phoneno',
						errorMsg: '不是手机号码'
					}];
					//进行表单检查
					let formData = e.detail.value;
					let checkRes = graceChecker.check(formData, rule);
					// ..检查是否注册-没注册跳转注册
					if (checkRes) {
						this.$api.get({
							url: '/wanlshop/validate/check_mobile_exist',
							data: {
								mobile: formData.account
							},
							success: res => {
								this.$wanlshop.to(`phone?mobile=${formData.account}&url=${this.pageroute}`,'none');
							},
							fail: res => {
								uni.showModal({
									title: '提示',
									content: '账号不存在，是否注册？',
									success: (res) => {
										if (res.confirm) {
											this.$wanlshop.to(`register?mobile=${formData.account}&url=${this.pageroute}`);
										} else if (res.cancel) {
											console.log('取消');
										}
									}
								});
							}
						});
					} else {
						if (formData.account) {
							this.$wanlshop.to(`name?name=${formData.account}&url=${this.pageroute}`,'none');
						} else {
							this.$wanlshop.msg('请填写账号');
						}
					}
				}else{
					this.checked = 1;
					setTimeout(() => {
						this.checked = 0;
						this.$wanlshop.msg('请先同意用户协议和隐私保护声明');
					}, 300)
				}
			},
			getUrlCode() {
				// 截取url中的code方法
				var url = location.search;
				// this.winUrl = url;
				var theRequest = new Object();
				if (url.indexOf('?') != -1) {
					var str = url.substr(1);
					var strs = str.split('&');
					for (var i = 0; i < strs.length; i++) {
						theRequest[strs[i].split('=')[0]] = strs[i].split('=')[1];
					}
				}
				return theRequest;
			},
			register() {
				this.$wanlshop.to(`register?url=${this.pageroute}`);
			},
			help() {
				this.$wanlshop.to(`/pages/user/help?url=${this.pageroute}`);
			},
			queryParams(data, isPrefix = false) {
				let prefix = isPrefix ? '?' : ''
				let _result = []
				for (let key in data) {
					let value = data[key]
					// 去掉为空的参数
					if (['', undefined, null].includes(value)) {
						continue
					}
					if (value.constructor === Array) {
						value.forEach(_value => {
							_result.push(encodeURIComponent(key) + '[]=' + encodeURIComponent(_value))
						})
					} else {
						_result.push(encodeURIComponent(key) + '=' + encodeURIComponent(value))
					}
				}
				return _result.length ? prefix + _result.join('&') : ''
			}
		}
	};
</script>

<style>
	@import url("auth.css");
</style>
