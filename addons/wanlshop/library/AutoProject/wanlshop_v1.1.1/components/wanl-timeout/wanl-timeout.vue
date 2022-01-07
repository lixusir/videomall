<template>
	<view class="wanl-timeout" :style="{color: color}">
		<view v-if="content" class="wanl-timeout__content">
			{{content}}
		</view>
		<view v-else class="wanl-timeout__time">
			<view class="tips" :style="{color: companyColor}"> {{tipsContent}} </view>
			<view v-if="showDay && (day > 0)" class="character"
				:style="{ borderColor: borderColor, backgroundColor: backgroundColor, padding: padding, margin: '0 ' + margin}">
				{{day}}</view>
			<view v-if="showDay && (day > 0)" class="company"> 天 </view>
			<view class="character"
				:style="{ borderColor: borderColor, backgroundColor: backgroundColor, padding: padding, margin: '0 ' + margin}">
				{{hou}}
			</view>
			<view class="company" :style="{color: companyColor}"> {{ showColon ? ':' : '时' }} </view>
			<view class="character"
				:style="{ borderColor: borderColor, backgroundColor: backgroundColor, padding: padding, margin: '0 ' + margin}">
				{{min}}</view>
			<view class="company" :style="{color: companyColor}"> {{ showColon ? ':' : '分' }} </view>
			<view class="character"
				:style="{ borderColor: borderColor, backgroundColor: backgroundColor, padding: padding, margin: '0 ' + margin}">
				{{sec}}</view>
			<view class="company" :style="{color: companyColor}"> {{ showColon ? '′' : '秒' }} </view>
			<view v-if="showSsec" class="character"
				:style="{ borderColor: borderColor, backgroundColor: backgroundColor, padding: padding, margin: '0 ' + margin}">
				{{ssec}}</view>
			<view v-if="!showColon && showSsec" class="company" :style="{color: companyColor}"> 毫秒 </view>
		</view>
	</view>
</template>

<script>
	/**
	 * WanlTimeout 倒计时
	 * @description 倒计时组件 深圳前海万联科技有限公司 https://www.wanlshop.com（除万联官方内嵌系统，未经授权严禁使用）
	 * @著作权：WanlShop 登记号：2020SR0255711 版本：V1.1.0
	 * @property {String} endTime 时间戳
	 * @property {String} backgroundColor 背景色
	 * @property {String} color 文字颜色
	 * @property {Boolean} showDay = [true|false] 是否显示天数
	 * @property {Boolean} showColon = [true|false] 是否以冒号为分隔符
	 * @property {String} splitorColor 分割符号颜色
	 * @example <wanl-timeout endTime="1621803285" color="#222222" backgroundColor="#ffffff" borderColor="#ffffff" padding="0" margin="0" />
	 */
	export default {
		name: 'WanlTimeout',
		props: {
			endTime: {
				type: Number,
				default: ''
			},
			showDay: {
				type: Boolean,
				default: true
			},
			showSsec: {
				type: Boolean,
				default: true
			},
			showColon: {
				type: Boolean,
				default: true
			},
			tipsContent: {
				type: String,
				default: '剩余'
			},
			TimeContent: {
				type: String,
				default: '活动已结束'
			},
			backgroundColor: {
				type: String,
				default: '#007AFF'
			},
			borderColor: {
				type: String,
				default: '#000000'
			},
			companyColor: {
				type: String,
				default: '#7b7b7b'
			},
			color: {
				type: String,
				default: '#fff'
			},
			padding: {
				type: String,
				default: '4rpx'
			},
			margin: {
				type: String,
				default: '2rpx'
			}
		},
		data() {
			return {
				day: '', //天
				hou: '', //时
				min: '', //分
				sec: '', //秒
				ssec: '', //毫秒
				content: '', //倒计时完成结束语
			};
		},
		created() {
			this.time(this.endTime)
		},
		methods: {
			time(timestamp) {
				let _this = this;
				let interval = setInterval(function timestampToTime() {
					// let closeTime = new Date(timestamp).getTime();
					let data = (new Date(timestamp * 1000)) - (new Date());
					if (data > 0) {
						let time = data / 1000;

						_this.day = parseInt(time / (60 * 60 * 24));
						// 获取时、分、秒、毫秒
						_this.hou = parseInt((time % (60 * 60 * 24)) / 3600) < 10 ? ('0' + parseInt((time % (60 *
								60 * 24)) / 3600)) :
							parseInt((time % (60 * 60 * 24)) / 3600);
						_this.min = parseInt(((time % (60 * 60 * 24)) % 3600) / 60) < 10 ? ('0' + parseInt((time %
							(60 * 60 * 24) %
							3600) / 60)) : parseInt(((time % (60 * 60 * 24)) % 3600) / 60);
						_this.sec = parseInt(((time % (60 * 60 * 24)) % 3600) % 60) < 10 ? ('0' + parseInt(((time %
								(60 * 60 * 24)) %
							3600) % 60)) : parseInt(((time % (60 * 60 * 24)) % 3600) % 60);
						_this.ssec = parseInt(((data % (60 * 60 * 24)) % 3600) / 60) % 10;
					} else {
						// 自定义活动结束语
						_this.content = _this.TimeContent
						clearInterval(interval)
					}
				}, 100)
			},
		}
	}
</script>

<style lang="scss">
	.wanl-timeout {
		&__content {}

		&__time {
			display: flex;
			align-items: center;

			.character {
				border-radius: 6rpx;
				border: 2rpx solid #fff;
				line-height: 1.2;
			}
		}
	}
</style>
