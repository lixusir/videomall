<template>
	<view class="wanlshop-product-container" :style="{ height: `${windowHeight}px` }">
		<view class="wanlshop-product-container__bar">
			<view class="list">
				<view class="item" :class="{ active: filterIndex === 0 }" @tap="handleBarTab(0)">综合</view>
				<view class="item" :class="{ active: filterIndex === 1 }" @tap="handleBarTab(1)">销量</view>
				<view class="item" :class="{ active: filterIndex === 2 }" @tap="handleBarTab(2)">新上架</view>
				<view class="item" :class="{ active: filterIndex === 3 }" @tap="handleBarTab(3)">
					<view>价格</view>
					<view class="box">
						<text :class="{ active: priceOrder === 1 && filterIndex === 3 }" class="wlIcon-sheng" />
						<text :class="{ active: priceOrder === 2 && filterIndex === 3 }" class="wlIcon-jiang" />
					</view>
				</view>
			</view>
			<view class="text-30 text-bold" @tap="handleListStyle">
				<text class="wlIcon-listing-jf" v-if="listStyle === 'col-2-10'"></text>
				<text class="wlIcon-liebiao" v-else></text>
			</view>
		</view>
		<scroll-view
			:style="{ height: `${windowHeight - 40}px`}"
			scroll-y
			enable-back-to-top
			enable-flex="true"
			@scrolltoupper="handleUpper" 
			@scrolltolower="handleLower">
			<wanl-product v-if="dataList.length > 0" :dataList="dataList" :dataType="params.type" :dataStyle="listStyle"/>
			<wanl-empty v-if="dataList.length === 0 && status !=='loading'" :text="empty"/>
			<uni-load-more :status="status" :content-text="contentText" />
			<view :style="{ height: `${tabbarHeight}px`}"/>
		</scroll-view>
	</view>
</template>

<script>
	export default{
		name: "wanlShopProduct",
		props: {
			tabbarHeight: {
				type: Number,
				default: 0
			},
			windowHeight: {
				type: Number,
				default: 0
			},
			tabId: {
				type: String,
				default: ""
			},
			option: {
				type: Object,
				default () {
					return {}
				}
			}
		},
		data(){
			return{
				filterIndex: 0, 
				priceOrder: 0, // 1 价格从低到高 2价格从高到低
				listStyle: 'col-2-10',
				dataList: [], // 数据
				empty: '',
				params: {
					search: '',
					sort: 'weigh',
					order: 'asc',
					page: 1,
					filter: {},
					op: {},
					type: 'goods'
				},
				last_page: 1, //总页码
				status: 'loading',
				contentText: {
					contentdown: '下拉加载更多',
					contentrefresh: '疯狂加载中...',
					contentnomore: ''
				}
			}
		},
		mounted() {
			if(this.tabId === 'goods'){
				this.initialize(this.tabId);
			}
		},
		methods:{
			initialize(type){
				this.params.type = type === 'groups' ? 'groups':'goods';
				// 判断是否类目搜索
				if(this.option.category_id){
					this.params.filter.shop_category_id = this.option.category_id;
					this.params.op.shop_category_id = 'FIND_IN_SET';
					this.loadData();
					if (this.dataList.length === 0) {
						this.empty = `没有查询到相关宝贝，以下为您推荐猜你喜欢的商品~`;
					}
				}else{
					this.params.filter.shop_id = this.option.shop_id;
					this.params.op.shop_id = 'in';
					if(type === 'news'){
						this.params.sort = 'createtime';
					}
					this.loadData();
					if (this.dataList.length === 0) {
						this.empty = '暂时没有在店铺内找到任何产品，请搜索查询~';
					}
				}
			},
			async loadData(type) {
				this.status = 'loading';
				// 判断上拉还是下拉
				if(type === 'upper'){
					this.params.page = 1;
				}else if(type === 'lower'){
					if (this.params.page >= this.last_page) {
						this.status = 'noMore';
						return false;
					} else {
						this.params.page += 1;
					}
				}
				// 请求数据
				await this.$api.get({
					url: '/wanlshop/product/lists',
					loadingTip: '加载中...',
					data: this.params,
					success: res => {
						if(type === 'lower'){
							this.dataList = this.dataList.concat(res.data);
						}else{
							this.dataList = res.data;
						}
						this.params.page = res.current_page;  //当前页码
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
			handleListStyle(){
				if (this.listStyle == 'col-1-10') {
					this.listStyle = 'col-2-10';
				} else {
					this.listStyle = 'col-1-10';
				}
			},
			handleBarTab(index){
				if (this.filterIndex === index && index !== 3) {
					return;
				}
				this.filterIndex = index;
				if (index === 3) {
					this.priceOrder = this.priceOrder === 1 ? 2 : 1;
				} else {
					this.priceOrder = 0;
				}
					
				// 排序方式
				if (index === 0) {
					this.params.sort = 'weigh';
					this.params.order = 'asc';
				}
				if (index === 1) {
					this.params.sort = 'sales';
					this.params.order = 'asc';
				}
				if (index === 2) {
					this.params.sort = 'createtime';
					this.params.order = 'asc';
				}
				if (index === 3 && this.priceOrder === 1) {
					this.params.sort = 'price';
					this.params.order = 'asc';
				}
				if (index === 3 && this.priceOrder === 2) {
					this.params.sort = 'price';
					this.params.order = 'desc';
				}
				this.status = 'loading';
				this.params.page = 1;
				this.loadData();
				uni.pageScrollTo({
					duration: 300,
					scrollTop: 0
				});
			}
		},
		watch: {
			tabId(newVal, oldVal) {
				if (newVal === 'news' || newVal === 'groups') {
					this.dataList = [];
					this.initialize(newVal);
				}
			}
		}
	}
</script>

<style lang="scss">
	.wanlshop-product-container{
		background-color: #f9f9f9;
		border-radius: 30rpx 30rpx 0 0;
		&__bar{
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 0 30rpx;
			height: 40px;
			overflow: hidden;
			color: #555;
			.list{
				width: 75%;
				display: flex;
				align-items: center;
				justify-content: space-between;
				.item{
					display: flex;
					align-items: center;
					&.active{
						color: #f40;
						font-weight: bold;
					}
					.box{
						color: #ccc;
						margin-top: 2rpx;
						margin-left: 8rpx;
						display: flex;
						flex-direction: column;
						line-height: 1;
						.active{
							color: #f40;
						}
					}
				}
			}
		}
	}
</style>