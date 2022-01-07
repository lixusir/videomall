<template>
	<scroll-view
		class="wanlshop-find-container"
		:style="{
			paddingTop: `${headHeight}px`,
			height: `${windowHeight}px`,
			backgroundColor: ``
		}"
		scroll-y
		enable-back-to-top
		enable-flex="true"
		@scrolltoupper="handleUpper"
		@scrolltolower="handleLower"
	>
		<block v-if="!user.isLogin && !userNo">
			<view
				class="bg-white margin-bj radius-bock flex align-center justify-center padding-lr padding-tb-xl"
			>
				<view class="text-center">
					<text class="wanl-gray">登录后才能看到您关注店铺的动态哦</text>
					<view class="margin-top-sm">
						<button class="cu-btn bg-orange" @tap="$wanlshop.auth('/pages/find')">
							马上登陆
						</button>
					</view>
				</view>
			</view>
		</block>
		<block v-else>
			<view
				class="wanlshop-find-container__list  wanl-find margin-top-sm"
				v-for="(find, index) in dataList"
				:key="find.id"
			>
				<view class="margin-lr-bj bg-white radius-bock padding-lr-bj padding-top-bj">
					<!-- 头部 -->
					<view class="userinfo">
						<block v-if="find.shop">
							<view class="avatar" @tap="handleUser(find.user_no)">
								<view
									class="cu-avatar round margin-right-bj"
									:style="{
										backgroundImage:
											'url(' +
											$wanlshop.oss(find.shop.avatar, 35, 35, 2, 'avatar') +
											')'
									}"
								></view>
								<view class="content">
									<view class="wanl-find-head text-cut">
										<text class="wanl-black margin-right-bj shopname">
											{{ find.shop.shopname }}
										</text>
										<!-- <view
											v-if="find.isLive"
											@tap="onLive(find.isLive.id)"
											class="cu-tag sm wanl-bg-orange round"
										>
											<text class="wlIcon-zhibo"></text>
											直播中
										</view> -->
									</view>
									<view class="wanl-gray text-min">
										{{ $wanlshop.timeToDate(find.createtime) }}
									</view>
								</view>
							</view>
							<view class="menu">
								<button
									class="cu-btn radius-bock text-sm"
									@click.stop="handleFollow(index, find.shop)"
								>
									<view v-if="find.isFollow === 0">
										<text
											class="wlIcon-31xuanze margin-right-xs text-bold"
										></text>
										关注
									</view>
									<view v-else>
										<text
											class="wlIcon-31guanbi margin-right-xs text-bold"
										></text>
										取关
									</view>
								</button>
								<button
									class="cu-btn wlIcon margin-left-sm"
									@click.stop="onShop(find.shop.id)"
								>
									<text class="wlIcon-31dianpu"></text>
								</button>
							</view>
						</block>
						<block v-else>
							<view class="avatar" @tap="handleUser(find.user_no)">
								<view
									class="cu-avatar round margin-right-bj"
									:style="{
										backgroundImage:
											'url(' +
											$wanlshop.oss(find.user.avatar, 35, 35, 2, 'avatar') +
											')'
									}"
								></view>
								<view class="content">
									<view class="wanl-find-head text-cut">
										<text class="wanl-black margin-right-bj shopname">
											{{ find.user.nickname }}
										</text>
										<!-- <view
											v-if="find.isLive"
											@tap="onLive(find.isLive.id)"
											class="cu-tag sm wanl-bg-orange round"
										>
											<text class="wlIcon-zhibo"></text>
											直播中
										</view> -->
									</view>
									<view class="wanl-gray text-min">
										{{ $wanlshop.timeToDate(find.createtime) }}
									</view>
								</view>
							</view>
							<view class="menu">
								<button
									class="cu-btn radius-bock text-sm"
									@click.stop="handleFollow(index)"
								>
									<view v-if="find.isFollow === 0">
										<text
											class="wlIcon-31xuanze margin-right-xs text-bold"
										></text>
										关注
									</view>
									<view v-else>
										<text
											class="wlIcon-31guanbi margin-right-xs text-bold"
										></text>
										取关
									</view>
								</button>
							</view>
						</block>
					</view>
					<!-- 内容 -->
					<view
						class="content margin-tb-bj wanl-gray-dark text-cut-2"
						@tap="onFind(find)"
					>
						<view
							class="cu-tag sm radius margin-right-xs"
							:class="[typeList[find.type]]"
						>
							{{ find.type_text }}
						</view>
						{{ formatHtml(find.content) }}
					</view>
					<!-- 图片 -->
					<view class="container radius-bock">
						<block v-for="(img, kes) in find.images" :key="kes">
							<block v-if="find.type === 'live'">
								<view
									class="item"
									@tap="onFind(find)"
									:class="[kes == 0 ? 'item-live' : '']"
									:style="{
										backgroundImage: 'url(' + $wanlshop.oss(img, 200, 0) + ')'
									}"
								>
									<block v-if="kes == 0">
										<view class="play">
											<text
												:class="[
													find.live.state == 1
														? 'wlIcon-icon_zhibo-mian'
														: 'wlIcon-guijihuifang'
												]"
											></text>
										</view>
										<view class="state text-white" v-if="find.live.state == 1">
											<view class="tags wanl-bg-orange">
												<text class="wlIcon-zhibo"></text>
												<text class="text-min">直播</text>
											</view>
											<view class="text-sm">{{ find.live.views }} 在看</view>
										</view>
										<view class="state text-white" v-else>
											<view class="tags bg-grey">
												<text class="text-min">回放</text>
											</view>
											<view class="text-sm">{{ find.live.views }} 看过</view>
										</view>
										<!-- <view class="number text-white">
														<view>{{$wanlshop.toFormat(find.live.goods_ids.split(',').length,'hundred')}}</view>
														<text>宝贝</text> 
													</view> -->
										<view
											class="like text-center text-white"
											v-if="find.live.state == 1"
										>
											<view class="likebut">
												<text class="wlIcon-yiguanzhu1"></text>
											</view>
											<text class="text-min">{{ find.live.like }}</text>
										</view>
									</block>
								</view>
							</block>
							<block v-else-if="find.type === 'video'">
								<view class="item item-1 text-sl radius-bock" :style="{ backgroundImage: 'url(' + $wanlshop.oss(img, 200, 0) + ')' }" @tap="onFind(find)">
									<text class="wlIcon-video text-white"></text>
								</view>
							</block>
							<block v-else>
								<block v-if="find.images.length == 1">
									<view
										class="item item-1 radius-bock"
										@tap="previewImage(find.images, kes)"
										:style="{
											backgroundImage:
												'url(' + $wanlshop.oss(img, 200, 0) + ')'
										}"
									></view>
								</block>
								<block v-else-if="find.images.length == 3">
									<view
										class="item"
										@tap="previewImage(find.images, kes)"
										:class="[kes == 0 ? 'item-3' : '']"
										:style="{
											backgroundImage:
												'url(' + $wanlshop.oss(img, 200, 0) + ')'
										}"
									></view>
								</block>
								<block v-else>
									<view
										class="item"
										@tap="previewImage(find.images, kes)"
										:style="{
											backgroundImage:
												'url(' + $wanlshop.oss(img, 200, 200) + ')'
										}"
									></view>
								</block>
							</block>
						</block>
					</view>
					<!-- 商品 -->
					<scroll-view class="scroll-view margin-top-sm" scroll-x="true">
						<view
							class="scroll-view-item radius-bock"
							:class="[find.goods.length == 1 ? 'col-1' : '']"
							v-for="(goods, sub) in find.goods"
							:key="goods.id"
							@tap="onGoods(goods.id)"
						>
							<view class="img">
								<image lazy-load :src="$wanlshop.oss(goods.image, 50, 50)"></image>
							</view>
							<view class="content padding-left-bj text-cut text-df">
								{{ goods.title }}
								<view class="flex align-center">
									<text class="text-price text-orange">{{ goods.price }}</text>
								</view>
							</view>
							<view class="icon text-lg wanl-gray-light">
								<text class="wlIcon-fanhui6"></text>
							</view>
						</view>
					</scroll-view>
					<view class="flex justify-between padding-tb-bj align-center">
						<view
							v-if="find.type == 'live'"
							class="wanl-gray-light text-sm"
							@tap="onFind(find)"
						>
							{{ find.live.views }} 阅读
						</view>
						<view v-else class="wanl-gray-light text-sm" @tap="onFind(find)">
							{{ find.views }} 阅读
						</view>
						<view class="flex">
							<view
								:class="[find.isLike === 1 ? 'wanl-orange' : '']"
								@tap="handleLike(index)"
							>
								<text
									:class="[
										find.isLike === 1 ? 'wlIcon-dianzan1' : 'wlIcon-dianzan11'
									]"
								></text>
								<text class="margin-left-xs">{{ find.likes }}</text>
							</view>
							<view
								class="margin-left"
								@tap="onFind(find, 'comments')"
								v-if="appConfig.comment_switch == 'Y'"
							>
								<text class="wlIcon-pinglun"></text>
								<text class="margin-left-xs">{{ find.comments }}</text>
							</view>
						</view>
					</view>
					<view
						class="padding-tb-bj solid-top text-center text-orange"
						v-if="find.newGoods > 0"
						@tap="onShop(find.shop_id)"
					>
						进店查看 {{ find.newGoods }} 件新品
						<text class="wlIcon-fanhui6 margin-left-xs"></text>
					</view>
				</view>
			</view>
			<wanl-empty
				v-if="status !== 'loading' && dataList.length === 0"
				src="collect_default3x"
				text="没有找到任何发现动态"
			/>
			<uni-load-more
				:status="status"
				:content-text="contentText"
				v-if="dataList.length > 0"
			/>
		</block>
	</scroll-view>
