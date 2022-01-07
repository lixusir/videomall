<template>
	<scroll-view
		class="wanlgroups-container"
		:style="{
			paddingTop: headHeight + 'px', 
			height: windowHeight + 'px'
		}"
		scroll-y 
		enable-back-to-top
		enable-flex="true"
		@scrolltoupper="handleUpper" 
		@scrolltolower="handleLower">
		<!-- 菜单 -->
		<view v-if="childlist.length > 0" class="wanlgroups-container__menu bg-white margin-bj padding-bj radius-bock">
			<block v-if="itemId === 0">
				<view
					v-for="(item, index) in childlist" 
					:key="item.id" 
					class="item" 
					@tap="handleMenu(item.id, index)"
				>
					<image :src="$wanlshop.oss(item.image, 50, 50)"></image>
					<view class="text-sm wanl-gray-dark margin-top-xs">
						{{item.name}}
					</view>
				</view>
			</block>
			<block v-else>
				<view
					v-for="(item, index) in childlist" 
					:key="item.id" 
					class="item" 
					@tap="productList(item.id, item.name)"
				>
					<image :src="$wanlshop.oss(item.image, 50, 50)"></image>
					<view class="text-sm wanl-gray-dark margin-top-xs">
						{{item.name}}
					</view>
				</view>
			</block>
		</view>
		<!-- 分隔符 -->
		<view class="wanlgroups-container__separate">
			<view class="line"></view>
			<view class="content">
				<text class="wlIcon-pintuan2 wanl-text-orange"></text>
				<text class="text-bold wanl-gray">拼团推荐</text>
			</view>
			<view class="line"></view>
		</view>
		<!-- 商品 -->
		<wanl-product :dataList="dataList" dataType="groups"/>
		<!-- 底部提示 -->
		<uni-load-more :status="status" :content-text="contentText" />
	</scroll-view>
</template>

<script>
	export default{
		name: 'wanlGroups',
		props:{
			headHeight: {
				type: Number,
				default: 0
			},
			windowHeight: {
				type: Number,
				default: 0
			},
			itemId:{
				type: Number,
				default: 0
			},
			currentItemId: {
				type: String,
				default: ""
			},
			childlist: {
				type: Array,
				default: () => {
					return []
				}
			}
		},
		data(){
			return{
				dataList: [],
				current_page: 1, //当前页码
				last_page: 1, //总页码
				status: 'loading',
				contentText: {
					contentdown: '下拉加载更多',
					contentrefresh: '疯狂加载中...',
					contentnomore: '没有更多商品了'
				}
			}
		},
		watch:{
			currentItemId(newVal,oldVal){
				if(newVal === 'cid' + this.itemId && this.dataList.length === 0){
					this.loadData()
				}
			}
		},
		mounted() {
			if(this.currentItemId === 'cid' + this.itemId){
				this.loadData();
			}
		},
		methods:{
			async loadData(type) {
				this.status = 'loading';
				// 判断上拉还是下拉
				if(type === 'upper'){
					this.current_page = 1;
				}else if(type === 'lower'){
					if (this.current_page >= this.last_page) {
						this.status = 'noMore';
						return false;
					} else {
						this.current_page += 1;
					}
				}
				// 请求数据
				await this.$api.get({
					url: '/wanlshop/groups/product/recommend',
					data: {
						cid: this.itemId,
						page: this.current_page
					},
					success: res => {
						if(type === 'lower'){
							this.dataList = this.dataList.concat(res.data);
						}else{
							this.dataList = res.data;
						}
						this.current_page = res.current_page;  //当前页码
						this.last_page = res.last_page;  //总页码
						// 判断是否还有数据
						if(res.current_page === res.last_page || res.total === 0){
							this.status = 'noMore';
						}else{
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
			productList(category_id, category_name) {
				this.$wanlshop.to(`/pages/product/list?type=groups&data=${JSON.stringify({ category_id: category_id, category_name: category_name })}`);
			},
			handleGroups(id) {
				this.$wanlshop.to(`/pages/apps/groups/index?id=${id}`);
			},
			handleGroupsGoods(id){
				this.$wanlshop.to(`/pages/apps/groups/goods?id=${id}`);
			},
			handleMenu(id, index) {
				let data = {
					id:'cid' + id,
					index: index
				}
				this.$emit('handleMenu',data);
			}
		}
	}
</script>

<style lang="scss" scoped>
	.wanlgroups-container{
		&__menu{
			display: grid;
			grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
			grid-auto-flow: row dense;
			grid-gap: 16rpx;
			.item{
				display: flex;
				align-items: center;
				justify-content: center;
				flex-wrap: wrap;
				image{
					height: 100rpx;
					width: 100rpx;
					border-radius: 6rpx;
				}
			}
		}
		&__seckill{
			
		}
		&__groups{
			.title{
				display: flex;
				align-items: center;
				justify-content: space-between;
				&>view{
					display: flex;
					align-items: center;
				}
			}
			.list{
				white-space: nowrap;
				.item {
					display: inline-block;
					width: 200rpx;
					overflow: hidden;
					image{
						height: 200rpx;
					}
				}
			}
		}
		&__recommend{
			
		}
		&__separate{
			display: flex;
			align-items: center;
			justify-content: center;
			.line{
				width: 60rpx;
				height: 2rpx;
				background-color: #dedede;
			}
			.content{
				display: flex;
				align-items: center;
				margin: 0 50rpx;
				text+text{
					margin-left: 10rpx;
				}
				[class*="wlIcon-"]{
					font-size: 40rpx;
				}
			}
		}
	}
</style>