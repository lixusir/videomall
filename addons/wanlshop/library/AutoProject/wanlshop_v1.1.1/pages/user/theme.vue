<template>
	<view class="wanlgroups-user-container">
		<view class="wanlgroups-user-container__head" :style="{height: headHeight + 'px'}">
			<view class="background" v-if="headerOpacity > 0"
				:style="{height: headHeight + 'px', opacity: headerOpacity, backgroundColor: themeData.color}">
				<view class="bgcolor"></view>
			</view>
			<view class="navigater text-white" :style="{
					height: headHeight + 'px', 
					paddingTop: headTop + 'px'
				}">
				<view class="text-xl">
					<view class="margin-right">
						<text class="wlIcon-fanhui1 text-bold" @tap="handleBack"></text>
					</view>
					<view class="text-cut" style="width: 100rpx;">
						<text class="text-lg" v-if="headerOpacity > 0" :style="{opacity: headerOpacity}">
							{{user.nickname}}
						</text>
					</view>
				</view>
				<view v-if="isChoice" class="text-xxl" @tap="changeTheme">
					<text class="text-30 text-bold">保存</text>
				</view>
			</view>
		</view>
		<!-- 主体 -->
		<view class="wanlgroups-user-container__main"
			:style="{backgroundColor: themeData.color, backgroundImage: `url(${$wanlshop.oss(themeData.image, 0, 50, 1, 'transparent', 'png')})`}">
			<view class="head text-white" :style="{paddingTop: headHeight + 'px'}">
				<view class="userinfo">
					<view class="cu-avatar margin-right-bj round"
					:style="{backgroundImage: `url(${$wanlshop.oss(user.avatar, 62, 62, 2, 'avatar')})`}"></view>
					<view>
						<view class="text-xl margin-bottom-xs">{{user.nickname}}</view>
						<view class="text-min text-white-agba">
							<text>账户号：</text>
							<text>{{themeData.user_no}}</text>
							<text class="wlIcon-fuzhi margin-left-sm" @click.stop="$wanlshop.copy(themeData.user_no)"></text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<scroll-view class="wanlgroups-user-container__list bg-white padding-bj" scroll-y
			:style="{height: windowHeight - headHeight - 150 + 'px'}">
			<view class="content">
				<view 
					class="item" 
					v-for="(item, index) in themeData.list" 
					:key="item.id" 
					@tap="choiceTheme(item.image,item.color)"
				>
					<image :src="$wanlshop.oss(item.image, 0, 50)" mode=""></image>
					<view class="background"></view>
					<view class="tag text-white round">
						<view class="colour round margin-xs" :style="{backgroundColor: item.color}"></view>
						<view class="name text-min margin-right-sm"> {{item.name}} </view>
					</view>
				</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	export default {
		data() {
			return {
				isChoice: false,
				headHeight: 0,
				headerOpacity: 0,
				windowHeight: 0,
				headTop: 0,
				isMyfind: false,
				themeData: {
					user_no: '',
					user: {
						avatar: '',
						nickname: '加载中..',
					},
					color: '#e9dacd',
					image: '',
					list: []
				}
			};
		},
		computed: {
			...mapState(['user'])
		},
		onLoad() {
			// 计算页面尺寸
			let sys = this.$wanlshop.wanlsys();
			this.headTop = sys.top;
			this.headHeight = sys.height;
			this.windowHeight = sys.windowHeight;
			// 加载 Api
			this.loadData();
		},
		onPageScroll(e) {
			let tmpY = 123;
			e.scrollTop = e.scrollTop > tmpY ? 123 : e.scrollTop;
			this.headerOpacity = e.scrollTop * (1 / tmpY);
		},
		methods: {
			async loadData() {
				this.$api.post({
					url: '/wanlshop/find/user/getTheme',
					success: res => {
						this.themeData = res;
					}
				});
			},
			handleBack() {
				this.$wanlshop.back(1);
			},
			choiceTheme(image, color){
				this.isChoice = true;
				this.themeData.image = image;
				this.themeData.color = color;
			},
			changeTheme(){
				this.$wanlshop.prePage().editTheme({
					url: this.themeData.image,
					color: this.themeData.color
				});
				this.handleBack();
			}
		}
	}
</script>


<style lang="scss">
	.wanlgroups-user-container {
		&__head {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			z-index: 999;

			.background {
				position: absolute;
				left: 0;
				right: 0;
				z-index: 1;

				.bgcolor {
					width: 100%;
					height: 100%;
					background-color: rgba(0, 0, 0, .4);
				}
			}

			.navigater {
				display: flex;
				justify-content: space-between;
				align-items: center;
				position: absolute;
				left: 0;
				right: 0;
				z-index: 999;
				padding-left: 25rpx;
				padding-right: 25rpx;
				/* #ifdef MP */
				padding-right: 220rpx;
				/* #endif */
				&>view {
					display: flex;
					align-items: center;
				}
			}
		}

		&__main {
			background-size: 100% auto;
			background-repeat: no-repeat;
			background-position-y: center;
			.text-white-agba{
				color: rgba(255,255,255,.9);
			}
			.head {
				padding: 40rpx 30rpx;
				background-color: rgba(0, 0, 0, .4);

				.userinfo {
					height: 140px;
					display: flex;
					align-items: center;
					.cu-avatar {
						width: 160rpx;
						height: 160rpx;
					}
				}
			}
		}
		&__list {
			margin-top: -24rpx;
			border-radius: 24rpx 24rpx 0 0;
			.content{
				display: flex;
				flex-direction: row;
				flex-wrap: wrap;
				justify-content: space-between;
				
				.item{
					position: relative;
					width: calc(50% - 14rpx);
					height: 250rpx;
					overflow: hidden;
					margin-bottom: 28rpx;
					border-radius: 12rpx;
					image{
						position: absolute;
						height: 250rpx;
						z-index: 97;
					}
					.background{
						position: absolute;
						background-color: rgba(0,0,0,.1);
						width: 100%;
						height: 100%;
						z-index: 98;
					}
					.tag{
						position: absolute;
						top: 20rpx;
						left: 20rpx;
						background-color: rgba(0,0,0,.2);
						display: flex;
						align-items: center;
						z-index: 99;
						.colour{
							width: 30rpx;
							height: 30rpx;
						}
						.name{
							line-height: 30rpx;
						}
					}
				}
			}
			
		}
	}
</style>