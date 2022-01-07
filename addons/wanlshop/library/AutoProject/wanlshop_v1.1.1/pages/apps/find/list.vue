<template>
	<view class="wanlshop-find-container">
		<view class="wanlshop-find-container___head" :style="{height: `${headHeight}px`}">
			<view class="fixed bg-bgcolor" :style="{height: `${headHeight}px`, paddingTop: `${headTop}px`}">
				<view class="head">
					<view class="margin-right-bj text-xl" @tap="handleBack">
						<text class="wlIcon-fanhui1"></text>
					</view>
					<view class="search round padding-lr-bj solid" @tap="$wanlshop.to('/pages/page/search?type=find')">
						<text class="wlIcon-sousuo1 margin-right-sm"></text>
						<text class="wanl-gray-dark">{{keyword ? keyword : '搜索 种草作品 或 创作者'}}</text>
					</view>
				</view>
				<scroll-view scroll-x class="nav text-center ">
					<view class="cu-item" :class="index === currentId ? 'text-30 text-bold cur' : 'wanl-gray'"
						v-for="(item,index) in navList" :key="index" @tap="handleSelect(index)">
						{{item.type_text}}
					</view>
				</scroll-view>
			</view>
		</view>
		<view class="wanlshop-find-container__main">
			<block v-if="currentId === 0">
				<wanl-empty v-if="navList[currentId].dataList.length === 0 && navList[currentId].loaded"
					text="没有找到任何动态" />
				<scroll-view v-if="navList[currentId].dataList.length > 0" :style="{height: `${mainHeight}px`}" scroll-y
					@scrolltolower="loadData">
					<view class="find">
						<view class="item" v-for="(item, index) in navList[currentId].dataList" :key="index">
							<view class="cover radius-bock"
								:style="{ backgroundImage: 'url(' + $wanlshop.oss(item.images[0], 200, 0) + ')' }"
								@tap="onFind(item)">
							</view>
							<view class="padding-xs  text-bold5">
								<view class="text-cut-2">
									<view class="cu-tag sm radius margin-right-xs" :class="[typeList[item.type]]">
										{{item.type_text}}</view>
									{{formatHtml(item.content)}}
								</view>
							</view>
							<view class="flex wanl-gray text-min padding-lr-xs justify-between">
								<view>
									{{$wanlshop.timeFormat(item.createtime, 'mm-dd')}} 发布
								</view>
								<view :class="{'text-red': item.isLike === 1}" @tap="handleLike(index)">
									<text :class="[item.isLike === 1 ? 'wlIcon-yiguanzhu1' : 'wlIcon-likes']"></text>
									<text class="margin-left-xs">{{item.likes}}</text>
								</view>
							</view>
						</view>
					</view>
					<uni-load-more :status="navList[currentId].loadingType" :content-text="contentText" />
				</scroll-view>
			</block>
			<block v-if="currentId === 1">
				<wanl-empty v-if="navList[currentId].dataList.length === 0 && navList[currentId].loaded"
					text="没有找到任何用户" />
				<scroll-view v-if="navList[currentId].dataList.length > 0" :style="{height: `${mainHeight}px`}" scroll-y
					@scrolltolower="loadData">
					<view class="user">
						<view class="item padding-bj margin-bj bg-white radius-bock" v-for="(item, index) in navList[currentId].dataList" :key="index" @click="handleUser(item.user_no)">
							<block v-if="item.shop">
								<view class="cu-avatar margin-right-bj round"
									:style="{backgroundImage: `url(${$wanlshop.oss(item.shop.avatar, 88, 88)})`}"
								>
								</view>
								<view class="subject">
									<view class="info">
										<view class="text-cut-2 margin-bottom-xs">
											<view class="cu-tag sm bg-gradual-blue radius margin-right-xs">
												商家
											</view>
											{{item.shop.shopname}}
										</view>
										<view class="flex wanl-gray-light text-sm">
											<view class="margin-right"> 粉丝 <text class="margin-left-xs">{{item.fans}}</text> </view>
											<view> 获赞 <text class="margin-left-xs">{{item.praised}}</text> </view>
										</view>
									</view>
									<view class="menu">
										<button class="cu-btn wlIcon margin-right-sm"
											@click.stop="onShop(item.shop.id)">
											<text class="wlIcon-31dianpu"></text>
										</button>
										<button v-if="item.isFollow === 0" class="cu-btn wlIcon"
											@click.stop="handleFollow(index, item.shop)">
											<text class="wlIcon-tianjia"></text>
										</button>
										<button v-else class="cu-btn radius-bock text-sm"
											@click.stop="handleFollow(index, item.shop)">
											<text class="wlIcon-31guanbi margin-right-xs text-bold"></text>取消
										</button>
									</view>
								</view>
							</block>
							<block v-else>
								<view class="cu-avatar margin-right-bj round"
									:style="{backgroundImage: `url(${$wanlshop.oss(item.user.avatar, 88, 88)})`}">
								</view>
								<view class="subject">
									<view class="info">
										<view class="text-cut-2 margin-bottom-xs">{{item.user.nickname}}</view>
										<view class="flex wanl-gray-light text-sm">
											<view class="margin-right"> 粉丝 <text class="margin-left-xs">{{item.fans}}</text> </view>
											<view> 获赞 <text class="margin-left-xs">{{item.praised}}</text> </view>
										</view>
									</view>
									<view class="menu">
										<button v-if="item.isFollow === 0" class="cu-btn wlIcon"
											@click.stop="handleFollow(index)">
											<text class="wlIcon-tianjia"></text>
										</button>
										<button v-else class="cu-btn radius-bock text-sm"
											@click.stop="handleFollow(index)">
											<text class="wlIcon-31guanbi margin-right-xs text-bold"></text>取消
										</button>
									</view>
								</view>
							</block>
						</view>
					</view>
					<uni-load-more :status="navList[currentId].loadingType" :content-text="contentText" />
				</scroll-view>
			</block>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				headTop: 0,
				headHeight: 0,
				mainHeight: 0,
				keyword: '',
				currentId: 0,
				typeList: {
					new: 'bg-gradual-yellow',
					live: 'wanl-bg-orange',
					want: 'bg-gradual-green',
					activity: 'bg-gradual-orange',
					show: 'wanl-bg-pink',
					video: 'wanl-bg-pink'
				},
				navList: [{
						type: 'works',
						type_text: '作品',
						dataList: [],
						loadingType: 'more',
						current_page: 1
					},
					{
						type: 'user',
						type_text: '用户',
						dataList: [],
						loadingType: 'more',
						current_page: 1
					}
				],
				contentText: {
					contentdown: ' ',
					contentrefresh: '正在疯狂加载中...',
					contentnomore: '我是有底线的'
				}
			}
		},
		onLoad(option) {
			let sys = uni.getSystemInfoSync(),
				headHeight = sys.safeAreaInsets.top + uni.upx2px(160);
			this.headTop = sys.safeAreaInsets.top;
			this.headHeight = headHeight;
			this.mainHeight = sys.windowHeight - headHeight;
			this.keyword = option.search;
			this.loadData();
		},
		methods: {
			async loadData(source) {
				//这里是将订单挂载到tab列表下
				let navItem = this.navList[this.currentId];
				//判断是否最后一页
				if (navItem.loadingType == 'noMore') {
					return;
				}
				//tab切换只有第一次需要加载数据
				if (source === 'tabChange' && navItem.loaded === true) {
					return;
				}
				//防止重复加载
				if (navItem.loadingType === 'loading') {
					return;
				}
				navItem.loadingType = 'loading';
				// 获取列表
				this.$api.get({
					url: '/wanlshop/find/find/getList',
					data: {
						search: this.keyword,
						type: navItem.type,
						page: navItem.current_page
					},
					success: res => {
						navItem.current_page = res.current_page; //当前页码
						if (res.last_page === res.current_page) {
							navItem.loadingType = 'noMore';
						} else {
							navItem.loadingType = 'more';
							navItem.current_page++;
						}
						res.data.forEach(item => {
							navItem.dataList.push(item);
						});
						this.$set(navItem, 'loaded', true);
					}
				});
			},
			async handleLike(index) {
				if (this.$store.state.user.isLogin) {
					let data = this.navList[this.currentId].dataList[index];
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
					this.$wanlshop.to('/pages/user/auth/auth')
				}
			},
			async handleFollow(index, shop) {
				if (this.$store.state.user.isLogin) {
					let data = this.navList[this.currentId].dataList[index];
					this.$api.post({
						url: '/wanlshop/find/user/setFindUser',
						data: {
							id: data.user_no,
							type: 'follow'
						},
						success: res => {
							if(shop){
								let concern = this.$store.state.statistics.dynamic.concern;
								if(res.data === 0){
									concern -= 1;
								}else{
									concern += 1;
								}
								this.$store.commit('statistics/dynamic', {
									concern: concern,
								});
							}
							data.isFollow = res.data;
						}
					});
				} else {
					this.$wanlshop.to('/pages/user/auth/auth')
				}
			},
			// 格式化html
			formatHtml(content) {
				return (content.replace(/<\/?.+?>/g, "").replace(/ /g, ""));
			},
			// 选择Tag
			handleSelect(index) {
				this.currentId = index;
				this.loadData('tabChange');
			},
			handleUser(user_no) {
				this.$wanlshop.to(`/pages/apps/find/user?id=${user_no}`);
			},
			handleBack() {
				this.$wanlshop.back(1);
			}
		}
	}
