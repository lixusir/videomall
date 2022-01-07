<template>
    <view class="wanlshop-shop-container" :style="{
		backgroundImage: `url()`
	}">
		<!-- 顶部导航 -->
		<view :style="{ height: `${sys.headHeight}px`}">
			<wanl-shop-head
			class="wanlshop-shop-container__head"
			:headHeight="sys.headHeight" 
			:headTop="sys.headTop" 
			:tabbarId="tabbarId" 
			:headTabbarId="headTabbarId"
			:shopId="shopData.id"
			@headId="handleHeadTabbarId"
			/>
		</view>
		<!-- 主体内容 -->
		<view class="wanlshop-shop-container__main">
			<swiper class="page"
				v-show="tabbarId === 'home'"
				:current-item-id="headTabbarId" 
				:style="{ height: `${sys.mainHeight}px`}"
				@change="changeCurrent"
				@animationfinish="animationFinish">
				<!-- 主页 -->
				<swiper-item item-id="page"> 
					<wanl-shop-page :windowHeight="sys.mainHeight" :pageModules="shopData.page" :shopData="shopData" @handleHandleFollow="handleFollow"/>
				</swiper-item>
				<!-- 新品 -->
				<swiper-item item-id="news">
					<wanl-shop-product :windowHeight="sys.mainHeight" :tabbarHeight="sys.tabbarHeight" :tabId="headTabbarId" :option="{shop_id: shopData.id}"/>
				</swiper-item>
				<!-- 拼团 -->
				<swiper-item item-id="groups">
				    <wanl-shop-product :windowHeight="sys.mainHeight" :tabbarHeight="sys.tabbarHeight" :tabId="headTabbarId" :option="{shop_id: shopData.id}"/>
				</swiper-item>
			</swiper>
			<!-- 全部宝贝 -->
			<wanl-shop-product v-if="tabbarId === 'goods'" class="goods" :windowHeight="sys.mainHeight" :tabbarHeight="sys.tabbarHeight" :tabId="tabbarId" :option="{shop_id: shopData.id}"/>
			<!-- 发现 -->
			<wanl-shop-find  
				v-if="tabbarId === 'find'" 
				class="find" 
				:windowHeight="sys.mainHeight" 
				:tabbarHeight="sys.tabbarHeight" 
				:userNo="shopData.find_user.user_no"
			/>
			<!-- 分类 -->
			<wanl-shop-classify v-if="tabbarId === 'classify'" class="classify" :shopId="shopData.id" :mainHeight="sys.mainHeight" :categoryStyle="shopData.categoryStyle === 0 ? 1 : shopData.categoryStyle" :categoryData="shopData.category" />
		</view>
		<!-- 底部导航 -->
		<view class="wanlshop-shop-container__tabbar" :style="{ height: `${sys.tabbarHeight}px`, paddingBottom:`${sys.tabbarBottom}px` }">
			<view class="cu-bar tabbar">
				<view class="action" @tap="handleTabbar('home')">
					<view v-if="handleActive('home')">
						<view class="cu-avatar round" :style="{backgroundImage: `url(${$wanlshop.oss(shopData.avatar, 32, 32)})`}" />
					</view>
					<view v-else>
						<view class="wlIcon-dianpu text-bold"></view>
						<view>首页</view>
					</view>
				</view>
				<view class="action" :class="{ active: handleActive('goods') }" @tap="handleTabbar('goods')">
					<view class="wlIcon-baobei text-bold"></view>
					<view>全部宝贝</view>
				</view>
				<view class="action" :class="{ active: handleActive('find') }" @tap="handleTabbar('find')">
					<view class="wlIcon-guanzhu1 text-bold"></view>
					<view>发现动态</view>
				</view>
				<view class="action" :class="{ active: handleActive('classify') }" @tap="handleTabbar('classify')">
					<view class="wlIcon-fenlei1 text-bold"></view>
					<view>店铺分类</view>
				</view>
				<view class="action">
					<view v-if="user.id === shopData.user_id" @tap="$wanlshop.to('/pages/apps/find/user')">
						<view class="wlIcon-guanfang text-bold"></view>
						<view>创作中心</view>
					</view>
					<view v-else @tap="toChat(shopData.id)">
						<view class="wlIcon-kefu text-bold"></view>
						<view>客服</view>
					</view>
				</view>
			</view>
		</view>
    </view>
</template>

