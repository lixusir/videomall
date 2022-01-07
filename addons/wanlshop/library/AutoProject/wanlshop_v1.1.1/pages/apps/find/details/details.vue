<template>
	<view>
		<!-- 导航条 -->
		<view class="cu-custom" :style="{ height: $wanlshop.wanlsys().height + 'px' }">
			<view
				class="cu-bar fixed bg-bgcolor"
				:style="{
					height: $wanlshop.wanlsys().height + 'px',
					paddingTop: $wanlshop.wanlsys().top + 'px'
				}"
			>
				<view class="action">
					<text
						class="wlIcon-fanhui1"
						style="margin-left: 0;"
						@tap="$wanlshop.back(1)"
					></text>
				</view>
				<view class="content" :style="{ top: $wanlshop.wanlsys().top + 'px' }">
					<view v-if="iShead">更多推荐</view>
					<view v-else class="userinfo">
						<block v-if="data.shop">
							<view
								class="cu-avatar round"
								:style="{
									backgroundImage: `url(${$wanlshop.oss( data.shop.avatar, 35, 35, 2, 'avatar' )})`
								}"
							></view>
							<view class="text-cut margin-lr-xs text-sm">
								{{ data.shop.shopname }}
							</view>
						</block>
						<block v-else>
							<view
								class="cu-avatar round"
								:style="{
									backgroundImage: `url(${$wanlshop.oss( data.user.avatar, 35, 35, 2, 'avatar' )})`
								}"
							></view>
							<view class="text-cut margin-lr-xs text-sm">
								{{ data.user.nickname }}
							</view>
						</block>
						<view
							class="cu-tag round bg-white"
							v-if="data.isFollow === 1"
							@tap="handleFollow(data.user_no)"
						>
							<text class="wlIcon-31guanbi margin-right-xs"></text>
							取关
						</view>
						<view
							class="cu-tag round bg-orange"
							v-else
							@tap="handleFollow(data.user_no)"
						>
							<text class="wlIcon-31xuanze margin-right-xs"></text>
							关注
						</view>
					</view>
				</view>
			</view>
		</view>
		<!-- 主体内容-->
		<view class="wanl-find bg-white" id="main">
			<!-- 内容 -->
			<view class="content padding-bj" v-if="data.type == 'new'">
				<view class="flex align-center">
					<view
						class="calendar text-center"
						:style="{
							backgroundImage: `url(${$wanlshop.appstc('/common/calendar.png')})`
						}"
					>
						{{ data.createtime_date }}
					</view>
					<view class="margin-left-xs"><text class="text-bold">今日上新</text></view>
				</view>
				<view class="wanl-gray-dark padding-tb-sm">
					<rich-text :nodes="data.content"></rich-text>
				</view>
				<wanl-more :goods="data.goods" />
			</view>
			<view class="content" v-if="data.type == 'want' || data.type == 'show'">
				<view class="swiper-box">
					<swiper circular="true" autoplay="true" @change="swiperChange($event)">
						<swiper-item v-for="(swiper, index) in data.images" :key="index">
							<image :src="$wanlshop.oss(swiper, 400, 0)" mode="aspectFill"></image>
						</swiper-item>
					</swiper>
					<view class="indicator">{{ data.current + 1 }}/{{ data.images.length }}</view>
				</view>
				<scroll-view
					class="scroll-view padding-top-bj padding-left-bj"
					:class="[data.goods.length == 1 ? 'padding-right-bj' : '']"
					scroll-x="true"
				>
					<view
						class="scroll-view-item radius-bock"
						:class="[data.goods.length == 1 ? 'col-1' : '']"
						v-for="(goods, sub) in data.goods"
						:key="goods.id"
						@tap="onGoods(goods.id)"
					>
						<view class="img">
							<image :src="$wanlshop.oss(goods.image, 50, 50)"></image>
						</view>
						<view class="goodsinfo text-cut text-df">
							{{ goods.title }}
							<view class="flex align-center">
								<text class="text-price text-orange">{{ goods.price }}</text>
							</view>
						</view>
					</view>
				</scroll-view>
				<view class="wanl-gray-dark padding-lr-bj padding-top-sm">
					<rich-text :nodes="data.content"></rich-text>
				</view>
			</view>
			<!-- 底部 -->
			<view class="flex justify-between padding-bj align-center">
				<view @tap="handleComments()" v-if="common.appConfig.comment_switch == 'Y'">
					<view
						class="cu-avatar round sm margin-right-xs"
						:style="{
							backgroundImage: `url(${$wanlshop.oss( user.avatar, 35, 35, 2, 'avatar' )})`
						}"
					></view>
					<text class="text-sm wanl-gray">快来评论吧</text>
				</view>
				<view v-else></view>
				<view class="flex">
					<view :class="{ 'wanl-orange': data.isLike === 1 }" @tap="handleLike()">
						<text
							class="margin-right-xs"
							:class="[data.isLike === 1 ? 'wlIcon-dianzan1' : 'wlIcon-dianzan11']"
						></text>
						{{ data.likes }}
					</view>
					<view
						class="margin-left"
						@tap="handleComments()"
						v-if="common.appConfig.comment_switch == 'Y'"
					>
						<text class="wlIcon-pinglun margin-right-xs"></text>
						{{ data.comments }}
					</view>
					<view class="margin-left">
						<text class="wlIcon-yueduliang margin-right-xs"></text>
						{{ data.views }}
					</view>
				</view>
			</view>
		</view>
		<!-- 列表内容 ######################################################################################-->
		<view
			class="wanl-find bg-white solid-top"
			v-for="(item, index) in listData"
			:key="item.id"
			v-if="item.type != 'activity'"
		>
			<!-- 头部 -->
			<view class="userinfo padding-bj">
				<view v-if="data.shop" class="avatar">
					<view
						@tap="handleUser(data.user_no)"
						class="cu-avatar round margin-right-bj"
						:style="{
							backgroundImage: `url(${$wanlshop.oss( data.shop.avatar, 35, 35, 2, 'avatar' )})`
						}"
					></view>
					<view>
						<view>
							<text
								@tap="handleUser(data.user_no)"
								class="text-df text-bold5 wanl-black margin-right-bj"
							>
								{{ data.shop.shopname }}
							</text>
						</view>
						<view class="wanl-gray text-sm">
							{{ $wanlshop.timeToDate(item.createtime) }}
						</view>
					</view>
				</view>
				<view v-else class="avatar">
					<view
						@tap="handleUser(data.user_no)"
						class="cu-avatar round margin-right-bj"
						:style="{
							backgroundImage: `url(${$wanlshop.oss( data.user.avatar, 35, 35, 2, 'avatar' )})`
						}"
					></view>
					<view>
						<view>
							<text
								@tap="handleUser(data.user_no)"
								class="text-df text-bold5 wanl-black margin-right-bj"
							>
								{{ data.user.nickname }}
							</text>
						</view>
						<view class="wanl-gray text-sm">
							{{ $wanlshop.timeToDate(item.createtime) }}
						</view>
					</view>
				</view>
				<view
					class="cu-tag round bg-bgcolor"
					v-if="data.isFollow === 1"
					@tap="handleFollow(data.user_no)"
				>
					<text class="wlIcon-31guanbi margin-right-xs"></text>
					取关
				</view>
				<view class="cu-tag round bg-orange" v-else @tap="handleFollow(data.user_no)">
					<text class="wlIcon-31xuanze margin-right-xs"></text>
					关注
				</view>
			</view>
			<!-- 内容 -->
			<view class="content padding-lr-bj" v-if="item.type == 'new'">
				<view class="flex align-center">
					<view
						class="calendar text-center"
						:style="{
							backgroundImage: `url(${$wanlshop.appstc('/common/calendar.png')})`
						}"
					>
						{{ item.createtime_date }}
					</view>
					<view class="margin-left-xs"><text class="text-bold">今日上新</text></view>
				</view>
				<view class="wanl-gray-dark padding-tb-sm">
					<rich-text :nodes="item.content"></rich-text>
				</view>
				<wanl-more :goods="item.goods" />
			</view>
			<view class="content" v-if="item.type == 'want' || item.type == 'show'">
				<view class="swiper-box">
					<swiper circular="true" @change="swiperChange($event, index)">
						<swiper-item v-for="(swiper, key) in item.images" :key="key">
							<image :src="$wanlshop.oss(swiper, 400, 0)" mode="aspectFill"></image>
						</swiper-item>
					</swiper>
					<view class="indicator">{{ item.current + 1 }}/{{ item.images.length }}</view>
				</view>
				<scroll-view
					class="scroll-view padding-top-bj padding-left-bj"
					:class="[data.goods.length == 1 ? 'padding-right-bj' : '']"
					scroll-x="true"
				>
					<view
						class="scroll-view-item radius-bock"
						:class="[item.goods.length == 1 ? 'col-1' : '']"
						v-for="(goods, sub) in item.goods"
						:key="goods.id"
						@tap="onGoods(goods.id)"
					>
						<view class="img">
							<image :src="$wanlshop.oss(goods.image, 50, 50)"></image>
						</view>
						<view class="goodsinfo text-cut text-df">
							{{ goods.title }}
							<view class="flex align-center">
								<text class="text-price text-orange">{{ goods.price }}</text>
							</view>
						</view>
					</view>
				</scroll-view>
				<view class="wanl-gray-dark padding-lr-bj padding-top-sm">
					<wanl-more :content="item.content" />
				</view>
			</view>
			<!-- 底部 -->
			<view class="flex justify-between padding-bj align-center">
				<view @tap="handleComments(index)" v-if="common.appConfig.comment_switch == 'Y'">
					<view
						class="cu-avatar round sm margin-right-xs"
						:style="{
							backgroundImage: `url(${$wanlshop.oss( user.avatar, 35, 35, 2, 'avatar' )})`
						}"
					></view>
					<text class="text-sm wanl-gray">快来评论吧</text>
				</view>
				<view v-else></view>
				<view class="flex">
					<view :class="{ 'wanl-orange': item.isLike === 1 }" @tap="handleLike(index)">
						<text
							class="margin-right-xs"
							:class="[item.isLike === 1 ? 'wlIcon-dianzan1' : 'wlIcon-dianzan11']"
						></text>
						{{ item.likes }}
					</view>
					<view
						class="margin-left"
						@tap="handleComments(index)"
						v-if="common.appConfig.comment_switch == 'Y'"
					>
						<text class="wlIcon-pinglun margin-right-xs"></text>
						{{ item.comments }}
					</view>
					<view class="margin-left">
						<text class="wlIcon-yueduliang margin-right-xs"></text>
						{{ item.views }}
					</view>
				</view>
			</view>
		</view>
		<!-- 模态框 -->
		<view class="WANL-MODAL text-sm" @touchmove.stop.prevent="moveHandle">
			<view
				class="cu-modal bottom-modal"
				:class="modalName == 'comments' ? 'show' : ''"
				@tap="hideModal"
			>
				<view class="cu-dialog" @tap.stop="">
					<view class="wanl-modal" style="padding-bottom: 0;">
						<view class="head padding-bj">
							<view class="content"><view class="text-lg">全部评论（{{commentData.count}}）</view></view>
						</view>
						<view class="text-left padding-lr-bj padding-bottom-bj">
							<wanl-comment
								:findId="commentData.find_id"
								:commentList="commentData.list"
								:commenCount="commentData.count"
								@complete="handleComplete"
							/>
						</view>
					</view>
				</view>
			</view>
		</view>
		<!-- 结束 -->
		<uni-load-more :status="status" :content-text="contentText" />
		<view class="edgeInsetBottom"></view>
	</view>
