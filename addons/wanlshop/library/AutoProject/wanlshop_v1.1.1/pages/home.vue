<template>
	<view class="wanladvert-container" :style="{ height: windowHeight + 'px' }">
		<video v-if="common.adData.openAdverts.type == 'video'" :style="{ height: windowHeight + 'px', width: '100%' }"
			id="advertVideo" ref="advertVideo"
			:src="common.adData.openAdverts.media ? common.adData.openAdverts.media : ''" :direction="0"
			objectFit="fill" :controls="false" autoplay muted loop>
			<cover-view class="advert-info" :style="{ top: statusBar + 'px' }">预加载系统中...</cover-view>
			<cover-view class="advert-btn" @tap="outBtn" :style="{ bottom: statusBar + 'px' }">跳过 {{ countdown }}
			</cover-view>
			<cover-image class="advert-logo" :style="{ top: statusBar + 'px' }"
				:src="$wanlshop.appstc('/common/wanlian4@2x.png')" />
		</video>
		<view v-else>
			<image :style="{ height: windowHeight + 'px', width: '100%' }"
				:src="common.adData.openAdverts.media ? $wanlshop.oss(common.adData.openAdverts.media, 414, 0, 2, 'transparent', 'png') : ''"
				mode="aspectFill" />
			<view class="advert-info" :style="{ top: statusBar + 'px' }">预加载系统中...</view>
			<view class="advert-btn" @tap="outBtn" :style="{ bottom: statusBar + 'px' }">跳过 {{ countdown }}</view>
			<image class="advert-logo" :style="{ top: statusBar + 'px' }"
				:src="$wanlshop.appstc('/common/wanlian4@2x.png')" mode=""></image>
		</view>
	</view>
</template>
<script>
	import {
		mapState
	} from 'vuex';
	export default {
		data() {
			return {
				windowHeight: 0,
				statusBar: 0,
				countdown: 9, // 修改广告倒计时
				cTimer: null
			};
		},
		computed: {
			...mapState(['common'])
		},
		onReady() {
			var wanlsys = this.$wanlshop.wanlsys();
			this.windowHeight = wanlsys.windowHeight;
			// #ifdef APP-PLUS
			plus.navigator.setFullscreen(true);
			this.statusBar = wanlsys.top - 4;
			// #endif
			// #ifdef MP
			this.statusBar = wanlsys.top  + 5;
			// #endif
			// #ifdef H5
			this.statusBar = 15;
			// #endif
			uni.createVideoContext('advertVideo').hideStatusBar();
		},
		onLoad() {
			uni.getNetworkType({
				success: (res) => {
					if (res.networkType == 'none') {
						uni.redirectTo({
							url: '/pages/page/no_network',
							animationType: 'fade-in',
							animationDuration: 200
						});
					} else {
						this.loadExecution();
					}
				}
			});
		},
		methods: {
			loadExecution() {
				/**
				 * 获取本地存储中launchFlag的值
				 * 若存在，说明不是首次启动，直接进入首页；
				 * 若不存在，说明是首次启动，进入引导页；
				 */
				try {
					const value = uni.getStorageSync('wanlshop:launch');
					if (value || this.$store.state.common.adData.firstAdverts.length == 0) {
						if (value == true) {
							this.startTimer();
						} else {
							this.clearTimerGuide();
						}
					} else {
						uni.setStorage({
							key: 'wanlshop:launch',
							data: true,
							success: res => {
								console.log('存储launchFlag');
							}
						});
						this.clearTimerGuide();
					}
				} catch (e) {
					// error
					uni.setStorage({
						key: 'wanlshop:launch',
						data: true,
						success: res => {
							console.log('error时存储launchFlag');
						}
					});
					this.clearTimerGuide();
				}
				return;
				this.clearTimerIndex();
			},

			outBtn() {
				this.clearTimerIndex();
			},
			outImage() {
				this.clearTimerIndex();
			},
			startTimer() {
				if (this.cTimer == null) {
					this.cTimer = setInterval(() => {
						this.countdown--;
						if (this.countdown == 0) {
							this.clearTimerIndex();
						}
					}, 1000);
				}
			},
			clearTimerIndex() {
				clearInterval(this.cTimer);
				this.cTimer = null;
				uni.switchTab({
					url: '/pages/index'
				});
			},
			clearTimerGuide() {
				uni.redirectTo({
					url: '/pages/guide'
				});
				clearInterval(this.cTimer);
				this.cTimer = null;
			}
		}
	};
</script>

<style lang="scss">
	.wanladvert-container {
		position: relative;
		width: 100%;
		overflow: hidden;

		.advert-info {
			position: absolute;
			height: 60rpx;
			width: 170rpx;
			text-align: right;
			line-height: 60rpx;
			right: 30rpx;
			/* #ifdef MP */
			right: 210rpx;
			/* #endif */
			font-size: 24rpx;
			color: rgba(255, 255, 255, .7);
		}

		.advert-btn {
			position: absolute;
			right: 32rpx;
			background: rgba(0, 0, 0, .4);
			border-radius: 20rpx;
			font-size: 28rpx;
			color: #ffffff;
			width: 140rpx;
			height: 66rpx;
			line-height: 66rpx;
			text-align: center;
		}

		.advert-logo {
			position: absolute;
			left: 40rpx;
			width: 252rpx;
			height: 56rpx;
		}
	}
</style>
