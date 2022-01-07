<template>
	<view>
		<!-- 收银台 -->
		<view class="edgeInsetTop"></view>
		<view class="wanl-pay">
			<view class="price-box">
				<text class="text-df">{{order_no}}</text>
				<text class="wanl-pip text-price margin-top-sm">{{price}}</text>
			</view>
			<view class="list-box">
				<view class="item" v-for="(item, index) in payList" :key="index">
					<text :class="'wlIcon-'+ item.type +'-pay'"></text>
					<view class="action">
						<text class="title wanl-pip">{{item.name}}</text>
						<view v-if="item.type == 'balance'">
							<text v-if="isbalance">可用余额 ￥{{user.money}}</text>
							<text v-else>余额不足，可用余额 ￥{{user.money}}</text>
						</view>
						<view v-else>{{item.describe}}</view>
					</view>
					<view class="radio text-xxl" v-if="item.state" @tap="onSelect(index)">
						<text v-if="item.select" class="wlIcon-xuanze-danxuan wanl-orange"></text>
						<text v-else class="wlIcon-xuanze wanl-gray-light"></text>
					</view>
					<view class="radio text-xxl" v-else>
						<text class="wlIcon-xuanze-danxuan wanl-gray-light"></text>
					</view>
				</view>
			</view>
			<view v-if="disabled">
				<button class="mix-btn wanl-bg-redorange"> <text class="wlIcon-jiazai wlIconfont-spin margin-right-xs"></text>正在支付中... </button>
			</view>
			<view v-else>
				<button v-if="payNum == 1" class="mix-btn wanl-bg-redorange" @tap="confirm()"> 确认支付 </button>
				<button v-else class="mix-btn wanl-bg-redorange" @tap="confirm()"> 合并支付 </button>
			</view>
			<view class="footer text-center">
				<view class="text-sm" v-if="order_pay_no">{{order_type === 'groups' ? '拼团':''}}交易号：{{order_pay_no}}</view>
				<view> © 万联支付</view>
			</view>
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	export default {
		data() {
			return {
				token: '',
				disabled: false,
				price:'0.00',
				isbalance: false,
				order_type: 'goods',
				order_no: '',
				order_pay_no: '',
				payNum: 1, // 支付方式 独立支付-合并支付
				payList: []
			}
		},
		computed: {
			...mapState(['user'])
		},
		watch: {
			price(val, newval) {
				if(val <= parseFloat(this.$store.state.user.money)){
					this.isbalance = true;
					this.getPayment();
				}
			}
		},
		onLoad(option) {
			this.$api.post({
				url: '/wanlshop/pay/getBalance',
				success: res => {
					this.$store.commit('user/setUserInfo', {money: res});
					// 获取支付列表
					this.getPayment();
				}
			});
			if (option.data) {
				var sum = 0, 
					data = JSON.parse(option.data), 
					order_ids = [];
				if (data.length <= 1) {
					this.price = data[0].price;
					this.order_type = data[0].type;
					this.order_no = `${data[0].type == 'groups' ? '拼团':''}订单号：${data[0].order_no}`;
					this.order_pay_no = data[0].pay_no;
					this.order_id = data[0].order_id;
				}else{
					data.forEach(item => {
						sum = this.$wanlshop.bcadd(sum, item.price);
						order_ids.push(item.order_id);
					});
					this.price = sum;
					this.order_id = order_ids;
					this.payNum = data.length;
					this.order_no = '合并支付 ' + data.length + '个订单';
				}
				this.token = option.token;
			}else if(option.order_id){
				uni.showLoading({
				    title: '结算中...'
				});
				this.$api.post({
					url: '/wanlshop/pay/getPay',
					data: {
						order_id: option.order_id,
						order_type: option.order_type
					},
					success: res => {
						uni.hideLoading();
						this.token = res.token;
						this.price = res.price;
						this.order_type = res.order_type;
						this.order_no = `${res.order_type == 'groups' ? '拼团':''}订单号：${res.order_no}`;
						this.order_pay_no = res.pay_no;
						this.order_id = res.order_id;
					}
				});
			}else{
				console.log('非法访问');
			}
		},
		methods: {
			getPayment(){
				let method = 'wap';
				// #ifdef APP-PLUS
				method = 'app';
				// #endif
				// #ifdef MP-BAIDU
				method = 'mini';
				// #endif
				// #ifdef MP-WEIXIN
				method = 'miniapp';
				// #endif
				// #ifdef MP-ALIPAY
				method = 'mini';
				// #endif
				// #ifdef MP-QQ
				method = 'mini';
				// #endif
				this.payList = [{
					name: '余额支付',
					describe: '',
					type: 'balance',
					method: 'balance',
					state: this.isbalance ? true: false, // 是否可用
					select: this.isbalance ? true: false // 是否选中
				}];
				// #ifdef H5
				if(this.$jssdk.isWechat()){
					// 基本完成,后端未测试,没有完善回调，1.0.8 版本暂时屏蔽
					this.payList.push({
						name: '微信支付',
						describe: '推荐使用微信支付',
						type: 'jssdk',
						method: method,
						state: true,
						select: this.isbalance ? false : true
					});
				}else{
					this.payList.push({
						name: '支付宝',
						describe: '',
						type: 'alipay',
						method: method,
						state: true,
						select: false
					}, {
						name: '微信支付',
						describe: '推荐使用微信支付',
						type: 'wechat',
						method: method,
						state: true,
						select: this.isbalance ? false : true
					});
				}
				// #endif
				// #ifndef H5
				uni.getProvider({
				    service: "payment",
				    success: (e) => {
				        e.provider.map((value) => {
							if (value == 'alipay') {
								this.payList.push({name: '支付宝',describe: '',type: value,method: method,state: true,select: false});
							}else if(value == 'wxpay'){
								this.payList.push({name: '微信支付',describe: '推荐使用微信支付',type: 'wechat',method: method,state: true,select: this.isbalance ? false : true});
							}else if(value == 'baidu'){
								this.payList.push({name: '百度收银台',describe: '',type: value,method: method,state: true,select: false});
							}else if(value == 'appleiap'){
								this.payList.push({name: 'Apple支付',describe: '',type: 'apple',method: method,state: true,select: false});
							}
				        })
				    }
				});
				// #endif
			},
			//确认支付
			confirm() {
				let data = null;
				if (this.disabled) {
					return;
				}
				this.payList.map((value,index,array) => {
				　　if(value.select){
						data = value;
					}else{
						return;
					}
				});
				// 判断支付是否存在
				if (!data) {
					uni.showModal({
					    content: "请选择支付方式",
					    showCancel: false
					});
					return;
				}else{
					this.disabled = true;
					// 获取小程序code
					// #ifdef MP
					uni.login({
					    success: (e) => {
							this.wanlPay(data, e.code);
					    },
					    fail: (e) => {
					        uni.showModal({
					            content: "无法获取微信code,原因为: " + e.errMsg,
					            showCancel: false
					        })
					    }
					})
					// #endif
					// #ifndef MP
					this.wanlPay(data);
					// #endif
				}
			},
			async wanlPay(data, code = null){
				this.$api.post({
					url: '/wanlshop/pay/payment',
					data: {
						order_type: this.order_type,
						type: data.type,
						method: data.method,
						code: code,
						order_id: this.order_id,
						token: this.token
					},
					success: res => {
						// 余额支付/小程序支付
						if(data.type == 'balance'){
							this.$store.commit('user/setUserInfo', {
								money: this.$wanlshop.bcsub(this.$store.state.user.money, this.price)
							});
							this.paySuccess();
						}
						// 微信 H5支付
						if(data.type == 'wechat' && data.method == 'wap'){
							// 1.0.8升级 延迟弹出
							setTimeout(() => {
								uni.showModal({
								    content: '请确认微信支付是否已完成',
									confirmText: '已完成',
									cancelText: '重新支付',
								    success: response=> {
								        if (response.confirm) {
								            this.paySuccess();
								        } else if (response.cancel) {
								            this.confirm();
								        }
								    }
								});
							}, 2000);
							// 异步查询是否支付成功
							window.location.href = res;
						}
						// 支付宝 H5支付
						if(data.type == 'alipay' && data.method == 'wap'){
							this.disabled = false;
							if(this.order_type === 'goods'){
								this.$store.commit('statistics/order', {
									pay: this.$wanlshop.bcsub(this.$store.state.statistics.order.pay, this.payNum),
									delive: this.$wanlshop.bcadd(this.$store.state.statistics.order.delive, this.payNum)
								});
							}
							document.write(res);
						}
						// 微信小程序支付
						if(data.type == 'wechat' && data.method == 'miniapp'){
							uni.requestPayment({
							    appId: res.appId,
							    nonceStr: res.nonceStr,
							    package: res.package,
							    paySign: res.paySign,
								signType: res.signType,
								timeStamp: res.timeStamp,
							    success: (e) => {
							        this.paySuccess();
							    },
							    fail: (e) => {
							        uni.showModal({
							            content: "支付失败,原因为: " + e.errMsg,
							            showCancel: false
							        })
							    }
							})
						}
						// 微信JSSDK支付，返回的是配置
						if(data.type == 'jssdk' && data.method == 'wap'){
							this.$jssdk.wxpay({
								data: res,
								success: res => {
									this.paySuccess();
								},
								fail: err => {
									if (err.errMsg == "chooseWXPay:ok") {
										this.$wanlshop.msg('支付成功');
									}else if (err.errMsg == "chooseWXPay:cancel") {
										this.$wanlshop.msg('支付失败，取消支付');
									}else if (err.errMsg == "chooseWXPay:fail") {
										this.$wanlshop.msg('支付失败');
									}
								}
							})
						}
						// APP支付
						if((data.type == 'alipay' || data.type == 'wechat') && data.method == 'app'){
							let provider = data.type;
							if(data.type == 'wechat'){
								provider = 'wxpay';
							}
							uni.requestPayment({
							    provider: provider,
							    orderInfo: res,
							    success: (e) => {
							        console.log("success", e);
							        this.paySuccess();
							    },
							    fail: (e) => {
							        uni.showModal({
							            content: "支付失败,原因为: " + e.errMsg,
							            showCancel: false
							        })
							    },
							    complete: () => {
									this.disabled = false;
							    }
							})
						}
					}
				});
			},
			onSelect(key){
				this.payList.map((value,index,array) => {
				　　if(index == key){
						value.select = !value.select;
					}else{
						value.select = false;
					}
				});
			},
			// 支付成功
			paySuccess(){
				this.disabled = false;
				if(this.order_type === 'goods'){
					this.$store.commit('statistics/order', {
						pay: this.$wanlshop.bcsub(this.$store.state.statistics.order.pay, this.payNum),
						delive: this.$wanlshop.bcadd(this.$store.state.statistics.order.delive, this.payNum)
					});
				}
				this.$wanlshop.to(`/pages/page/success?type=pay&order_type=${this.order_type ? this.order_type:'goods'}`);
			}
		}
	}
</script>

<style>
	page{
		background-color: white;
	}
	radio-group {
	    display: block;
	}
</style>
