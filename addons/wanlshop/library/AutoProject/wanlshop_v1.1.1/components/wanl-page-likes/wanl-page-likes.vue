<template>
	<view class="wanlpage-likes wanl-product col-2-20" :style="[pageData.style]">
		<wanl-product :dataList="dataList" :dataStyle="`col-${pageData.params.colthree}-${pageData.params.colmargin}`" />
		<uni-load-more :status="status" :content-text="contentText" />
	</view>
</template>
<script>
export default {
	name: 'WanlPageLikes',
	props: {
		lower: {
			type: Number,
			default: 0
		},
		pageData: {
			type: Object,
			default: function() {
				return {
					name: '猜你喜欢',
					type: 'likes',
					params: [],
					style: [],
					data: []
				};
			}
		}
	},
	data() {
		return {
			dataList: [], //数据
			current_page: 1, //当前页码
			last_page: 1, //总页码
			status: 'loading',
			contentText: {
				contentdown: '下拉加载更多',
				contentrefresh: '疯狂加载中...',
				contentnomore: '我是有底线的'
			}
		};
	},
	created() {
		this.loadData();
	},
	methods: {
		async loadData(type) {
			this.status = 'loading';
			// 判断上拉还是下拉
			if (type === 'lower') {
				if (this.current_page >= this.last_page) {
					this.status = 'noMore';
					return false;
				} else {
					this.current_page += 1;
				}
			}
			this.$api.get({
				url: '/wanlshop/product/likes',
				data: {
					page: this.current_page
				},
				success: res => {
					if (type === 'lower') {
						this.dataList = this.dataList.concat(res.data);
					} else {
						this.dataList = res.data;
					}
					this.current_page = res.current_page; //当前页码
					this.last_page = res.last_page; //总页码
					// 判断是否还有数据
					if (res.current_page === res.last_page || res.total === 0) {
						this.status = 'noMore';
					} else {
						this.status = 'more';
					}
				}
			});
		}
	},
	watch: {
		lower(newVal, oldVal) {
			this.loadData('lower');
		}
	}
};
</script>
<style></style>
