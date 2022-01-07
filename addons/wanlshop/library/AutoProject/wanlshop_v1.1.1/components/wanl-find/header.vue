<!-- 
	版本声明：
	* 由于 WanlLive、WanlChat、短视频NUVE页面、以下代码开发难度较大，已将相关代码独立申请著作权，受法律保护！！！
	* 无论你购买任何版本，均不允许复制到第三方直接、间接使用，且也不能以学习为目的参考和借鉴！！
	* 你仅有在 WanlShop 中使用、二次开发权利，否则我们会追究法律责任 
	* 深圳前海万联科技有限公司 @www.i36k.com
-->

<template>
	<!-- 导航条 -->
	<view class="position-top-center" :style="{ height: height + 'px' }">
		<view class="header-bar padding-lr-bj flex-direction justify-between align-center" :style="{ 
				height: height + 'px', 
				paddingTop: top + 'px', 
				backgroundColor: currentItemId === 'find' ? navColor : 'transparent' 
		}">
			<view class="flex-direction align-center">
				<view @tap="handleRelease">
					<text class="icon text-xxxl margin-right">&#xe61d;</text>
				</view>
				<view v-for="(item, index) in currentList" :key="index" class="header-tag position-relative align-center justify-center" @tap="onChange(item.type)">
					<text class="text-34 text-white-7" :class="{active: currentItemId === item.type}">{{item.name}}</text>
					<view v-if="currentItemId === item.type" class="header-tag-active"></view>
				</view>
			</view>
			<view class="flex-direction">
				<view class="osition-relative margin-left" @tap="handleSearch">
					<text class="text-40 icon">&#xe62b;</text>
				</view>
				<!-- #ifndef MP-TOUTIAO -->
				<view class="position-relative margin-left" @tap="handleMyFind">
					<text class="text-40 icon">&#xe7d8;</text>
					<view class="header-icon-tag"></view>
				</view>
				<!-- #endif -->
				<!-- #ifdef MP-TOUTIAO -->
				<view class="position-relative margin-left" style="margin-right: 220rpx;" @tap="handleMyFind">
					<text class="text-40 icon">&#xe7d8;</text>
					<view class="header-icon-tag"></view>
				</view>
				<!-- #endif -->
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: "wanlFindHeader",
		props: {
			navColor: {
				type: String,
				default: '#fd4705'
			},
			currentItemId: {
				type: String,
				default: ''
			},
			currentList: {
				type: Array,
				default () {
					return []
				}
			},
			height: {
				type: Number,
				default: 0
			},
			top: {
				type: Number,
				default: 0
			},
			isShop: {
				type: Boolean,
				default: false
			},
			isVideo: {
				type: Boolean,
				default: false
			},
			isLive: {
				type: Boolean,
				default: false
			}
		},
		methods: {
			onChange(e) {
				this.$emit('onChange', e);
		    },
			// 点击我的发现
			handleMyFind(){
				if(this.$store.state.user.isLogin){
					uni.navigateTo({
						url: `/pages/apps/find/user`
					})
				}else{
					uni.navigateTo({
						url: `/pages/user/auth/auth`
					})
				}
			},
			// 点击搜索
			handleSearch(){
				uni.navigateTo({
					url: `/pages/page/search?type=find`
				})
			},
			// 点击发布
			handleRelease(){
				if(this.$store.state.user.isLogin){
					let itemList = [], url = [];
					if(this.isShop){
						if(this.isVideo){
							if(this.isLive){
								itemList = ['发布种草', '发布短视频', '商家直播'];
								url = ['/pages/apps/find/user/add?type=want', '/pages/apps/find/user/add?type=video', '/pages/shop/live/live'];
							}else{
								itemList = ['发布种草', '发布短视频'];
								url = ['/pages/apps/find/user/add?type=want', '/pages/apps/find/user/add?type=video'];
							}
						}else{
							if(this.isLive){
								itemList = ['发布种草', '商家直播'];
								url = ['/pages/apps/find/user/add?type=want', '/pages/apps/find/user/add?type=video'];
							}else{
								itemList = ['发布种草'];
								url = ['/pages/apps/find/user/add?type=want'];
							}
						}
					}else{
						if(this.isVideo){
							itemList = ['发布种草', '发布短视频'];
							url = ['/pages/apps/find/user/add?type=want', '/pages/apps/find/user/add?type=video'];
						}else{
							itemList = ['发布种草'];
							url = ['/pages/apps/find/user/add?type=want'];
						}
					}
					uni.showActionSheet({
					    itemList: itemList,
					    success: res => {
							if(res.tapIndex === 2){
								// #ifdef APP-PLUS || MP-WEIXIN
								uni.navigateTo({
									url: url[res.tapIndex]
								})
								// #endif
								// #ifndef APP-PLUS || MP-WEIXIN
								uni.showToast({
								    title: '目前只开放App和微信小程序直播',
								    icon:'none'
								});
								// #endif
							}else{
								uni.navigateTo({
									url: url[res.tapIndex]
								})
							}
					    }
					});
				}else{
					uni.navigateTo({
						url: `/pages/user/auth/auth`
					})
				}
			},
		}
	};
</script>

<style>
	@import '@/static/style/wanlnvue.css';
	.header-icon-tag{
		position: absolute;
		right: -8rpx;
		top: -4rpx;
		width: 16rpx;
		height: 16rpx;
		border-radius: 2000%;
		background-color: red;
	}
	.header-bar {
		/* #ifdef MP */
		padding-right: 220rpx;
		/* #endif */
	}
	.header-tag{
		height: 71rpx;
		width: 110rpx;
	}
	.header-tag-active{
		position: absolute;
		bottom: 0;
		background-color: #fff;
		width: 22rpx;
		height: 4rpx;
		border-radius: 200rpx;
	}
	.active{
		font-weight: bold;
		color: #fff;
	}
</style>