</template>

<script>
export default {
	name: 'wanlShopFind',
	props: {
		// 页面配置
		windowHeight: {
			type: Number,
			default: 0
		},
		headHeight: {
			type: Number,
			default: 0
		},
		currentItemId: {
			type: String,
			default: ''
		},
		userNo: {
			type: String,
			default: ''
		},
		appConfig: {
			type: Object,
			default() {
				return {};
			}
		},
		user: {
			type: Object,
			default() {
				return {};
			}
		}
	},
	watch: {
		currentItemId(newVal, oldVal) {
			if (newVal === 'follow') {
				this.loadData();
			}
		}
	},
	data() {
		return {
			typeList: {
				new: 'bg-gradual-yellow',
				live: 'wanl-bg-orange',
				want: 'bg-gradual-green',
				activity: 'bg-gradual-orange',
				show: 'wanl-bg-pink',
				video: 'wanl-bg-orange'
			},
			dataList: [], //数据
			current_page: 1, //当前页码
			last_page: 1, //总页码
			status: 'loading',
			contentText: {
				contentdown: '下拉加载更多',
				contentrefresh: '疯狂加载中...',
				contentnomore: '我是有底线的'
			}
		};
	},
	methods: {
		async loadData(type) {
			this.status = 'loading';
			let data = {
				page: this.current_page
			};
			// 判断上拉还是下拉
			if (type === 'upper') {
				this.current_page = 1;
			} else if (type === 'lower') {
				if (this.current_page >= this.last_page) {
					this.status = 'noMore';
					return false;
				} else {
					this.current_page += 1;
				}
			}
			if (this.currentItemId === 'follow') {
				data.type = this.currentItemId;
			}
			// 请求数据
			await this.$api.get({
				url: '/wanlshop/find/find/getList',
				data: data,
				success: res => {
					if (type === 'lower') {
						this.dataList = this.dataList.concat(res.data);
					} else {
						this.dataList = res.data;
					}
					this.current_page = res.current_page; //当前页码
					this.last_page = res.last_page; //总页码
					// 判断是否还有数据
					if (res.current_page === res.last_page || res.total === 0) {
						this.status = 'noMore';
					} else {
						this.status = 'more';
					}
				}
			});
		},
		async handleLike(index) {
			if (this.$store.state.user.isLogin) {
				let data = this.dataList[index];
				this.$api.post({
					url: '/wanlshop/find/user/setFindUser',
					data: {
						id: data.id,
						type: 'likes'
					},
					success: res => {
						if (res.data === 0) {
							data.likes -= 1;
						} else {
							data.likes += 1;
						}
						data.isLike = res.data;
					}
				});
			} else {
				this.$wanlshop.to('/pages/user/auth/auth');
			}
		},
		async handleFollow(index, shop) {
			if (this.$store.state.user.isLogin) {
				let data = this.dataList[index];
				this.$api.post({
					url: '/wanlshop/find/user/setFindUser',
					data: {
						id: data.user_no,
						type: 'follow'
					},
					success: res => {
						if (shop) {
							let concern = this.$store.state.statistics.dynamic.concern;
							if (res.data === 0) {
								concern -= 1;
							} else {
								concern += 1;
							}
							this.$store.commit('statistics/dynamic', {
								concern: concern
							});
						}
						data.isFollow = res.data;
					}
				});
			} else {
				this.$wanlshop.to('/pages/user/auth/auth');
			}
		},
		handleUpper() {
			// this.loadData('upper');
		},
		handleLower() {
			this.loadData('lower');
		},
		handleUser(user_no) {
			this.$wanlshop.to(`/pages/apps/find/user?id=${user_no}`);
		},
		//预览图片
		previewImage(image, index) {
			var imgArr = [];
			for (var item of image) {
				imgArr = imgArr.concat(this.$wanlshop.oss(item, 500));
			}
			uni.previewImage({
				urls: imgArr,
				current: imgArr[index],
				indicator: 'number'
			});
		},
		// 格式化html
		formatHtml(content) {
			return content.replace(/<\/?.+?>/g, '').replace(/ /g, '');
		}
	}
};
</script>