</script>

<style lang="scss">
	.wanlshop-find-container {
		&___head {
			.fixed {
				width: 100%;
				position: fixed;
				top: 0;
				z-index: 999;

				.head {
					display: flex;
					align-items: center;
					justify-content: space-between;
					height: 90rpx;
					padding: 0 25rpx;

					.search {
						flex: 1;
						display: flex;
						align-items: center;
						height: 68rpx;
						background-color: #ffffff;
					}
				}

				.nav {
					.cu-item {
						height: 70rpx;
						line-height: 70rpx;
					}
				}
			}
		}

		&__main {
			.find {
				display: flex;
				flex-direction: row;
				flex-wrap: wrap;
				padding: 0 16rpx;
				justify-content: space-between;
				overflow: hidden;
				.item {
					margin-top: 16rpx;
					margin-bottom: 30rpx;
					flex: 0 0 calc((100% - 16rpx) / 2);
					overflow: hidden;

					.cover {
						height: 460rpx;
						background-size: cover;
						background-position: center;
						background-repeat: no-repeat;
					}
				}
			}

			.user {
				overflow: hidden;
				.item {
					display: flex;
					align-items: center;

					.cu-avatar {
						width: 100rpx;
						height: 100rpx;
						flex-shrink: 0;
						background-color: #f9f9f9;
					}

					.subject {
						display: flex;
						align-items: center;
						justify-content: space-between;
						flex: 1;

						.info {
							max-width: 250rpx;
						}

						.menu {
							display: flex;
							align-items: center;
						}
					}
				}
			}
		}
	}
</style>