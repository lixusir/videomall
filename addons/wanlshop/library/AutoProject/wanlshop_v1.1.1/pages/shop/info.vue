<template>
	<view class="wanlshop-info-container">
		<view class="edgeInsetTop"></view>
		<view class="wanlshop-info-container__main bg-white">
			<view class="margin-left-bj shopuser">
				<view class="cu-avatar round margin-right-bj"
					:style="{backgroundImage: 'url('+ $wanlshop.oss(shopData.avatar, 52, 52, 2, 'avatar') +')'}"></view>
				<view class="">
					<view class="text-df text-cut" style="width: 320rpx;">
						<text>{{shopData.shopname || '加载中...'}}</text>
					</view>
					<view class="wanl-gray text-min">粉丝数 {{shopData.find_user.fans}}</view>
					<view class="wanl-gray text-min">{{shopData.city}}</view>
				</view>
			</view>
			<view class="margin-right-bj">
				<button v-if="shopData.isFollow === 0" class="cu-btn radius-bock text-sm" @click.stop="handleFollow">
					<text class="wlIcon-tianjia margin-right-xs text-bold"></text>关注
				</button>
				<button v-else class="cu-btn radius-bock text-sm" @click.stop="handleFollow">
					<text class="wlIcon-31xuanze margin-right-xs text-bold"></text>已关注
				</button>
			</view>
		</view>
		<view class="shop_info bg-white margin-top-bj padding-lr-bj">
			<view class="padding-tb-bj solid-bottom"> 店铺介绍 </view>
		</view>
		<view class="bg-white padding-bj">
			<rich-text :nodes="shopData.bio"></rich-text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				shopData: {
					id: 0,
					avatar: '',
					shopname: '',
					state: 0,
					islive: 0,
					isself: 0,
					find_user:{
						user_no: '',
						fans: 0
					}
				}
			}
		},
		onLoad(option) {
			this.loadData(option.id);
		},
		methods: {
			async loadData(shop_id) {
				this.$api.get({
					url: '/wanlshop/shop/getShopInfo',
					data: {
						id: shop_id
					},
					success: res => {
						res.bio = res.bio.replace(/<img/g, "<img style='display: block;'");
						this.shopData = res;
					}
				});
			},
			async handleFollow() {
				if (this.$store.state.user.isLogin) {
					this.$api.post({
						url: '/wanlshop/find/user/setFindUser',
						data: {
							id: this.shopData.find_user.user_no,
							type: 'follow'
						},
						success: res => {
							let concern = this.$store.state.statistics.dynamic.concern;
							if(res.data === 0){
								concern -= 1;
								this.shopData.find_user.fans -= 1;
							}else{
								concern += 1;
								this.shopData.find_user.fans += 1;
							}
							this.$store.commit('statistics/dynamic', {
								concern: concern,
							});
							this.shopData.isFollow = res.data;
						}
					});
				} else {
					this.$wanlshop.to('/pages/user/auth/auth')
				}
			}
		}
	}
</script>

<style lang="scss">
	.wanlshop-info-container {
		&__main {
			display: flex;
			align-items: center;
			justify-content: space-between;
			height: 200rpx;
			.cu-avatar{
				width: 120rpx;
				height: 120rpx;
			}
			.shopuser {
				display: flex;
				align-items: center;
			}
		}
	}
</style>
