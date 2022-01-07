<template>
	<scroll-view class="wanlshop-find-container" :style="{ height: `${windowHeight}px` }" scroll-y enable-back-to-top
		enable-flex="true" @scrolltoupper="handleUpper" @scrolltolower="handleLower">
		<wanl-empty src="find_default3x" text="店铺还没有发布任何动态" v-if="dataList.length === 0 && status !== 'loading'"/>
		<view v-if="dataList.length > 0" class="wanlshop-find-container__list">
			<view class="item margin-bj" v-for="(item, index) in dataList" :key="item.id">
				<view class="time">
					<text v-if="timeToFind(item.createtime)"
						class="day text-xxl text-bold margin-right-xs">{{timeToFind(item.createtime)}}</text>
					<block v-else>
						<text class="day text-xxl text-bold margin-right-xs">{{item.createtime_date}}</text>
						<text
							class="month wanl-gray-light">{{parseInt($wanlshop.timeFormat(item.createtime, 'mm'))}}月</text>
					</block>
				</view>
				<view class="main" @click="onFind(item)">
					<view class="title text-cut-2">
						<view class="cu-tag sm radius margin-right-xs" :class="[typeList[item.type]]">{{item.type_text}}</view>
						{{formatHtml(item.content)}}
					</view>
					<view v-if="item.images.length > 0" class="container margin-top-sm">
						<block v-for="(img, kes) in item.images" :key="kes">
							<block v-if="item.type === 'live'">
								<view class="list" :class="[kes == 0 ? 'item-live':'']"
									:style="{ backgroundImage: 'url(' + $wanlshop.oss(img, 200, 0) + ')' }">
									<block v-if="kes == 0">
										<view class="play">
											<text
												:class="[item.live.state == 1?'wlIcon-icon_zhibo-mian':'wlIcon-guijihuifang']"></text>
										</view>
										<view class="state text-white" v-if="item.live.state == 1">
											<view class="tags wanl-bg-orange"><text class="wlIcon-zhibo"></text> <text
													class="text-min">直播</text></view>
											<view class="text-sm">{{item.live.views}} 在看</view>
										</view>
										<view class="state text-white" v-else>
											<view class="tags bg-grey"><text class="text-min">回放</text></view>
											<view class="text-sm">{{item.live.views}} 看过</view>
										</view>
										<view class="like text-center text-white" v-if="item.live.state == 1">
											<view class="likebut">
												<text class="wlIcon-yiguanzhu1"></text>
											</view>
											<text class="text-min">{{item.live.like}}</text>
										</view>
									</block>
								</view>
							</block>
							<block v-else-if="item.type === 'video'">
								<view
									class="list item-1 radius-bock text-sl" 
									:style="{ backgroundImage: `url(${$wanlshop.oss(img, 200, 0)})` }" 
									@click.stop="onFind(item)" 
								>
									<text class="wlIcon-video text-white"></text>
								</view>
							</block>
							<block v-else>
								<view 
									v-if="item.images.length === 1" 
									class="list item-1 radius-bock" 
									:style="{ backgroundImage: `url(${$wanlshop.oss(img, 200, 0)})` }" 
									@click.stop="previewImage(item.images, kes)" 
								/>
								<view 
									v-else-if="item.images.length === 3" 
									class="list" 
									:class="[kes == 0 ? 'item-3':'']" 
									:style="{ backgroundImage: `url(${$wanlshop.oss(img, 200, 0)})` }"
									@click.stop="previewImage(item.images, kes)" 
								/>
								<view 
									v-else 
									class="list" 
									:style="{ backgroundImage: `url(${$wanlshop.oss(img, 200, 200)})` }"
									@click.stop="previewImage(item.images, kes)" 
								/>
							</block>
						</block>
					</view>
					<scroll-view class="scroll-view margin-top-sm" scroll-x="true">
						<view class="scroll-view-item radius-bock" :class="[item.goods.length == 1 ? 'col-1':'']"
							v-for="(goods, sub) in item.goods" :key="goods.id" @click.stop="onGoods(goods.id)">
							<view class="img">
								<image lazy-load :src="$wanlshop.oss(goods.image, 50, 50)"></image>
							</view>
							<view class="content padding-left-bj text-cut text-df">
								{{goods.title}}
								<view class="flex align-center">
									<text class="text-price text-orange">{{goods.price}}</text>
								</view>
							</view>
							<view class="icon text-lg wanl-gray-light"><text class="wlIcon-fanhui6"></text></view>
						</view>
					</scroll-view>
					<view class="flex justify-between padding-tb-sm align-center">
						<view> </view>
						<view class="flex">
							<view :class="{'text-red': item.isLike === 1}" @click.stop="handleLike(index)">
								<text :class="[item.isLike === 1 ? 'wlIcon-dianzan1' : 'wlIcon-dianzan11']"></text>
								<text class="margin-left-xs">{{item.likes}}</text>
							</view>
							<view class="margin-left" v-if="$store.state.common.appConfig.comment_switch == 'Y'">
								<text class="wlIcon-pinglun"></text>
								<text class="margin-left-xs">{{item.comments}}</text>
							</view>
						</view>
					</view>
				</view>
			</view>
			<uni-load-more :status="status" :content-text="contentText" />
		</view>
		<view :style="{height: `${tabbarHeight}px`}"></view>
	</scroll-view>
