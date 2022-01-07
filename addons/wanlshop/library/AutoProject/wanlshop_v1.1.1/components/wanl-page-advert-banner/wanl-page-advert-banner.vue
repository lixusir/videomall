<template>
	<!-- ok -->
	<view class="wanlpage-advert-banner" :style="[pageData.style]">
		<swiper class="screen-swiper square-dot" :style="{height: pageData.params.height}" :indicator-dots="true" :circular="true" :autoplay="true" :interval="pageData.params.interval" duration="500">
			<swiper-item v-for="(item, index) in advertData" :key="item.id" v-if="item.module == 'page' && item.type == 'banner'" @tap="onAdvert(item)">
				<image :style="{borderRadius: radius}" :src="$wanlshop.oss(item.media, 414, 0, 1, 'transparent', 'png')" mode="aspectFill"></image>
			</swiper-item>
		</swiper>
	</view>
</template>

<script>
	export default {
		name: "WanlPageAdvertBanner",
		props: {
			pageData: {
				type: Object,
				default: function() {
					return {
						name: '',
						type: '',
						params: [],
						style: []
					}
				}
			},
			advertData: {
				type: Array,
				default: () => []
			}
		},
		data(){
			return{
				radius: '0rpx'
			}
		},
		mounted() {
			if(this.pageData.style['border-radius']){
				this.radius = this.pageData.style['border-radius']
			}
		}
	}
</script>
<style lang="scss">
	.screen-swiper{
		min-height: 100rpx !important;
		image{
			overflow: hidden;
		}
	}
</style>
