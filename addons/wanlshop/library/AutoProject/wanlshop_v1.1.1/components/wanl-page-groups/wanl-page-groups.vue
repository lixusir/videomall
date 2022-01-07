<template>
	<view class="wanlpage-groups" :style="[pageData.style]">
		<view
			class="wanlpage-groups__mian"
			:class="[`col-3-${pageData.params.colmargin}`]"
			:style="{ backgroundImage: `url(${$wanlshop.oss( pageData.params.groupsBackground, 0, 50, 1, 'transparent', 'png' )})` }"
		>
			<view class="head">
				<view class="title">
					<text
						:style="{ color: pageData.params.titleColor, fontSize: pageData.params.titleFontSize }"
						class="margin-right-sm text-bold"
					>
						{{ pageData.params.titleText }}
					</text>
					<text :style="{ color: pageData.params.infoColor, fontSize: pageData.params.infoFontSize }" >
						{{ pageData.params.infoText }}
					</text>
				</view>
				<view class="text-gray text-sm" @tap="handleGroups">
					<text class="margin-right-xs">更多</text>
					<text class="wlIcon-fanhui2"></text>
				</view>
			</view>
			<view class="list">
				<view class="item" v-for="(item, index) in data" :key="item.id" @tap="handleGoods(item.id)">
					<image :src="$wanlshop.oss(item.image, 100, 100)" mode="aspectFill"></image>
					<view class="text-cut text-sm">{{item.title}}</view>
					<view class="text-cut text-orange text-xs">{{item.description}}</view>
					<view class="flex align-center justify-between margin-top-xs">
						<text class="text-price text-lg text-orange">{{item.price}}</text>
						<text class="cu-tag round sm wanl-bg-orange">拼</text>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
export default {
	name: 'WanlPageGroups',
	props: {
		pageData: {
			type: Object,
			default: function() {
				return {
					name: '拼团组件',
					type: 'groups',
					params: [],
					style: [],
					data: []
				};
			}
		},
		shopData: {
			type: Object,
			default () {}
		}
	},
	data() {
		return {
			data: []
		};
	},
	created() {
		this.loadData();
	},
	methods: {
		async loadData() {
			this.$api.get({
				url: '/wanlshop/page/groups',
				data: {
					shop: this.shopData ? this.shopData.id : 0
				},
				success: res => {
					this.data = res;
				}
			});
		},
		handleGoods(id) {
			this.$wanlshop.to(`/pages/apps/groups/goods?id=${id}`);
		},
		handleGroups(){
			this.$wanlshop.to(`/pages/apps/groups/index`);
		}
	}
};
</script>

<style lang="scss">
.wanlpage-groups {
	&__mian {
		background-repeat: no-repeat;
		background-size: 100% auto;
		&.col-3-10 {
			.head {
				padding: 0 10rpx;
			}
			.list {
				padding: 0 10rpx;
				.item {
					width: calc((100% - 20rpx) / 3);
					margin-bottom: 10rpx;
				}
			}
		}
		&.col-3-15 {
			.head {
				padding: 0 15rpx;
			}
			.list {
				padding: 0 15rpx;
				.item {
					width: calc((100% - 30rpx) / 3);
					margin-bottom: 15rpx;
				}
			}
		}
		&.col-3-20 {
			.head {
				padding: 0 20rpx;
			}
			.list {
				padding: 0 20rpx;
				.item {
					width: calc((100% - 40rpx) / 3);
					margin-bottom: 20rpx;
				}
			}
		}
		&.col-3-25 {
			.head {
				padding: 0 25rpx;
			}
			.list {
				padding: 0 25rpx;
				.item {
					width: calc((100% - 50rpx) / 3);
					margin-bottom: 25rpx;
				}
			}
		}
		&.col-3-30 {
			.head {
				padding: 0 30rpx;
			}
			.list {
				padding: 0 30rpx;
				.item {
					width: calc((100% - 60rpx) / 3);
					margin-bottom: 30rpx;
				}
			}
		}

		.head {
			display: flex;
			justify-content: space-between;
			align-items: center;
			height: 85rpx;
			.title {
				display: flex;
				align-items: center;
			}
		}
		.list {
			display: flex;
			flex-wrap: wrap;
			justify-content: space-between;
			&::after{
				content: '';
				display: block;
				width: 33%;
				visibility: hidden;
			}
			.item {
				flex-wrap: wrap;
				image {
					height: 200rpx;
					margin-bottom: 6rpx;
					border-radius: 18rpx;
					overflow: hidden;
					background-color: #f9f9f9;
				}
			}
		}
	}
}
</style>