<script>
	import {mapState} from 'vuex';
	import wanlShopPage from '@/components/wanl-shop/page';
	import wanlShopClassify from '@/components/wanl-shop/classify';
	import wanlShopHead from '@/components/wanl-shop/shop/head';
	import wanlShopProduct from '@/components/wanl-shop/shop/product';
	import wanlShopFind from '@/components/wanl-shop/shop/find';
	export default {
	    data() {
	        return {
				tabbarId: 'home',
				headTabbarId: 'page',
				sys: {
					headTop: 0,
					headHeight: 0,
					mainHeight: 0,
					tabbarBottom: 0,
					tabbarHeight: 0,
				},
	            shopData: {
					id: 0,
					avatar: '',
					shopname: '',
					state: 0,
					islive: 0,
					isself: 0,
					categoryStyle: 1,
					find_user:{
						user_no: '',
						fans: 0
					}
				}
	        }
	    },
		components: {
			wanlShopHead,
			wanlShopPage,
			wanlShopProduct,
			wanlShopClassify,
			wanlShopFind
		},
		computed: {
			...mapState(['user'])
		},
		onShow() {
			// setTimeout(() => {
			// 	uni.setNavigationBarColor({
			// 		frontColor: this.$store.state.common.modulesData.homeModules.page 
			// 			? this.$store.state.common.modulesData.homeModules.page.style.navigationBarTextStyle 
			// 			: '',
			// 		backgroundColor: this.$store.state.common.modulesData.homeModules.page
			// 			? this.$store.state.common.modulesData.homeModules.page.style.navigationBarBackgroundColor 
			// 			: ''
			// 	})
			// }, 200);
		},
		onReady() {
			let sys = uni.getSystemInfoSync(),
				headHeight = sys.safeAreaInsets.top + uni.upx2px(88),
				tabbarHeight = sys.safeAreaInsets.bottom + uni.upx2px(100);
			this.sys = {
				headTop: sys.safeAreaInsets.top,
				headHeight: headHeight,
				mainHeight: sys.windowHeight - headHeight,
				tabbarBottom: sys.safeAreaInsets.bottom,
				tabbarHeight: tabbarHeight
			}
		},
		onLoad(option) {
			this.loadData(option.id);
			if(option.type){
				this.tabbarId = option.type;
			}
		},
	    methods: {
			async loadData(id) {
				this.$api.get({
					url: '/wanlshop/shop/getShopInfo',
					data: {id: id},
					success: res => {
						this.shopData = res
					}
				});
			},
			async handleFollow() {
				if (this.$store.state.user.isLogin) {
					this.$api.post({
						url: '/wanlshop/find/user/setFindUser',
						data: {
							id: this.shopData.find_user.user_no,
							type: 'follow'
						},
						success: res => {
							let concern = this.$store.state.statistics.dynamic.concern;
							if (res.data === 0) {
								concern -= 1;
								this.shopData.find_user.fans -= 1;
							} else {
								concern += 1;
								this.shopData.find_user.fans += 1;
							}
							this.$store.commit('statistics/dynamic', {
								concern: concern,
							});
							this.shopData.isFollow = res.data;
						}
					});
				} else {
					this.$wanlshop.to('/pages/user/auth/auth')
				}
			},
			// 动画
			animationFinish(e) {
				//#ifdef APP-PLUS
				this.changeCurrent(e)
				//#endif
			},
			// 滚动的tag
			changeCurrent(e) {
				this.headTabbarId = e.detail.currentItemId;
			},
			handleTabbar(name){
				this.tabbarId = name;
			},
			handleActive(name){
				return this.tabbarId === name;
			},
			handleHeadTabbarId(name){
				this.headTabbarId = name;
			},
	    }
	}
</script>

<style lang="scss">
	.wanlshop-shop-container{
		background-color: #ffffff;
		&__head{
			position: fixed;
			top: 0;
			z-index: 999;
			width: 100%;
			background-color: #ffffff;
		}
		&__main{
			.classify{
				background-color: #f9f9f9;
			}
			.find{
				background-color: #f9f9f9;
				border-radius: 30rpx 30rpx 0 0;
				overflow: hidden;
			}
		}
		&__tabbar{
			position: fixed;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: rgba(255,255,255,.96);
			z-index: 999;
			.cu-bar{
				color: #555;
				.action{
					[class*="wlIcon-"]{
						font-size: 45rpx;
					}
					
					&.active{
						color: #f40;
					}
					.cu-avatar{
						background-color: #f1f1f1;
						height: 74rpx;
						width: 74rpx;
					}
				}
			}
		}
	}
</style>