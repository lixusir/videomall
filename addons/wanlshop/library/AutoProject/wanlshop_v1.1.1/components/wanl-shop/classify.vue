<template>
	<view class="wanlshop-classify-container">
		<block v-if="categoryStyle === 1">
			<scroll-view :style="{height: `${mainHeight}px`}" scroll-y class="wanlshop-classify-container__one">
				<view class="item" v-for="(item,index) in categoryData" :key="item.id" @tap="handleProduct(item.id, item.name)">
					<image :src="$wanlshop.oss(item.image, 350, 150)" class="list-image" />
					<view class="category-title">
						<view>{{ item.description }}</view>
						<text class="text-sm text-bold text-black">- {{ item.name }} -</text>
					</view>
				</view>
			</scroll-view>
		</block>
		<block v-else-if="categoryStyle === 2">
			<scroll-view class="wanlshop-classify-container__grid" :style="{height: `${mainHeight}px`}" scroll-y>
				<view class="cu-list grid col-3 no-border">
					<view class="cu-item" v-for="(item,index) in categoryData" :key="item.id" @tap="handleProduct(item.id, item.name)">
						<image :src="$wanlshop.oss(item.image, 80, 80)" class="list-image" />
						<text>{{ item.name }}</text>
					</view>
					<view class="safeAreaBottom"></view>
				</view>
			</scroll-view>
		</block>
		<block v-else-if="categoryStyle === 3">
			<view class="wanlshop-classify-container__list">
				<scroll-view scroll-y scroll-with-animation class="left" :scroll-top="scrollTop" :style="{ height: `${mainHeight}px` }">
					<view v-for="(item,index) in categoryData" :key="item.id" class="item"
						:class="[currentTab == index ? 'active' : '']" :data-current="index" @tap.stop="handleNav">
						<text>{{ item.name }}</text>
					</view>
				</scroll-view>
				<block v-for="(item,index) in categoryData" :key="item.id">
					<scroll-view scroll-y class="right" :style="{ height: `${mainHeight}px` }" v-if="currentTab == index">
						<view class="list-cat">
							<swiper class="screen-swiper square-dot" indicator-dots circular autoplay interval="4000" duration="500" v-if="adverData && adverData[item.id]">
								<swiper-item v-for="(adverts, adindex) in adverData[item.id]" :key="adindex" @tap="onAdvert(adverts)">
									<image lazy-load class="radius-bock" :src="$wanlshop.oss(adverts.media, 350, 0, 1)" mode="aspectFill"></image>
								</swiper-item>
							</swiper>
							<view class="padding-top-sm">
								<view class="list-item radius-bock">
									<view class="list-container">
										<view class="list-box" v-for="(category, infokey) in item.childlist"
											:key="category.id" @tap="handleProduct(category.id, category.name)">
											<image lazy-load :src="$wanlshop.oss(category.image, 60, 60)" class="list-image" />
											<view class="text-sm">{{ category.name }}</view>
										</view>
									</view>
								</view>
							</view>
						</view>
					</scroll-view>
				</block>
			</view>
		</block>
		<block v-else-if="categoryStyle === 4">
			<view class="wanlshop-classify-container__list">
				<scroll-view scroll-y scroll-with-animation class="left" :scroll-top="scrollTop"
					:style="{ height: `${mainHeight}px`}">
					<view v-for="(item,index) in categoryData" :key="item.id" class="item"
						:class="[currentTab == index ? 'active' : '']" :data-current="index" @tap.stop="handleNav">
						<text>{{ item.name }}</text>
					</view>
					<view class="edgeInsetBottom"></view>
				</scroll-view>
				<block v-for="(item, index) in categoryData" :key="item.id">
					<scroll-view scroll-y class="right" :style="{ height: `${mainHeight}px` }" v-if="currentTab == index">
						<view class="list-cat">
							<swiper class="screen-swiper square-dot" indicator-dots circular autoplay interval="4000"
								duration="500" v-if="adverData && adverData[item.id]">
								<swiper-item v-for="(adverts, adindex) in adverData[item.id]" :key="adindex"
									@tap="onAdvert(adverts)">
									<image lazy-load class="radius-bock" :src="$wanlshop.oss(adverts.media, 350, 0, 1)"
										mode="aspectFill"></image>
								</swiper-item>
							</swiper>
							<view class="padding-top-sm">
								<view class="list-item radius-bock" v-for="(childlist, childkey) in item.childlist"
									:key="childlist.id">
									<view class="flex justify-between text-sm"
										@tap="handleProduct(childlist.id, childlist.name)">
										<text>{{childlist.name}}</text>
										<text class="wlIcon-fanhui2 wanl-gray-light"></text>
									</view>
									<view class="list-container">
										<view class="list-box" v-for="(category, infokey) in childlist.childlist"
											:key="category.id" @tap="handleProduct(category.id, category.name)">
											<image lazy-load :src="$wanlshop.oss(category.image, 60, 60)" class="list-image" />
											<view class="text-sm">{{ category.name }}</view>
										</view>
									</view>
								</view>
								<view class="edgeInsetBottom"></view>
							</view>
						</view>
					</scroll-view>
				</block>
			</view>
		</block>
	</view>
