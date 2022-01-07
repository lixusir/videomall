<template>
	<view class="groups-container">
		<view class="edgeInsetTop"></view>
		<view v-if="loading" class="">
			<view v-if="data.state === 'success'" class="groups-container__state padding margin-bottom-bj margin-lr-bj bg-white text-center radius-bock">
				<view class="text-green text-xl">
					<text class="wlIcon-wancheng margin-right-xs"></text>
					<text>拼团成功</text>
				</view>
				<view class="margin-top-bj text-sm wanl-gray-dark">
					<text>你可以在会员中心 - 拼团订单中</text>
					<text class="margin-lr-xs text-blue" @tap="$wanlshop.auth('/pages/apps/groups/order/order?state=3')">查看发货</text>
					<text>动态</text>
				</view>
			</view>
			<view class="groups-container__user padding-bj margin-bottom-bj margin-lr-bj bg-white radius-bock">
				<view class="cu-avatar round margin-right-bj"
					:style="{ backgroundImage: 'url(' + $wanlshop.oss(data.user.avatar, 80, 80, 2, 'avatar') + ')' }">
				</view>
				<view class="content">
					<view class="">
						{{data.user.nickname}}
					</view>
					<view class="text-min wanl-gray">
						发起了{{data.people_num}}人{{data.group_type == 'group' ? '拼团' : '阶梯团'}} 
					</view>
				</view>
			</view>
			<view class="groups-container__goods margin-bottom-bj margin-lr-bj padding-bj bg-white radius-bock" @tap="onGroupsGoods(data.orderGoods.goods_id, data.id)">
				<view class="cu-avatar radius-bock margin-right-bj"
					:style="{ backgroundImage: 'url(' + $wanlshop.oss(data.orderGoods.image, 80, 80, 2, 'avatar') + ')' }">
				</view>
				<view class="content">
					<view class="title text-cut-2 wanl-gray-dark">
						<text class="cu-tag sm radius wanl-bg-pink margin-right-xs">{{data.people_num}}人{{data.group_type == 'group' ? ' 团' : ' 阶梯团'}}</text>
						<text>{{data.orderGoods.title}}</text>
					</view>
					<view class="text-min flex justify-between flex-sub align-center">
						<view class="difference">
							<text class="text-price margin-right-xs text-orange text-lg">{{data.orderGoods.group_price}}</text>
							<text class="cu-tag sm radius">规格 {{data.orderGoods.difference}}</text>
						</view>
						<view class="wanl-gray-light">
							<text class="margin-right-xs">单买价</text>
							<text class="text-price">{{data.orderGoods.market_price}}</text>
						</view>
					</view>
				</view>
			</view>
			<view class="groups-container__team padding margin-bottom-bj margin-lr-bj bg-white radius-bock">
				<view class="title">
					<view v-if="data.state === 'start'">
						<view class="text-center">
							邀请 <text class="text-red margin-lr-xs"> {{data.people_num - data.team.length}} </text> 位好有，团满可拼团成功
						</view>
						<wanl-timeout class="text-min" :endTime="data.validitytime" 
						padding="2rpx"
						margin="6rpx"
						color="#fff" backgroundColor="#32343c" borderColor="#32343c"/>
					</view>
					<view v-if="data.state === 'success' || data.state === 'auto'">
						<view class="text-center">
							团长人气太高，已拼团成功
						</view>
					</view>
					<view v-if="data.state === 'fail'">
						<view class="text-center text-red">
							该团未能按时凑齐人数，拼团失败
						</view>
						<view class="text-center text-gray text-sm">
							自动退款将返余额
						</view>
					</view>
				</view>
				
				<view class="list margin-bottom">
					<view v-for="(item, index) in data.team" :key="item.id">
						<view class="cu-avatar round margin-right-sm"
							:style="{ backgroundImage: 'url(' + $wanlshop.oss(item.user_id === 0 ? item.avatar : item.user.avatar, 52, 52, 2, 'avatar') + ')' }">
							<view v-if="item.user_id == data.user_id" class="cu-tag badge">
								团长
							</view>
						</view>
					</view>
					<view v-for="(item, index) in (data.people_num - data.team.length)" :key="index">
						<view class="ungrouped">
							<text class="wlIcon-31wodexuanzhong"></text>
						</view>
					</view>
				</view>
				
				<view class="button">
					<!-- 拼团状态:start=拼团中,success=已成团,fail=拼团失败,auto=自动成团 -->
					<block v-if="user.isLogin && user.id === data.user_id">
						<button v-if="data.state === 'start'" class="cu-btn wanl-bg-orange lg round" @tap="showModal('share')">
							邀请好有拼团
						</button>
						<button v-else class="cu-btn wanl-bg-orange lg round" @tap="onGroupsGoods(data.orderGoods.goods_id)">
							再次拼团
						</button>
					</block>
					<block v-else>
						<button v-if="data.state === 'start'" class="cu-btn wanl-bg-orange lg round" @tap="onGroupsGoods(data.orderGoods.goods_id, data.id)">
							参与好友的拼团
						</button>
						<button v-else class="cu-btn wanl-bg-orange lg round" @tap="onGroupsGoods(data.orderGoods.goods_id)">
							发起新的拼团
						</button>
					</block>
				</view>
			</view>
			<view class="groups-container__explain padding-bj bg-white margin-lr-bj radius-bock">
				<view class="text-30 wanl-gray-dark">
					拼团玩法
				</view>
				<view class="text-min wanl-gray">
					选择商品开团 / 参团 > 邀请好有参团 > 人满成团
				</view>
			</view>
		</view>
		<!-- 弹出框 -->
		<view class="WANL-MODAL text-sm" @touchmove.stop.prevent="moveHandle" v-if="loading">
			<!-- 分享 -->
			<view class="cu-modal bottom-modal" :class="modalName == 'share' ? 'show' : ''" @tap="hideModal">
				<view class="cu-dialog" @tap.stop="">
					<wanl-share 
						:scrollAnimation="scrollAnimation" 
						:shareTitle="data.orderGoods.title"
						:shareText="data.orderGoods.description" 
						:image="$wanlshop.oss(data.orderGoods.image,50, 50)"
						:href="common.appConfig.domain + '/pages/apps/groups/goods?id='+ data.orderGoods.id +'&groups_id='+ data.id +'&QRtype=goods'"
						isReport 
						@change="wanlShare" 
					/>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	export default {
		data() {
			return {
				group_no: 0,
				data: {
					user: {
						nickname: '',
						avatar: ''
					},
					team:[]
				},
				loading: false,
				modalName: null ,// 弹出层
				scrollAnimation: 100 //分享滚动
			};
		},
		computed: {
			...mapState(['user', 'common'])
		},
		onLoad(option) {
			this.group_no = option.id;
			this.loadData();
		},
		methods: {
			async loadData(){
				this.$api.get({
					url: '/wanlshop/groups/team/getGroupsDetails',
					data: {
						id: this.group_no
					},
					success: res => {
						this.loading = true;
						this.data = res; //评论数据 追加
					}
				});
			},
			onGroupsGoods(id, groups_id){
				if(groups_id){
					this.$wanlshop.to(`/pages/apps/groups/goods?id=${id}&groups_id=${groups_id}`);
				}else{
					this.$wanlshop.to(`/pages/apps/groups/goods?id=${id}`);
				}
			},
			// 弹出层
			showModal(name) {
				this.modalName = name;
				setTimeout(() => {
					this.scrollAnimation = 0;
				}, 300);
			},
			// 关闭弹出层
			hideModal(name) {
				this.modalName = null;
			},
			// 分享回调
			wanlShare(e) {
				if (e) {
					this.$wanlshop.auth(`/pages/user/complaint/complaint?id=${this.goodsData.id}&type=3`);
				} else {
					this.modalName = null;
				}
			}
		}
	}
