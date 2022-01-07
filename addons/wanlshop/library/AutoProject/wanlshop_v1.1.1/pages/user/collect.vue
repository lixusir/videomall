<template>
	<view class="collect-container">
		<view class="collect-container__head bg-white" :style="{ height: headHeight + 'px' }">
			<view :style="{ height: headHeight + 'px', paddingTop: headTop + 'px' }">
				<view class="navigater flex align-center justify-between">
					<view class="back" :style="{height: headHeight + 'px', lineHeight: headHeight + 'px'}"
						@tap="handleBack">
						<text class="wlIcon-fanhui1"></text>
					</view>
					<scroll-view scroll-x class="nav text-center">
						<view v-for="(item, index) in navList" :key="index" class="cu-item flex-sub"
							:class="currentItemId === item.type ? 'text-orange cur' : ''"
							@tap="handleSelect(item.type, index)">
							<text class="text-30">{{item.type_text}}</text>
						</view>
					</scroll-view>
				</view>
			</view>
		</view>
		<!-- 主体 -->
		<swiper class="collect-container__main" :current-item-id="currentItemId"
			:style="{ height: windowHeight + 'px' }" @change="changeCurrent" @animationfinish="animationFinish">
			<swiper-item v-for="(data, keys) in navList" :key="keys" :item-id="data.type">
				<wanl-empty text="你还没有任何收藏" src="collect_default3x" v-if="data.dataList.length === 0 && data.loaded" />
				<scroll-view class="content" scroll-y @scrolltolower="loadData">
					<view class="item margin-bj padding-sm bg-white radius-bock" v-for="(item, index) in data.dataList"
						:key="index" @click="handleGoods(item.id)">
						<view class="cu-avatar margin-right-bj radius"
							:style="{backgroundImage: `url(${$wanlshop.oss(item.image, 88, 88)})`}"></view>
						<view class="subject">
							<view class="text-sm wanl-gray-dark text-cut-2">
								<view v-if="currentItemId === 'groups'" class="cu-tag sm bg-gradual-red radius margin-right-xs">
									{{item.is_ladder === 1 ?'阶梯': item.people_num + '人'}}拼团
								</view>
								{{item.title}}
							</view>
							<view class="text-price text-red text-df"> {{item.price}} </view>
							<view class="menu">
								<view class="wanl-gray-light text-sm">
									<text class="margin-right-sm">喜欢 {{item.like}}</text>
									<text v-if="currentItemId === 'goods'">付款 {{item.payment}}</text>
									<text v-if="currentItemId === 'groups'">已拼团 0 件</text>
								</view>
								<view class="button text-bold text-30">
									<text class="wlIcon-lajitong" @click.stop="loadFollow(item.id, index)"></text>
									<text class="wlIcon-dianpu1" @click.stop="onShop(item.shop_id)"></text>
								</view>
							</view>
						</view>
					</view>
					<uni-load-more :status="data.loadingType" :content-text="contentText" />
				</scroll-view>
			</swiper-item>
		</swiper>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				headHeight: 0,
				windowHeight: 0,
				headTop: 0,
				currentId: 0,
				currentItemId: 'goods',
				navList: [{
						type: 'goods',
						type_text: '商品收藏',
						dataList: [],
						loadingType: 'more',
						current_page: 1
					},
					{
						type: 'groups',
						type_text: '团购收藏',
						dataList: [],
						loadingType: 'more',
						current_page: 1
					}
				],
				contentText: {
					contentdown: ' ',
					contentrefresh: '收藏正在加载...',
					contentnomore: ''
				}
			};
		},
		onLoad() {
			// 计算页面尺寸
			let sys = this.$wanlshop.wanlsys();
			this.headTop = sys.top;
			this.headHeight = sys.height;
			this.windowHeight = sys.windowHeight - uni.upx2px(90);
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
					url: '/wanlshop/product/collect',
					data: {
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
							navItem.dataList.push(item.goods);
						});
						//loaded新字段用于表示数据加载完毕，如果为空可以显示空白页
						this.$set(navItem, 'loaded', true);
					}
				});
			},
			async loadFollow(id, index) {
				if(this.currentItemId === 'goods'){
					var apiUrl = '/wanlshop/product/follow';
				}else if(this.currentItemId === 'groups'){
					var apiUrl = '/wanlshop/groups/product/follow';
				}
				this.$api.post({
					url: apiUrl,
					loadingTip: '正在删除资源...',
					data: {
						id: id
					},
					success: res => {
						// 从列表删除
						this.$delete(this.navList[this.currentId].dataList, index);
						this.$store.commit('statistics/dynamic', {
							collection: this.$store.state.statistics.dynamic.collection - 1
						});
					}
				});
			},
			// 选择Tag
			handleSelect(id, index) {
				this.currentItemId = id;
				this.currentId = index;
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
				this.currentId = e.detail.current;
				this.loadData('tabChange');
			},
			handleBack() {
				this.$wanlshop.back(1);
			},
			handleGoods(id){
				if(this.currentItemId === 'goods'){
					this.$wanlshop.to(`/pages/product/goods?id=${id}`)
				}else if(this.currentItemId === 'groups'){
					this.$wanlshop.to(`/pages/apps/groups/goods?id=${id}`)
				}
			}
			
		}
	}
</script>

<style lang="scss">
	.collect-container {
		&__head {
			.navigater {
				position: relative;

				.back {
					position: absolute;
					left: 25rpx;
					z-index: 9999;

					text {
						font-size: 36rpx;
						font-weight: bold;
					}
				}
			}
		}

		&__main {
			position: relative;
			z-index: 99;

			.content {
				height: 100%;

				.item {
					display: flex;

					.cu-avatar {
						height: 160rpx;
						width: 160rpx;
						flex-shrink: 0;
					}

					.subject {
						display: flex;
						flex: 1;
						flex-wrap: wrap;
						align-content: space-between;
						&>view {
							width: 100%;
						}

						.menu {
							display: flex;
							justify-content: space-between;
							.button {
								&>text + text{
									margin-left: 60rpx;
									margin-right: 10rpx;
								}
							}
						}
					}
				}
			}
		}
	}
</style>