</template>

<script>
	export default {
		name: 'wanlShopClassify',
		props: {
			shopId: {
				type: Number,
				default: 0
			},
			categoryStyle: {
				type: Number,
				default: 1
			},
			mainHeight: {
				type: Number,
				default: 0
			},
			adverData: {
				type: Object,
				default () {}
			},
			categoryData: {
				type: Array,
				default: () => {
					return []
				}
			}
		},
		data() {
			return {
				currentTab: 0, //预设当前项的值
				scrollTop: 0, //tab标题的滚动条位置
			}
		},
		methods: {
			// 点击标题切换当前页时改变样式
			handleNav(e) {
				let cur = e.currentTarget.dataset.current;
				if (this.currentTab == cur) {
					return false;
				} else {
					this.currentTab = cur;
					this.checkCor();
				}
			},
			handleProduct(category_id, category_name) {
				if(this.shopId === 0){
					this.$wanlshop.to(
						`/pages/product/list?data=${JSON.stringify({ category_id: category_id, category_name: category_name })}`
					);
				}else{
					this.$wanlshop.to(
						`/pages/shop/product/list?shop_id=${this.shopId}&category_id=${category_id}&category_name=${category_name}`
					);
				}
			},
			//判断当前滚动超过一屏时，设置tab标题滚动条。
			checkCor() {
				if (this.currentTab > 7) {
					this.scrollTop = 500;
				} else {
					this.scrollTop = 0;
				}
			}
		}
	}
</script>

<style lang="scss">
	.wanlshop-classify-container {
		&__one {
			padding-left: 25rpx;
			padding-right: 25rpx;
			padding-bottom: 100rpx;
			padding-bottom: calc(env(safe-area-inset-bottom) + 100rpx);
			image {
				width: 100%;
				height: 300rpx;
				border-radius: 15rpx;
				overflow: hidden;
			}
			.item {
				position: relative;
				display: flex;
				justify-content: center;
				margin-top: 25rpx;
				.category-title {
					font-size: 26rpx;
					position: absolute;
					bottom: 20rpx;
					border-radius: 10rpx;
					overflow: hidden;
					color: #666666;
					background-color: rgba(255, 255, 255, .6);
					width: 60%;
					text-align: center;
					padding: 4rpx 0;
				}
			}
		}
		&__grid {
			padding-bottom: 100rpx;
			padding-bottom: calc(env(safe-area-inset-bottom) + 100rpx);
			.cu-list{
				background-color: rgba(255,255,255,0);
				&>.cu-item {
					padding: 25rpx 0;
					align-items: center;
					image {
						width: 160rpx;
						height: 160rpx;
						margin-bottom: 10rpx;
					}
				}
			}
		}
		&__list {
			display: flex;

			.left {
				width: 200rpx;
				z-index: 10;
				background: #fbfbfb;

				.item {
					width: 200rpx;
					height: 110rpx;
					box-sizing: border-box;
					display: flex;
					align-items: center;
					justify-content: center;
					font-size: 26rpx;
					color: #444;
					font-weight: 400;

					&.active {
						position: relative;
						color: #000;
						font-size: 30rpx;
						font-weight: 600;
						background: white;

						&::before {
							content: '';
							position: absolute;
							border-left: 6rpx solid #fe6600;
							height: 32rpx;
							left: 0;
						}
					}
				}
			}

			.right {
				width: 100%;
				padding-left: 25rpx;
				box-sizing: border-box;

				/* background-image: linear-gradient(#fff, #f2f2f2, #f4f4f4, #f6f6f6); */
				.list-cat {
					width: 100%;
					overflow: hidden;
					padding-top: 10rpx;
					padding-right: 20rpx;
					box-sizing: border-box;
				}

				.screen-swiper {
					min-height: 180rpx;
					height: 180rpx;
					overflow: hidden;
				}

				.list-item {
					background: #ffffff;
					width: 100%;
					box-sizing: border-box;
					padding: 20rpx;
					margin-bottom: 20rpx;

					.title {
						color: #FF4F00;
					}
				}

				.list-container {
					/* padding-top: 20rpx; */
					display: flex;
					display: -webkit-flex;
					justify-content: flex-start;
					flex-direction: row;
					flex-wrap: wrap;
				}

				.list-box {
					width: 33.3333%;
					text-align: center;
					padding-top: 40rpx;
				}

				.list-image {
					width: 120rpx;
					height: 120rpx;
				}
			}
		}
	}
</style>
