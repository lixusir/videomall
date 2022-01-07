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
				<wanl-empty src="find_default3x" text="没有发现任何足迹" v-if="data.arrData.length === 0 && data.loaded"/>
				<scroll-view class="content" scroll-y @scrolltolower="loadData">
					<view v-for="(item, footprint) in data.arrData" :key="footprint">
						<view class="wanl-gray text-sm margin-sm">
							{{ footprint }}
						</view>
						<view class="box">
							<view class="item" v-for="(goods, index) in item" :key="index">
								<image :src="$wanlshop.oss(goods.goods.image, 136, 126)" @tap="handleGoods(goods.goods_id)"></image>
								<view class="text-price margin-sm text-orange">{{goods.goods.price}}</view>
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
						type_text: '商品足迹',
						dataList: [],
						arrData: [],
						loadingType: 'more',
						current_page: 1
					},
					{
						type: 'groups',
						type_text: '团购足迹',
						dataList: [],
						arrData: [],
						loadingType: 'more',
						current_page: 1
					}
				],
				contentText: {
					contentdown: ' ',
					contentrefresh: '足迹加载中...',
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
					url: '/wanlshop/product/footprint',
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
							navItem.dataList.push(item);
						});
						navItem.arrData = this.formatData(navItem.dataList);
						this.$set(navItem, 'loaded', true);
					}
				});
			},
			// 选择Tag
			handleSelect(id, index) {
				this.currentItemId = id;
				this.currentId = index;
			},
			handleGoods(id){
				if(this.currentItemId === 'goods'){
					this.$wanlshop.to(`/pages/product/goods?id=${id}`)
				}else if(this.currentItemId === 'groups'){
					this.$wanlshop.to(`/pages/apps/groups/goods?id=${id}`)
				}
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
			// 动画
			animationFinish(e) {
				//#ifdef APP-PLUS
				this.changeCurrent(e)
				//#endif
			},
			formatData(data) {
				var groups = {};
				data.forEach(res => {
					var cate = this.getLocalTime(res.createtime);
					if (!groups[cate]) {
						groups[cate] = [];
					}
					groups[cate].push(res);
				});
				return groups;
			},
			getLocalTime(nS) {
				return new Date(parseInt(nS) * 1000).toLocaleDateString().replace(/\//g, '-')
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
			.content{
				height: 100%;
				.box {
					display: flex;
					flex-wrap: wrap;
					justify-content: space-between;
					&:after { 
						content: ""; 
						width: 33%;
					}
					.item {
						width: 33%;
						background-color: #ffffff;
						margin-bottom: 5rpx;
						image{
							height: 230rpx;
						}
					}
				}
			}
		}
	}
</style>