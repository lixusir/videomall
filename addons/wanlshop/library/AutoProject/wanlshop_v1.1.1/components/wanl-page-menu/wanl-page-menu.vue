<template>
	<!-- ok -->
	<view class="wanlpage-menu grid text-center" 
		:class="'col-' + pageData.params.colfive" 
		:style="[pageData.style]"
		> 
		<view class="item" 
			:style="{'margin-top':pageData.params.menuMarginTop}" 
			v-for="(menu, keys) in pageData.data" 
			:key="keys"
			@tap="onLink(menu.link)">
			<view v-if="pageData.params.menuType === 'icon'" class="picicon" 
			:class="menu.bgClass" 
			:style="{
				'height':pageData.params.menuHeightWidth,
				'width':pageData.params.menuHeightWidth,
				'margin-bottom':pageData.params.menuMarginBottom,
				'border-radius':pageData.params.menuBorderRadius
			}">
				<text 
					:class="[menu.icon,menu.iconClass]" 
					:style="{'font-size':pageData.params.menuIconSize}"></text>
			</view>
			
			<view v-else class="picicon"
			:class="menu.bgClass" 
			:style="{
				'margin-bottom':pageData.params.menuMarginBottom
			}">
				<image :src="$wanlshop.oss(menu.iconImage, 100, 0, 1, 'transparent', 'png')" :style="{
					'height':pageData.params.menuHeightWidth,
					'width':pageData.params.menuHeightWidth,
					'border-radius':pageData.params.menuBorderRadius
				}" mode="heightFix"></image>
			</view>
			<view :style="{'font-size':pageData.params.menuTextSize}">
				{{menu.text}}
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		name: "WanlPageMenu",
		props: {
			pageData: {
				type: Object,
				default: function() {
					return {
						name: '菜单组件',
						type: 'menu',
						params: [],
						style: [],
						data: []
					}
				}
			}
		},
		// 1.0.2升级
		methods:{
			async onLink(url){
				this.$wanlshop.on(url);
			}
		}
	}
</script>
<style>
	.wanlpage-menu .picicon {
		display: inline-flex;
		align-items: center;
		justify-content: center;
	}
</style>
