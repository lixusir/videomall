<template>
	<view class="wanlshop-category-container">
		<!-- 导航条 -->
		<view class="cu-custom" :style="{ height: $wanlshop.wanlsys().height + 'px' }">
			<view
				class="cu-bar fixed bg-white solid-bottom"
				:style="{
					height: $wanlshop.wanlsys().height + 'px',
					paddingTop: $wanlshop.wanlsys().top + 'px'
				}"
			>
				<view class="action" @tap="handleGroups">
					<text class="wlIcon-pintuan3"></text>
					<!-- <view class="cu-tag badge">拼</view> -->
				</view>
				<view class="search-form round">
					<text class="wlIcon-sousuo1 text-bold"></text>
					<swiper
						class="search-swiper placeholder"
						vertical
						autoplay
						circular
						interval="3000"
					>
						<swiper-item @tap="productSearch('')">搜索商品、类目</swiper-item>
						<swiper-item
							v-for="(item, index) in common.modulesData.searchModules"
							:key="item.keywords"
							@tap="productSearch(item.keywords)"
						>
							{{ item.keywords }}
						</swiper-item>
					</swiper>
				</view>
				<!-- #ifndef MP -->
				<view class="action" @tap="showModal('menu')">
					<text class="wlIcon-gengduo"></text>
					<!-- {{( statistics.notice.notice + statistics.notice.order + statistics.notice.chat + statistics.order.pay + statistics.order.delive + statistics.order.receiving + statistics.order.evaluate + cart.cartnum )}} -->
					<view
						class="cu-tag badge bg-orange"
						v-if="
							statistics.notice.notice +
								statistics.notice.order +
								statistics.notice.chat +
								statistics.order.pay +
								statistics.order.delive +
								statistics.order.receiving +
								statistics.order.evaluate +
								cart.cartnum >
								0
						"
					></view>
				</view>
				<!-- #endif -->
			</view>
		</view>
		<!-- 加载类目 -->
		<wanl-shop-classify
			:mainHeight="height"
			:categoryStyle="parseInt(common.appStyle.category_style)"
			:categoryData="common.modulesData.categoryModules"
			:adverData="common.adData.categoryAdverts"
		/>
		<!-- 模态框 -->
		<view class="WANL-MODAL">
			<view
				class="cu-modal top-modal"
				:class="modalName == 'menu' ? 'show' : ''"
				@tap="hideModal"
			>
				<view class="wanl-modal-menu cu-dialog" @tap.stop="">
					<wanl-direct @change="hideModal" />
				</view>
			</view>
			<!-- 分享 -->
			<view
				class="cu-modal bottom-modal"
				:class="modalName == 'share' ? 'show' : ''"
				@tap="hideModal"
			>
				<view class="cu-dialog" @tap.stop="">
					<wanl-share
						:scrollAnimation="scrollAnimation"
						shareTitle="我发现了一个很好的线上购物商城，这是商城类目"
						shareText="品质好而且很省钱如果自己在上面购买，每年可以省下1%~40%的钱"
						:image="$wanlshop.appstc('/common/logo.png')"
						:href="common.appConfig.domain + '/pages/category?QRtype=category'"
						@change="hideModal"
					/>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
import { mapState } from 'vuex';
import wanlShopClassify from '@/components/wanl-shop/classify';
export default {
	data() {
		return {
			height: 0,
			modalName: null,
			scrollAnimation: 300
		};
	},
	components: {
		wanlShopClassify
	},
	computed: {
		...mapState(['common', 'statistics', 'cart'])
	},
	onLoad() {
		let wanlsys = this.$wanlshop.wanlsys();
		// #ifdef APP-PLUS
		this.height = wanlsys.windowHeight + wanlsys.windowBottom - wanlsys.height;
		// #endif
		// #ifdef H5
		this.height = wanlsys.windowHeight + wanlsys.windowBottom - wanlsys.height - 50;
		// #endif
		// #ifdef MP
		this.height = wanlsys.windowHeight - wanlsys.height;
		// #endif
		// #ifdef MP-WEIXIN
		wx.showShareMenu({
			withShareTicket: true,
			menus: ['shareAppMessage', 'shareTimeline']
		});
		// #endif
	},
	methods: {
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
		handleGroups() {
			this.$wanlshop.to('/pages/apps/groups/index');
		},
		productSearch(text) {
			this.$wanlshop.to(`/pages/page/search?type=goods&keywords=${text}`, 'fade-in', 100);
		}
	}
};
</script>

<style>
.cu-custom .cu-bar {
	z-index: 99;
}
.cu-bar .action {
	position: relative;
}

/* #ifdef MP */
.cu-bar .search-form {
	margin: 0 0 0 25rpx;
}
/* #endif */
</style>