<style lang="scss">
.wanlshop-find-container {
	&__list {
		.userinfo {
			display: flex;
			justify-content: space-between;
			align-items: center;

			text {
				&[class*='wlIcon-'] {
					font-size: 28rpx;
				}
			}

			.avatar {
				width: calc(100% - 245rpx);
				display: flex;
				align-items: center;
				.cu-avatar {
					background-color: #f5f5f5;
					flex-shrink: 1;
				}
				.content{
					width: calc(100% - 90rpx);
				}
				.cu-tag {
					font-size: 20rpx;
					padding: 0px 10rpx !important;
				}
			}
			// 待优化溢出,临时解决方案
			.menu{
				text-align: right;
				width: 245rpx;
			}

			.cu-btn {
				&.sm {
					padding: 0 20rpx;
					font-size: 26rpx;
					height: 50rpx;
				}
			}
		}

		.content {
			.cu-tag {
				&.sm {
					padding: 6rpx;
				}
			}
		}

		.container {
			display: grid;
			grid-template-columns: 1fr 1fr 1fr;

			&.col-2 {
				grid-template-columns: 1fr 1fr;
			}

			.item {
				display: flex;
				align-items: center;
				justify-content: center;
				border: 1px solid #fff;
				min-height: 6rem;
				width: 100%;
				background-size: cover;
				background-position: center;
				background-repeat: no-repeat;

				&.item-1 {
					grid-column: 1 / 3;
					grid-row: 1 / 3;
					height: 600rpx;
				}

				&.item-3 {
					grid-column: 1 / 3;
					grid-row: 1 / 3;
				}

				&.item-live {
					position: relative;
					grid-column: 1 / 3;
					grid-row: 1 / 3;

					.play {
						width: 120rpx;
						height: 120rpx;
						display: flex;
						align-items: center;
						justify-content: center;
						border-radius: 50%;
						background-color: rgba(0, 0, 0, 0.2);
						border: 1px solid rgba(255, 255, 255, 0.5);
						font-size: 56rpx;
						color: #fff;
					}

					.state {
						display: flex;
						position: absolute;
						left: 20rpx;
						bottom: 20rpx;
						background-color: rgba(0, 0, 0, 0.1);
						height: 36rpx;
						line-height: 36rpx;
						border-radius: 8rpx;
						overflow: hidden;

						& > view {
							padding: 0 12rpx;
						}

						.tags {
							height: 100%;
							border-radius: 8rpx;
							font-size: 28rpx;

							& > text {
								margin-right: 4rpx;
							}
						}
					}

					.number {
						position: absolute;
						background-color: rgba(0, 0, 0, 0.2);
						border: 1px solid rgba(255, 255, 255, 0.5);
						border-radius: 8rpx 8rpx 0 20rpx;
						overflow: hidden;
						padding: 8rpx 6rpx;
						top: 10rpx;
						right: 10rpx;
						line-height: 1;
						text-align: center;

						& > view {
							font-size: 30rpx;
						}

						& > text {
							font-size: 11px;
						}
					}

					.like {
						position: absolute;
						bottom: 15rpx;
						right: 30rpx;
						line-height: 1.2;

						.likebut {
							background-color: #f40;
							border-radius: 50%;
							width: 45rpx;
							line-height: 46rpx;
							height: 45rpx;
							font-size: 26rpx;
						}
					}
				}
			}
		}

		.scroll-view {
			white-space: nowrap;
			width: 100%;

			.scroll-view-item {
				display: inline-flex;
				width: 60%;
				height: 100rpx;
				overflow: hidden;
				margin-right: 10rpx;
				background-color: #f9f9f9;
				align-items: center;

				&.col-1 {
					width: 100%;
					margin-right: 0;
				}

				.img {
					height: 100rpx;
					width: 100rpx;
					border-radius: 18rpx 0 0 18rpx;
					background-color: #efefef;

					image {
						height: 100rpx;
						width: 100rpx;
						border-radius: 18rpx 0 0 18rpx;
						background-color: #efefef;
					}
				}

				.content {
					flex: 1;
					line-height: 1.3;
				}

				.icon {
					width: 60rpx;
					text-align: center;
				}
			}
		}
	}
}
</style>
