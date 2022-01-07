<template>
	<view class="wanlshophead-container" :style="{ height: `${headHeight}px`, paddingTop:`${headTop}px` }">
		<view class="text-xl text-bold" @tap="handleBack()">
			<text class="wlIcon-fanhui1"></text>
		</view>
		<view class="content">
			<view v-if="tabbarId === 'home'" class="home">
				<view :class="{active: headTabbarId === 'page'}" @tap="handleHeadTabbarId('page')">
					<text>推荐</text>
				</view>
				<view :class="{active: headTabbarId === 'news'}" @tap="handleHeadTabbarId('news')">
					<text>新品</text>
				</view>
				<view :class="{active: headTabbarId === 'groups'}" @tap="handleHeadTabbarId('groups')">
					<text>拼团</text>
				</view>
			</view>
			<view v-else class="search round" @tap="handleSearch">
				<text class="wlIcon-sousuo1 text-bold margin-left-bj margin-right-xs"></text>
				<text>搜索店铺内宝贝</text>
			</view>
		</view>
		<view class="text-xl">
			<text @tap="handleSearch" v-if="tabbarId === 'home'" class="wlIcon-sousuo1 margin-right"></text>
			<text @tap="showModal('menu')" class="wlIcon-gengduo"></text>
		</view>
		<!-- 模态框 -->
		<view class="WANL-MODAL wanlgroups-user-container__modal">
			<view class="cu-modal top-modal" :class="modalName == 'menu' ? 'show' : ''" @tap="hideModal">
				<view class="wanl-modal-menu cu-dialog" @tap.stop="">
					<wanl-direct @change="hideModal" />
				</view>
			</view>
			<!-- 分享 -->
			<view class="cu-modal bottom-modal" :class="modalName == 'share' ? 'show' : ''" @tap="hideModal">
				<view class="cu-dialog" @tap.stop="">
					<wanl-share :scrollAnimation="scrollAnimation" shareTitle="我发现了一个很好的线上购物商城，这是商城类目"
						shareText="品质好而且很省钱如果自己在上面购买，每年可以省下1%~40%的钱" :image="$wanlshop.appstc('/common/logo.png')"
						:href="`${$store.state.common.appConfig.domain}/pages/shop/index?id=${shopId}&QRtype=shop`"
						@change="hideModal" />
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		name: "WanlShopHead",
		props: {
			headHeight: {
				type: Number,
				default: 0
			},
			headTop: {
				type: Number,
				default: 0
			},
			shopId: {
				type: Number,
				default: 0
			},
			tabbarId: {
				type: String,
				default: ""
			},
			headTabbarId: {
				type: String,
				default: "page"
			}
		},
		data() {
			return {
				modalName: null,
				scrollAnimation: 300
			}
		},
		methods: {
			handleBack() {
				this.$wanlshop.back(1);
			},
			// 弹出层
			showModal(name) {
				// 滚动下分享
				if (name == 'share' && this.modalName != 'share') {
					setTimeout(() => {
						this.scrollAnimation = 300;
					}, 300);
				}
				this.modalName = name;
			},
			hideModal(name) {
				if (name) {
					this.showModal(name);
				} else {
					this.modalName = null;
				}
			},
			handleHeadTabbarId(name){
				this.$emit('headId', name);
			},
			handleSearch() {
				this.$wanlshop.to(`/pages/shop/product/list?shop_id=${this.shopId}`, 'fade-in', 100);
			}
		}
	}
</script>

<style lang="scss">
	.wanlshophead-container {
		position: relative;
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding-left: 22rpx;
		padding-right: 24rpx;
		/* #ifdef MP */
		padding-right: 220rpx;
		/* #endif */

		.content {
			flex: 1;

			.home {
				display: flex;
				align-items: center;
				margin: 0 25rpx;
				font-size: 32rpx;
				color: #555;
				&>view + view{
					margin-left: 40rpx;
				}
				.active{
					color: #333;
					font-size: 38rpx;
					font-weight: 600;
				}
			}

			.search {
				display: flex;
				align-items: center;
				margin: 0 25rpx;
				background-color: #f9f9f9;
				line-height: 68rpx;
				height: 68rpx;
				color: grey;
			}

		}
	}
</style>