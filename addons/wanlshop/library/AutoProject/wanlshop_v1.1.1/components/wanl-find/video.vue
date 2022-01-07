<!-- 
	版本声明：
	* 由于 WanlLive、WanlChat、WanlPay 以下代码开发难度较大，已将相关代码独立申请著作权，受法律保护！！！
	* 无论你购买任何版本，均不允许复制到第三方直接、间接使用，且也不能以学习为目的参考和借鉴！！
	* 你仅有在 WanlShop 中使用、二次开发权利，否则我们会追究法律责任 
	* 深圳前海万联科技有限公司 @www.i36k.com
	* 法律顾问：易法通 @http://www.yifatong.com/
-->
<template>
	<view class="flex justify-center align-center" :style="{ width: `${width}px` }">
		<!-- 1.0.9临时 -->
		<!-- :initial-time="startTime" -->
		<video
			:id="`video${videoId}`"
			ref="`video${videoId}`"
			class="flex justify-center align-center"
			:style="{ height: `${height}px`, width: `${width}px` }"
			:src="src"
			:controls="false"
			:show-play-btn="false"
			:enable-progress-gesture="false"
			:object-fit="videoFit"
			:muted="!initialize || !play"
			loop
			@play="continuePlay"
			@timeupdate="timeupdate"
		/>
		<view class="position-absolute" v-if="!play && !OpenCover">
			<text class="icon">&#xe60f;</text>
		</view>
		<!-- 封面图片 -->
		<view class="position-absolute" v-if="OpenCover">
			<image
				:style="{ height: `${height}px`, width: `${width}px` }"
				:src="coverUrl"
				mode="aspectFill"
			/>
			<!-- #ifndef H5 -->
			<view class="position-center align-center justify-center">
				<text class="icon icon-spin">&#xe607;</text>
			</view>
			<!-- #endif -->
		</view>
		<!-- 上阴影 -->
		<view class="video-top"/>
		<!-- 下阴影 -->
		<view class="video-bottom"/>
	</view>
</template>

<script>
	export default {
		props: {
			videoId: {
				type: Number,
				default: 0
			},
			src: {
				type: String,
				default: ''
			},
			play: {
				type: Boolean,
				default: false
			},
			height: {
				type: Number,
				default: 0
			},
			width: {
				type: Number,
				default: 0
			},
			
			objectFit: {
				type: String,
				default: 'cover'
			},
			coverUrl: { //视频封面的图片
				type: String,
				default: ''
			},
			initialTime: {
				type: Number,
				default: 0
			},
			gDuration: {
				type: Number,
				default: 999
			}
		},
		data() {
			return {
				videoFit: 'cover', // 优化HBuilder展示bug
				time: 0, // 时间类
				duration: 0,  // 视频时长
				OpenCover: true,  // 是否开启封面
				initialize: false, // 初始化
				videoTimer: null,// 计时器
			}
		},
		mounted() {
			// 修复HBuilder X 3.1.22 App端视频展示BUG，如果官方修复此问题video直接调用objectFit
			this.videoFit = this.objectFit
			// #ifdef APP-PLUS
			let sys = uni.getSystemInfoSync();
			if(sys.platform === 'ios'){
				if(this.objectFit === 'cover'){
					this.videoFit = 'fill'
				}
			}
			// #endif
			let video = `video${this.videoId}`;
			this.videoContext = uni.createVideoContext(video, this);
			setTimeout(() => {
				this.videoContext.play();
			}, 10);
		},
		methods: {
			continuePlay() {
				if (!this.initialize) {
					setTimeout(() => {
						this.initialize = true;
						this.videoPlay();
					}, 100);
				}
			},
			// 拖动滑块 1.0.9临时
			// changeCurrent(e) {
			// 	this.time = e.detail.value;
			// 	this.videoContext.seek(this.time);
			// },
			timeupdate(event) {
				// 1.0.9临时
				// this.duration = event.detail.duration;
				// if (this.time >= event.detail.duration) this.time = 0;
				// this.time = event.detail.currentTime;
			},
			videoPlay() {
				if (this.videoTimer) {
					clearTimeout(this.videoTimer);
				}
				this.videoTimer = setTimeout(() => {
					if (this.play) {
						this.videoContext.play();
						this.OpenCover = false;
					} else {
						this.videoContext.pause();
						// 1.0.9临时
						// this.$emit('pause', this.time);
					}
				});
				// if (this.play) {
				// 	this.videoContext.play();
				// 	this.OpenCover = false;
				// } else {
				// 	this.videoContext.pause();
				// 	this.$emit('pause', this.time);
				// }
			}
		},
		watch: {
			//防抖 防止视频播放暂停太快
			play: function(newVal, oldVal) {
				if (this.initialize) this.videoPlay();
			},
			// 1.0.9临时
			// startTime: {
			// 	immediate: true,
			// 	handler(newVal, oldVal) {
			// 		this.time = newVal;
			// 	}
			// },
			gDuration: {
				immediate: true,
				handler(newVal, oldVal) {
					this.duration = newVal;
				}
			}
		},
		computed: {
			barWidth() {
				let width = (this.time / this.duration) * parseInt(this.width);
				return `${width}px`;
			},
			// 1.0.9临时
			// startTime() {
			// 	return this.initialTime;
			// }
		}
	}
</script>
<style scoped>
	@import '@/static/style/wanlnvue.css';
	
	
	.icon{
		opacity: 0.6;
		font-size: 120rpx;
		color: #fff;
	}
	.icon-spin{
		//#ifndef APP-PLUS-NVUE
		animation: spin 1.5s infinite linear;
		//#endif
	}
	.video-top{
		position: absolute;
		top:0;
		background-image: linear-gradient(to top , rgba(0,0,0,0) , rgba(0,0,0,0.4));
		width: 750rpx;
		height: 300rpx;
	}
	.video-bottom{
		position: absolute;
		bottom: 0;
		background-image: linear-gradient(to top , rgba(0,0,0,0.4) , rgba(0,0,0,0));
		width: 750rpx;
		height: 300rpx;
	}
	.slider-view{
		position: absolute;
		left: 0;
		bottom: 30px;
		width: 750rpx;
	}
	.progressBar{
		border-radius: 2rpx;
		height: 4rpx;
		background-color: rgba(255,255,255,0.3);
		z-index: 999999;
		position: absolute;
		left: 0;
		bottom: 0;
		//#ifndef APP-PLUS-NVUE
		animation: flicker 4s linear infinite;
		animation-direction:alternate;
		//#endif
	}
	/* #ifndef APP-PLUS-NVUE */
	@keyframes flicker {
		0% { box-shadow:0 0 0 #FFFFFF; }
	     /** 暂停效果 */
		10% { box-shadow:0 0 2upx #FFFFFF; }
	    50% { box-shadow:0 0 10upx #FFFFFF; }
	    60% { box-shadow:0 0 12upx #FFFFFF; }
	    90% { box-shadow:0 0 18upx #FFFFFF; }
	    100% { box-shadow:0 0 20upx #FFFFFF; }
	}
	
	@keyframes spin {
		0% {
			-webkit-transform: rotate(0);
			transform: rotate(0);
		}
	
		100% {
			-webkit-transform: rotate(359deg);
			transform: rotate(359deg);
		}
	}
	/* #endif */
</style>