</template>

<script>
import { mapState } from 'vuex';
export default {
	data() {
		return {
			find_id: 0,
			modalName: null,
			iShead: false,
			height: 0,
			afferent: false,
			data: {
				user: {
					avatar: '',
					nickname: '加载中...'
				},
				shop: {
					avatar: '',
					shopname: '加载中...'
				}
			},
			listData: [], //列表
			commentData: {
				find_id: 0,
				count: 0,
				list: []
			}, //评论列表
			current_page: 1, //当前页码
			last_page: 1, //总页码
			status: 'loading',
			contentText: {
				contentdown: '',
				contentrefresh: '加载中',
				contentnomore: '- 我是有底线的 -'
			}
		};
	},
	computed: {
		...mapState(['user', 'common'])
	},
	onLoad(option) {
		this.find_id = option.id;
		this.loadData();
		if(option.afferent === 'comments'){
			this.afferent = true;
		}
	},
	onPageScroll(e) {
		this.iShead = e.scrollTop > this.height ? true : false;
	},
	onReachBottom() {
		//判断是否最后一页
		if (this.current_page >= this.last_page) {
			this.status = 'noMore';
		} else {
			this.current_page = this.current_page + 1; //页码+1
			this.status = 'loading';
			this.loadList();
		}
	},
	methods: {
		async loadData() {
			this.$api.get({
				url: '/wanlshop/find/find/getDetails',
				data: {
					id: this.find_id
				},
				success: res => {
					this.data = res;
					this.loadList(res.user_no);
					// 计算锚点高度
					setTimeout(() => {
						this.calcAnchor();
					}, 1000);
				}
			});
		},
		async loadList(user_no) {
			this.$api.get({
				url: '/wanlshop/find/find/getList',
				data: {
					user_no: user_no,
					page: this.current_page,
					find_id: this.find_id
				},
				success: res => {
					uni.stopPullDownRefresh();
					this.listData = this.listData.concat(res.data); //评论数据 追加
					this.current_page = res.current_page; //当前页码
					this.last_page = res.last_page; //总页码
					this.status = res.total == 0 ? 'noMore' : 'more';
					if(this.afferent){
						this.handleComments();
					}
				}
			});
		},
		async handleFollow(user_no) {
			if (this.$store.state.user.isLogin) {
				this.$api.post({
					url: '/wanlshop/find/user/setFindUser',
					data: {
						id: user_no,
						type: 'follow'
					},
					success: res => {
						if (this.data.shop) {
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
						this.data.isFollow = res.data;
					}
				});
			} else {
				this.$wanlshop.to('/pages/user/auth/auth');
			}
		},
		async handleLike(index = null) {
			if (this.$store.state.user.isLogin) {
				let data = index === null ? this.data : this.listData[index];
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
		async handleComments(index = null) {
			let data = index === null ? this.data : this.listData[index];
			this.$api.get({
				url: '/wanlshop/find/comments/getList',
				data: {
					id: data.id
				},
				success: res => {
					this.commentData = res;
					this.commentData.find_id = data.id;
					data.comments = res.count;
					this.modalName = 'comments';
				}
			});
		},
		handleComplete(){
			this.afferent = false;
			this.loadData();
			this.hideModal();
		},
		// 关闭弹出层
		hideModal() {
			this.modalName = null;
		},
		//轮播图指示器
		swiperChange(e, index) {
			if (index) {
				this.listData[index].current = e.detail.current;
			} else {
				this.data.current = e.detail.current;
			}
		},
		//计算锚点高度
		calcAnchor() {
			const query = uni.createSelectorQuery().in(this);
			query
				.select('#main')
				.boundingClientRect(data => {
					this.height = data.height;
				})
				.exec();
		},
		handleUser(user_no) {
			this.$wanlshop.to(`/pages/apps/find/user?id=${user_no}`);
		},
		//禁止父元素滑动
		moveHandle() {}
	}
};
</script>

<style>
/* 发现页 */
.wanl-find .userinfo {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.wanl-find .userinfo .avatar {
	display: flex;
	align-items: center;
}

.wanl-find .userinfo .avatar .cu-tag {
	font-size: 20rpx;
	padding: 0px 10rpx !important;
}

.wanl-find .userinfo .cu-btn.sm {
	padding: 0 20rpx;
	font-size: 26rpx;
	height: 50rpx;
}

.wanl-find .userinfo text[class*='wlIcon-'] {
	font-size: 28rpx;
	margin-right: 5rpx;
}

.wanl-find .content {
}

.wanl-find .content .cu-tag.sm {
	padding: 6rpx;
}

.wanl-find .container {
	display: grid;
	grid-template-columns: 1fr 1fr 1fr;
}

.wanl-find .container.col-2 {
	grid-template-columns: 1fr 1fr;
}

.wanl-find .container .item {
	display: flex;
	align-items: center;
	justify-content: center;
	border: 1px solid #fff;
	min-height: 6rem;
	width: 100%;
	background-size: cover;
	background-position: center;
	background-repeat: no-repeat;
}

.wanl-find .container .item.item-1 {
	grid-column: 1 / 3;
	grid-row: 1 / 3;
	height: 600rpx;
}

.wanl-find .container .item.item-3 {
	grid-column: 1 / 3;
	grid-row: 1 / 3;
}

.wanl-find .container .item.item-live {
	position: relative;
	grid-column: 1 / 3;
	grid-row: 1 / 3;
}

/* 直播-中心按钮 */
.wanl-find .container .item.item-live .play {
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

/* 直播-状态 */
.wanl-find .container .item.item-live .state {
	display: flex;
	position: absolute;
	left: 20rpx;
	bottom: 20rpx;
	background-color: rgba(0, 0, 0, 0.1);
	height: 36rpx;
	line-height: 36rpx;
	border-radius: 8rpx;
	overflow: hidden;
}

.wanl-find .container .item.item-live .state > view {
	padding: 0 12rpx;
}

.wanl-find .container .item.item-live .state .tags {
	height: 100%;
	border-radius: 8rpx;
	font-size: 28rpx;
}

.wanl-find .container .item.item-live .state .tags > text {
	margin-right: 4rpx;
}

/* 直播-商品数量 */
.wanl-find .container .item.item-live .number {
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
}

.wanl-find .container .item.item-live .number > view {
	font-size: 30rpx;
}

.wanl-find .container .item.item-live .number > text {
	font-size: 11px;
}

.wanl-find .container .item.item-live .like {
	position: absolute;
	bottom: 15rpx;
	right: 30rpx;
	line-height: 1.2;
}

.wanl-find .container .item.item-live .like .likebut {
	background-color: #f40;
	border-radius: 50%;
	width: 45rpx;
	line-height: 46rpx;
	height: 45rpx;
	font-size: 26rpx;
}

.wanl-find .scroll-view {
	white-space: nowrap;
	width: 100%;
}

.wanl-find .scroll-view-item {
	display: inline-flex;
	width: 60%;
	height: 100rpx;
	overflow: hidden;
	margin-right: 10rpx;
	background-color: #f9f9f9;
	align-items: center;
}

.wanl-find .scroll-view-item.col-1 {
	width: 100%;
	margin-right: 0;
}

.wanl-find .scroll-view-item .img,
.wanl-find .scroll-view-item .img image {
	height: 100rpx;
	width: 100rpx;
	border-radius: 18rpx 0 0 18rpx;
	background-color: #efefef;
}

.wanl-find .scroll-view-item .content {
	flex: 1;
	line-height: 1.3;
}

.wanl-find .scroll-view-item .icon {
	width: 60rpx;
	text-align: center;
}

.wanl-find .content .calendar {
	width: 50rpx;
	height: 50rpx;
	line-height: 60rpx;
	background-size: 100%;
	font-size: 22rpx;
	font-weight: 600;
}
.cu-custom .cu-bar .content {
	width: calc(100% - 450rpx);
}
.cu-custom .userinfo {
	display: flex;
	align-items: center;
	justify-content: space-between;
}
.cu-custom .userinfo .cu-avatar {
	flex-shrink: 0;
	width: 56rpx;
	height: 56rpx;
	overflow: hidden;
	border-radius: 1000px;
	margin: 0 !important;
}
.swiper-box {
	position: relative;
	background-color: #f7f7f7;
	width: 100%;
	height: 100vw;
}

.swiper-box swiper {
	width: 100%;
	height: 100vw;
}

.swiper-box swiper swiper-item image {
	width: 100%;
	height: 100vw;
}

.swiper-box .indicator {
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 0 25upx;
	height: 40upx;
	border-radius: 40upx;
	font-size: 22upx;
	position: absolute;
	bottom: 20upx;
	right: 20upx;
	color: #fff;
	background-color: rgba(0, 0, 0, 0.2);
}
.wanl-find .scroll-view-item .img {
	margin: 0 10rpx;
}
.wanl-find .scroll-view-item .img,
.wanl-find .scroll-view-item .img image {
	height: 80rpx;
	width: 80rpx;
	border-radius: 18rpx;
}
.scroll-view-item .goodsinfo {
	line-height: 1.2;
}
</style>
