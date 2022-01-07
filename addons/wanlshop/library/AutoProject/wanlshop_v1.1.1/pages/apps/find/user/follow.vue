<template>
	<view class="wanlfind-follow-container">
		<view class="edgeInsetTop"></view>
		<wanl-empty src="find_default3x" text="他/她暂时还没有任何关注哦 !" v-if="dataList.length === 0 && status !== 'loading'"/>
		<view v-if="dataList.length > 0" class="wanlfind-follow-container__list">
			<view class="item padding-bj margin-bj bg-white radius-bock" v-for="(item, index) in dataList"
				:key="item.id" @click="handleUser(item.user_no)">
				<block v-if="item.shop">
					<view class="cu-avatar margin-right-bj round"
						:style="{backgroundImage: `url(${$wanlshop.oss(item.shop.avatar, 88, 88)})`}"></view>
					<view class="subject">
						<view class="info">
							<view class="text-cut margin-bottom-xs">
								<view class="cu-tag sm bg-gradual-blue radius margin-right-xs">
									商家
								</view>
								{{item.shop.shopname}}
							</view>
							<view class="wanl-gray-light text-sm text-cut">
								<text>{{item.shop.bio || '他/她未留下任何个性签名'}}</text>
							</view>
						</view>
						<view class="menu">
							<button class="cu-btn wlIcon margin-right-sm" @click.stop="onShop(item.shop.id)">
								<text class="wlIcon-31dianpu"></text>
							</button>
							<button v-if="item.isFollow === 0" class="cu-btn wlIcon" @click.stop="handleFollow(index, item.shop)">
								<text class="wlIcon-tianjia"></text>
							</button>
							<button v-else class="cu-btn radius-bock text-sm" @click.stop="handleFollow(index, item.shop)">
								<text class="wlIcon-31guanbi margin-right-xs text-bold"></text>取消
							</button>
						</view>
					</view>
				</block>
				<block v-else>
					<view class="cu-avatar margin-right-bj round"
						:style="{backgroundImage: `url(${$wanlshop.oss(item.user.avatar, 88, 88)})`}"></view>
					<view class="subject">
						<view class="info">
							<view class="text-cut-2 margin-bottom-xs">{{item.user.nickname}}</view>
							<view class="wanl-gray-light text-sm text-cut">
								<text>{{item.user.bio || '他/她未留下任何个性签名'}}</text>
							</view>
						</view>
						<view class="menu">
							<button v-if="item.isFollow === 0" class="cu-btn wlIcon" @click.stop="handleFollow(index)">
								<text class="wlIcon-tianjia"></text>
							</button>
							<button v-else class="cu-btn radius-bock text-sm" @click.stop="handleFollow(index)">
								<text class="wlIcon-31guanbi margin-right-xs text-bold"></text>取消
							</button>
						</view>
					</view>
				</block>
			</view>
		</view>
		<view class="edgeInsetBottom"></view>
		<!-- 加载更多 -->
		<uni-load-more :status="status" :content-text="contentText" />
	</view>
</template>

<script>
	export default {
		data() {
			return {
				user_no: 0,
				dataList: [],
				reload: false, //判断是否上拉
				current_page: 1, //当前页码
				last_page: 1, //总页码
				status: 'loading',
				contentText: {
					contentdown: '',
					contentrefresh: '疯狂加载中...',
					contentnomore: ''
				}
			}
		},
		onLoad(option) {
			this.user_no = option.id;
			this.loadData();
		},
		onPullDownRefresh() {
			this.current_page = 1;
			this.reload = true;
			this.loadData();
		},
		onReachBottom() {
			//判断是否最后一页
			if (this.current_page >= this.last_page) {
				this.status = 'noMore';
			} else {
				this.reload = false;
				this.current_page = this.current_page + 1; //页码+1
				this.status = 'loading';
				this.loadData();
			}
		},
		methods: {
			async loadData() {
				this.$api.post({
					url: '/wanlshop/find/user/getList',
					data: {
						id: this.user_no,
						type: 'follow',
						page: this.current_page
					},
					success: res => {
						uni.stopPullDownRefresh();
						this.dataList = this.reload ? res.data : this.dataList.concat(res.data); //数据 追加
						this.current_page = res.current_page; //当前页码
						this.last_page = res.last_page; //总页码
						this.status = res.total == 0 ? 'noMore' : 'more';
					}
				});
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
			handleUser(user_no) {
				this.$wanlshop.to(`/pages/apps/find/user?id=${user_no}`);
			}
		}
	}
</script>

<style lang="scss">
	.wanlfind-follow-container {
		&__list {
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
</style>