</script>

<style lang="scss">
	.groups-container{
		&__state{
			.text-green{
				display: flex;
				align-items: center;
				justify-content: center;
				[class*="wlIcon-"]{
					font-size: 50rpx;
				}
			}
		}
		&__user{
			display: flex;
			align-items: center;
			.cu-avatar{
				width: 80rpx;
				height: 80rpx;
			}
		}
		&__goods{
			display: flex;
			.cu-avatar{
				width: 140rpx;
				height: 140rpx;
			}
			.content{
				flex: 1;
				display: flex;
				align-content: space-between;
				flex-wrap: wrap;
				.difference{
					display: flex;
					align-items: center;
				}
			}
		}
		&__team{
			.title{
				width: 100%;
				margin-top: 20rpx;
				margin-bottom: 40rpx;
				& > view{
					display: flex;
					justify-content: center;
					flex-wrap: wrap;
					.text-center{
						width: 100%;
						margin-bottom: 16rpx;
					}
				}
			}
			.list{
				display: flex;
				justify-content: center;
				.ungrouped{
					background-color: #e4e4e4;
					border-radius: 2500rpx;
					width: 64rpx;
					height: 64rpx;
					line-height: 64rpx;
					text-align: center;
					margin-right: 20rpx;
					text{
						font-size: 60rpx;
						color: #fff;
					}
				}
			}
			.button{
				display: flex;
				justify-content: center;
				margin-top: 60rpx;
				.cu-btn{
					width: 96%;
				}
			}
		}
		&__explain{
			display: flex;
			align-items: center;
			justify-content: space-between;
		}
	}
</style>
