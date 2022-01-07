<template>
	<scroll-view
		class="wanlshop-category-container"
		:style="{
			paddingTop: headHeight + 'px', 
			height: windowHeight + 'px',
			backgroundColor : homeModules.page ? homeModules.page.style.pageBackgroundColor : ''
		}"
		scroll-y 
		enable-back-to-top
		enable-flex="true"
		@scrolltoupper="handleUpper" 
		@scrolltolower="handleLower">
		<!-- 菜单 -->
		<view v-if="childlist.length > 0" class="wanlshop-category-container__menu bg-white margin-bj padding-bj radius-bock">
			<view v-for="(item, index) in childlist" :key="item.id" class="item" @tap="productList(item.id, item.name)">
				<image lazy-load :src="$wanlshop.oss(item.image, 50, 50)"></image>
				<view class="text-sm wanl-gray-dark margin-top-xs">
					{{item.name}}
				</view>
			</view>
		</view>
		<!-- 秒杀 -->
		<view v-if="seckillList.length > 0" class="wanlshop-category-container__seckill bg-white margin-bj padding-bj radius-bock">
			
		</view>
		<!-- 拼团 -->
		<view v-if="groupsList.length > 0" 
			class="wanlshop-category-container__groups bg-white margin-bj padding-bj radius-bock"
			:style="{backgroundImage: `url(${$wanlshop.appstc('/show/grueps-top-bg3x.png')})`}"
		>
			<view class="title margin-bottom-bj">
				<view class="text-xl">
					<text class="wlIcon-pintuan2"></text>
					<text class="margin-left-xs text-30">热门拼团</text>
				</view>
				<view class="wanl-gray" @tap="handleGroups(cid)">
					<text class="text-sm margin-right-xs">更多</text>
					<text class="wlIcon-fanhui2"></text>
				</view>
			</view>
			<scroll-view class="list" scroll-x>
                <view class="item text-center margin-right-sm" v-for="(item, index) in groupsList" :key="item.id" @tap="handleGroupsGoods(item.id)">
					<image lazy-load class="radius-bock" :src="$wanlshop.oss(item.image, 50, 50)"></image>
					<view class="text-cut">
						{{item.title}}
					</view>
					<view class="text-orange">
						<text class="text-price">{{item.price}}</text>
					</view>
				</view>
            </scroll-view>
		</view>
		<!-- 分隔符 -->
		<view class="wanlshop-category-container__separate">
			<view class="line"></view>
			<view class="content">
				<text class="wlIcon-31guanzhuxuanzhong wanl-text-orange"></text>
				<text class="text-bold wanl-gray">类目推荐</text>
			</view>
			<view class="line"></view>
		</view>
		<!-- 商品 -->
		<wanl-product :dataList="goodsList" />
		<!-- 底部提示 -->
		<uni-load-more :status="status" :content-text="contentText" />
	</scroll-view>
</template>

<script>
	export default{
		name: 'wanlShopCategory',
		props:{
			cid:{
				type: Number,
				default: 0
			},
			headHeight: {
				type: Number,
				default: 0
			},
			windowHeight: {
				type: Number,
				default: 0
			},
			currentItemId: {
				type: String,
				default: ""
			},
			homeModules: {
				type: Object,
				default () {
					return {}
				}
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
				goodsList: [],
				seckillList: [],
				groupsList: [],
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
				if(newVal === 'cid' + this.cid){
					this.loadData()
				}
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
					url: '/wanlshop/common/category',
					data: {
						id: this.cid,
						page: this.current_page
					},
					success: res => {
						this.seckillList = res.seckill;
						this.groupsList = res.groups;
						// 商品
						if(type === 'lower'){
							this.goodsList = this.goodsList.concat(res.goods.data);
						}else{
							this.goodsList = res.goods.data;
						}
						this.current_page = res.goods.current_page;  //当前页码
						this.last_page = res.goods.last_page;  //总页码
						// 判断是否还有数据
						if(res.goods.current_page === res.goods.last_page || res.goods.total === 0){
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
				this.$wanlshop.to(`/pages/product/list?data=${JSON.stringify({ category_id: category_id, category_name: category_name })}`);
			},
			handleGroups(id) {
				this.$wanlshop.to(`/pages/apps/groups/index?id=${id}`);
			},
			handleGroupsGoods(id){
				this.$wanlshop.to(`/pages/apps/groups/goods?id=${id}`);
			}
		}
	}
</script>

<style lang="scss" scoped>
	.wanlshop-category-container{
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
			background-repeat: no-repeat;
			background-size: 100% auto;
			.title{
				color: #ad4e00;
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
					font-size: 46rpx;
				}
			}
		}
	}
</style>