</template>

<script>
	export default {
		name: "WanlShopFind",
		props: {
			windowHeight: {
				type: Number,
				default: 0
			},
			tabbarHeight: {
				type: Number,
				default: 0
			},
			userNo: {
				type: String,
				default: ""
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
					video: 'wanl-bg-pink'
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
			}
		},
		mounted() {
			this.loadData();
		},
		methods: {
			async loadData(type) {
				this.status = 'loading';
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
				// 请求数据
				await this.$api.get({
					url: '/wanlshop/find/find/getList',
					data: {
						page: this.current_page,
						user_no: this.userNo
					},
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
			handleUpper() {
				// this.loadData('upper');
			},
			handleLower() {
				this.loadData('lower');
			},
			handleLike(index){
				if (this.$store.state.user.isLogin) {
					let data = this.dataList[index];
					this.$api.post({
						url: '/wanlshop/find/user/setFindUser',
						data: {
							id: data.id,
							type: 'likes'
						},
						success: res => {
							if(res.data === 0){
								data.likes -= 1;
							}else{
								data.likes += 1;
							}
							data.isLike = res.data;
						}
					});
				} else {
					this.$wanlshop.to('/pages/user/auth/auth')
				}
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
				return (content.replace(/<\/?.+?>/g, "").replace(/ /g, ""));
			},
			timeToFind(timestamp) {
				if (timestamp == null) timestamp = Number(new Date());
				timestamp = parseInt(timestamp);
				// 判断用户输入的时间戳是秒还是毫秒,一般前端js获取的时间戳是毫秒(13位),后端传过来的为秒(10位)
				if (timestamp.toString().length == 10) timestamp *= 1000;
				var timer = (new Date()).getTime() - timestamp;
				timer = parseInt(timer / 1000);
				// 如果小于5分钟,则返回"刚刚",其他以此类推
				let tips = '';
				var now = new Date(timestamp);
				switch (new Date().getDate() - now.getDate()) {
					case 0:
						tips = '今天';
						break;
					case 1:
						tips = '昨天';
						break;
					default:
						tips = false;
				}
				return tips;
			}
		}
	}
</script>

<style lang="scss">
	.wanlshop-find-container {
		&__list {
			.item {
				display: flex;
				margin: 25rpx 50rpx 25rpx 25rpx;

				.time {
					display: flex;
					width: 120rpx;

					.month {
						margin-top: 4rpx;
						font-size: 20rpx;
					}
				}

				.main {
					width: calc(100% - 120rpx);
					overflow: hidden;

					.container {
						display: grid;
						grid-template-columns: 1fr 1fr 1fr;

						.list {
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

									&>view {
										padding: 0 12rpx;
									}

									.tags {
										height: 100%;
										border-radius: 8rpx;
										font-size: 28rpx;

										&>text {
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

									&>view {
										font-size: 30rpx;
									}

									&>text {
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
							background-color: #fff;
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
		}
	}
</style>