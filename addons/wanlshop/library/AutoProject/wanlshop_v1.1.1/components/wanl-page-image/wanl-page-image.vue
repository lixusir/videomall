<template>
	<view class="wanlpage-advert-image">
		<view class="image" 
		:class="[`layout-${pageData.params.imgLayout}`]"
		:style="[pageData.style]">
			<view class="item" 
				v-for="(image, keys) in pageData.data" 
				:key="keys"
				:style="{ margin: pageData.params.imgPaddingTb + ' ' + pageData.params.imgPaddingLf }"
				@tap="onLink(image.link)">
				<image 
					:src="$wanlshop.oss(image.image, 420, 0, 1, 'transparent', 'png')" 
					mode="widthFix" />
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		name: "WanlPageImage",
		props: {
			pageData: {
				type: Object,
				default: function() {
					return {
						name: '图片组件',
						type: 'image',
						params: [],
						style: [],
						data: []
					}
				}
			},
			methods:{
				async onLink(url){
					this.$wanlshop.on(url);
				}
			}
		}
	}
</script>
<style lang="scss">
	.wanlpage-advert-image {
		image{
			width: 100% !important;
		}
		/*图片橱窗*/
		.image {
			display: flex;
			overflow: hidden;
			.item{
				display: grid;
			}
			&.layout-1 {
				display: block;
			}

			&.layout-2 {
				flex-wrap: wrap;

				&>view {
					width: 50%;
				}
			}

			&.layout-3 {
				flex-wrap: wrap;

				&>view {
					width: 33.33333%;
				}
			}

			&.layout-4 {
				flex-wrap: wrap;

				&>view {
					width: 25%;
				}
			}

			&.layout-5 {
				flex-wrap: wrap;

				&>view {
					width: 20%;
				}
			}

			&.layout-11 {
				display: grid;
				grid-template-columns: 25% 25% 25% 25%;
				grid-auto-flow: row dense;

				view:nth-child(1) {
					grid-column: 1 / 3;
					grid-row: 1 / 3;
				}

				view:nth-child(2) {
					grid-column: 3 / 5;
					grid-row: 1 / 2;
				}
			}

			&.layout-12 {
				display: grid;
				grid-template-columns: 25% 25% 25% 25%;
				grid-auto-flow: row dense;

				view:nth-child(1) {
					grid-column: 1 / 5;
				}

				view:nth-child(2),
				view:nth-child(4) {
					grid-column: 1 / 3;
				}

				view:nth-child(3),
				view:nth-child(5) {
					grid-column: 3 / 5;
				}
			}

			&.layout-13 {
				display: grid;
				grid-template-columns: 25% 25% 25% 25%;
				grid-auto-flow: row dense;

				view:nth-child(1) {
					grid-column: 1 / 5;
				}

				view:nth-child(2),
				view:nth-child(5),
				view:nth-child(7) {
					grid-column: 1 / 3;
				}

				view:nth-child(6),
				view:nth-child(8) {
					grid-column: 3 / 5;
				}
			}

			&.layout-14 {
				display: grid;
				grid-template-columns: 25% 25% 25% 25%;
				grid-auto-flow: row dense;

				view:nth-child(1),
				view:nth-child(3),
				view:nth-child(6),
				view:nth-child(8) {
					grid-column: 1 / 3;
				}

				view:nth-child(2),
				view:nth-child(7),
				view:nth-child(9) {
					grid-column: 3 / 5;
				}
			}

			&.layout-15 {
				display: grid;
				grid-template-columns: 25% 25% 25% 25%;
				grid-auto-flow: row dense;

				view:nth-child(1),
				view:nth-child(6),
				view:nth-child(8) {
					grid-column: 1 / 3;
				}

				view:nth-child(2),
				view:nth-child(5),
				view:nth-child(7),
				view:nth-child(9) {
					grid-column: 3 / 5;
				}
			}

			&.layout-16 {
				display: grid;
				grid-template-columns: 25% 25% 25% 25%;
				grid-auto-flow: row dense;

				view:nth-child(1),
				view:nth-child(7) {
					grid-column: 1 / 3;
				}

				view:nth-child(2),
				view:nth-child(8) {
					grid-column: 3 / 5;
				}
			}

			&.layout-17 {
				display: grid;
				grid-template-columns: 25% 25% 25% 25%;
				grid-auto-flow: row dense;

				view:nth-child(1),
				view:nth-child(3) {
					grid-column: 1 / 3;
				}

				view:nth-child(2),
				view:nth-child(4) {
					grid-column: 3 / 5;
				}
			}
		}
	}
</style>
