<template>
	<view class="wanlpage-activity" :style="[pageData.style]">
		<view class="bg" :style="{backgroundImage: `url(${$wanlshop.oss(pageData.params.activityBackground, 0, 50, 1, 'transparent', 'png')})`}">
			<view class="cu-list grid bg-transparent" :class="pageData.params.colStyle">
				<view class="cu-item" v-for="(item, index) in data" :key="index">
					<view class="name">
						<text class="text-lg" :class="item.color">{{item.activityText}}</text>
						<view v-if="item.tags" class="cu-tag round sm wanl-bg-orange">
							<text class="wlIcon-dot"></text> {{item.tags}}
						</view>
					</view>
					<view v-if="item.describe" class="text-sm wanl-gray">{{item.describe}}</view>
					<view class="goods margin-top-bj">
						<block v-for="(groups, keys) in item.list" :key="keys">
							<image class="radius" :src="$wanlshop.oss(groups.image, 70, 65)" @tap="handleItem(item.activity, groups.id)" mode="aspectFill"/>
						</block>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		name: "WanlPageActivity",
		props: {
			pageData: {
				type: Object,
				default () {
					return {
						name: '活动橱窗',
						type: 'activity',
						params: [],
						style: [],
						data: []
					}
				}
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
					url: '/wanlshop/page/activity',
					data: {
						col: this.pageData.params.colStyle,
						data: JSON.stringify(this.pageData.data)
					},
					success: res => {
						this.data = res;
					}
				});
			},
			handleItem(type, id) {
				let url = null;
				switch(type) {
				    case 'group':
				    url = `/pages/apps/groups/goods?id=${id}`;
				    break;
				}
				if(url){
					this.$wanlshop.to(url);
				}else{
					this.$wanlshop.msg('组件异常');
				}
			}
		}
	}
</script>
<style>
	.wanlpage-activity {overflow: hidden;}
	.wanlpage-activity .bg{background-repeat: no-repeat; background-size: 100% auto;}
	.wanlpage-activity .cu-list { text-align: left; }
	.wanlpage-activity .cu-list>.cu-item { padding: 25rpx; overflow: hidden; }
	.wanlpage-activity .cu-list>.cu-item .cu-tag { left: 0; top: -4rpx; margin-left: 10rpx; }
	.wanlpage-activity .cu-list>.cu-item .cu-tag text {font-size: 20rpx;color: rgba(255, 255, 255, 0.5);}
	.wanlpage-activity .cu-list>.cu-item .cu-tag.sm { padding-left: 0; }
	.wanlpage-activity .cu-list>.cu-item .name .text-lg { font-size: 31rpx; }
	.wanlpage-activity .cu-list>.cu-item .goods { display: flex; justify-content: space-between; align-items: center;}
	.wanlpage-activity .cu-list>.cu-item .goods>image { height: 130rpx; background-color: #f9f9f9;}
	.wanlpage-activity .cu-list>.cu-item .goods>image + image { margin-left: 25rpx; }
</style>
