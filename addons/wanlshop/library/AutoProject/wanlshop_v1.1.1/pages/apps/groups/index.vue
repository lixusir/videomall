<template>
	<view class="wanlgroups-container">
		<view class="wanlgroups-container__head"
			style="background-size: 100% auto; background-repeat: no-repeat;"
			:style="{
				height: headHeight + 'px',
				color: common.appStyle.groups_font_color === 'light' ? '#ffffff' : '#333333',
				backgroundColor: common.appStyle.groups_nav_color ? common.appStyle.groups_nav_color : '#f7f7f7',
				backgroundImage: 'url(' + $wanlshop.oss(common.appStyle.groups_nav_image, 0, 50, 1, 'transparent', 'png') + ')'
			}">
			<view :style="{
				height: headHeight + 'px', 
				paddingTop: headTop + 'px'
			}">
				<view class="navigater flex align-center justify-between">
					<view class="flex">
						<view class="text-xxl" @tap="handleBack">
							<text class="wlIcon-fanhui1"></text>
						</view>
					</view>
					<view class="search flex align-center margin-lr-sm round">
						<view class="icon text-df text-bold wanl-gray-dark">
							<text class="wlIcon-sousuo1"></text>
						</view>
						<swiper vertical autoplay circular interval="3000">
							<swiper-item @tap="handleSearch('')">
								<text class="wanl-gray-dark text-cut">搜索 拼团商品、拼团类目</text>
							</swiper-item>
							<swiper-item v-for="(item, index) in common.modulesData.searchModules" :key="item.keywords" @tap="handleSearch(item.keywords)">
								<text class="wanl-gray-dark text-cut">{{ item.keywords }}</text>
							</swiper-item>
						</swiper>
					</view>
					<view class="flex">
						<view class="text-xxl" @tap="handleGroupsOrder">
							<text class="wlIcon-listing-jf"></text>
						</view>
					</view>
				</view>
				<view class="toolbar flex padding-lr-bj align-center">
					<scroll-view class="scroll" scroll-x scroll-with-animation :scroll-left="scrollLeft">
						<view class="scroll__item" :class="{action: currentItemId === 'cid0'}"
							@tap="handleSelect('cid0', 0)">
							全部
						</view>
						<view class="scroll__item" v-for="(item, index) in common.modulesData.categoryModules"
							:key="item.id" :class="{action: currentItemId === 'cid' + item.id}"
							@tap="handleSelect('cid' + item.id, index + 1)">
							{{item.name}}
						</view>
					</scroll-view>
				</view>
			</view>
		</view>
		<!-- 主体 -->
		<swiper class="wanlgroups-container__main" :current-item-id="currentItemId" :style="{
				height: windowHeight + 'px'
			}" @change="changeCurrent" @animationfinish="animationFinish">
			<!-- 发现页 -->
			<swiper-item item-id="cid0">
				<wanl-groups :headHeight="headHeight"
					:windowHeight="windowHeight" :childlist="common.modulesData.categoryModules"
					:currentItemId="currentItemId" @handleMenu="handleMenu" />
			</swiper-item>
			<!-- 分类 -->
			<swiper-item v-for="(item, index) in common.modulesData.categoryModules" :key="item.id"
				:item-id="'cid' + item.id">
				<wanl-groups 
					:headHeight="headHeight"
					:windowHeight="windowHeight"
					:childlist="common.modulesData.categoryModules[index].childlist" :itemId="item.id"
					:currentItemId="currentItemId" />
			</swiper-item>
		</swiper>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';

	export default {
		computed: {
			...mapState(['common'])
		},
		data() {
			return {
				headHeight: 0,
				windowHeight: 0,
				headTop: 0,
				currentItemId: 'cid0',
				scrollLeft: 0
			};
		},
		onShow() {
			setTimeout(() => {
				uni.setNavigationBarColor({
					frontColor: this.$store.state.common.appStyle.groups_font_color === 'light' ? '#ffffff' : '#000000'
				})
			}, 200);
		},
		onLoad(option) {
			// 计算页面尺寸
			let sys = this.$wanlshop.wanlsys();
			this.headTop = sys.top;
			this.headHeight = sys.height + uni.upx2px(60);
			this.windowHeight = sys.windowHeight;
			
			// 打开类目
			if (option.id) {
				this.currentItemId = 'cid' + option.id;
			}
		},
		methods: {
			// 选择Tag
			handleSelect(id, index) {
				this.currentItemId = id;
				this.scrollLeft = (index - 1) * 50;
			},
			handleMenu(e) {
				this.currentItemId = e.id;
				this.scrollLeft = (e.index - 1) * 50;
			},
			handleBack() {
				this.$wanlshop.back(1);
			},
			// 搜索
			handleSearch(text) {
				this.$wanlshop.to(`/pages/page/search?type=groups&keywords=${text}`, 'fade-in', 100);
			},
			handleGroupsOrder(){
				this.$wanlshop.auth('/pages/apps/groups/order/order');
			},
			// 动画
			animationFinish(e) {
				//#ifdef APP-PLUS
				this.changeCurrent(e)
				//#endif
			},
			// 滚动的tag
			changeCurrent(e) {
				this.currentItemId = e.detail.currentItemId;
				this.scrollLeft = (e.detail.current - 1) * 50;
			}
		}
	}
</script>

<style lang="scss">
	.wanlgroups-container {
		&__head {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			z-index: 999;
			.navigater{
				height: 86rpx;
				padding-left: 25rpx;
				padding-right: 25rpx;
				/* #ifdef MP */
				padding-right: 220rpx;
				/* #endif */
				.search{
					flex: 1;
					background-color: #fff;
					height: 66rpx;
					border: 2rpx solid #fff;
					.icon{
						margin: 0 20rpx;
					}
					swiper{
						height: 100%;
						width: 100%;
						margin-right: 10rpx;
						swiper-item{
							display: flex;
							align-items: center;
						}
					}
				}
			}
			.toolbar{
				.scroll{
					flex: 1;
					white-space: nowrap;
					overflow: hidden;
					width: 100%;
						&__item{
							position: relative;
							z-index: 2;
							font-size: 26rpx;
							display: inline-flex;
							height: 58rpx;
							align-items: center;
							margin-right: 40rpx;
							&.action{
								position: relative;
								font-weight: bold;
								font-size: 30rpx;
								&::after{
									content: ' ';
									position: absolute;
									bottom: 0;
									left: 50%;
									transform: translateX(-50%);
									height: 4rpx;
									width: 30rpx;
									border-radius: 6rpx;
									background-color: #fff;
								}
							}
						}
				}
				.category{
					box-shadow: #eee -16rpx 0 16rpx -16rpx;
					height: 58rpx;
					font-size: 28rpx;
					padding-left: 25rpx;
				}
			}
		}
		&__main{
			position: relative;
			z-index: 99;
		}
	}
</style>