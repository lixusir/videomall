<!-- 
	版本声明：
	* 由于 WanlLive、WanlChat、WanlPay 以下代码开发难度较大，已将相关代码独立申请著作权，受法律保护！！！
	* 无论你购买任何版本，均不允许复制到第三方直接、间接使用，且也不能以学习为目的参考和借鉴！！
	* 你仅有在 WanlShop 中使用、二次开发权利，否则我们会追究法律责任 
	* 深圳前海万联科技有限公司 @www.i36k.com
-->
<template>
	<view :style="{paddingTop: top + 'px'}">
		<scroll-view 
			class="find-list" 
			:style="{height: height - top + 'px'}"
			scroll-y 
			enable-back-to-top
			enable-flex="true"
			@scrolltoupper="handleUpper" 
			@scrolltolower="handleLower"
		>
			<view class="bg-white margin-top-sm margin-lr-bj radius-bock padding-lr-bj padding-top-bj" v-for="(find, index) in dataList" :key="find.id">
				<view class="flex-direction justify-between align-center">
					<view v-if="find.shop" class="flex-direction align-center">
						<image lazy-load class="shop-avatar round margin-right-sm" :src="oss(find.shop.avatar, 35, 35, 2, 'avatar')"  @tap="handleShop(find.shop.id)"></image>
						<view>
							<view class="flex-direction align-center">
								<view class="margin-right-bj flex-direction align-center" @tap="handleShop(find.shop.id)">
									<!-- <text class="tag text-white text-min">商家</text> -->
									<text class="text-df text-gray-dark">{{find.shop.shopname}}</text>
								</view>
								<!-- <view class="tag" v-if="find.isLive" @tap="handleLive(find.isLive.id)">
									<text class="icon tag-icon">&#xe60e;</text>
									<text class="tag-text">直播中</text>
								</view> -->
							</view>
							<view>
								<text class="text-gray-light text-xs">{{timeToDate(find.createtime)}}</text>
							</view>
						</view>
					</view>
					<view v-else class="flex-direction align-center">
						<image lazy-load class="shop-avatar round margin-right-sm" :src="oss(find.user.avatar, 35, 35, 2, 'avatar')" @tap="handleFollow(index)"></image>
						<view>
							<view class="flex-direction align-center">
								<view class="margin-right-bj" @tap="handleFollow(index)">
									<text class="text-df text-gray-dark">{{find.user.nickname}}</text>
								</view>
							</view>
							<view>
								<text class="text-gray-light text-xs">{{timeToDate(find.createtime)}}</text>
							</view>
						</view>
					</view>
					<!-- 右侧 -->
					<view class="flex-direction align-center">
						<view class="find-button flex-direction align-center justify-center radius-bock padding-lr-sm" v-if="find.isFollow === 1" @tap="handleFollow(index, find.shop)">
							<text class="icon text-df text-black margin-right-xs">&#xe66b;</text>
							<text class="text-df text-black">取关</text>
						</view>
						<view class="find-button flex-direction align-center justify-center radius-bock padding-lr-sm" v-else @tap="handleFollow(index, find.shop)">
							<text class="icon text-df text-black margin-right-xs">&#xe66c;</text>
							<text class="text-df text-black">关注</text>
						</view>
						<view v-if="find.shop" class="find-button-radius margin-left-bj align-center justify-center radius-circular" @tap="handleShop(find.shop.id)">
							<text class="icon text-df text-black">&#xe66f;</text>
						</view>
					</view>
				</view>
				<!-- 详细 -->
				<view class="find-list-item-content margin-tb-bj flex-direction align-center" @tap="handleFind(find)">
					<view class="tag" :class="[typeList[find.type]]">
						<text class="tag-text">{{find.type_text}}</text>
					</view>
					<text class="text-df text-lines-2 text-gray-dark">{{formatHtml(find.content)}}</text>
				</view>
				<!-- 直播类型 -->
				<view v-if="find.type == 'live'" class="flex-direction justify-between" :style="{ width: width + 'px' }" @tap="handleFind(find)">
					<view class="live-cover align-center justify-center position-relative overflow-hidden flex-twice margin-right-s" >
						<view class="live-cover-bg position-center">
							<image :src="oss(find.images[0], 200, 200)" mode="aspectFill" />
						</view>
						<view class="live-cover-main position-center align-center justify-center">
							<view class="live-cover-paly round align-center justify-center">
								<text v-if="find.live.state == 1" class="icon text-white text-xxxl">&#xe700;</text>
								<text v-else class="icon text-white text-xxxl">&#xe6ff;</text>
							</view>
							<view v-if="find.live.state == 1" class="live-cover-people position-absolute flex-direction align-center">
								<view class="tag">
									<text class="icon tag-icon">&#xe60e;</text>
									<text class="tag-text">直播</text>
								</view>
								<text class="text-white text-sm">{{find.live.views}} 在看</text>
							</view>
							<view v-else class="live-cover-people position-absolute flex-direction align-center">
								<view class="tag wanl-bg-grey">
									<text class="icon tag-icon">&#xe60e;</text>
									<text class="tag-text">回放</text>
								</view>
								<text class="text-white text-sm">{{find.live.views}} 在看</text>
							</view>
							<view v-if="find.live.state == 1" class="live-cover-fabulous position-absolute align-center">
								<view class="live-cover-tag round bg-orange justify-center align-center margin-bottom-xs">
									<text class="icon text-lg text-white">&#xe635;</text>
								</view>
								<view class="live-cover-num">
									<text class="text-white text-xs">{{find.live.like}}</text>
								</view>
							</view>
						</view>
					</view>
					<view class="flex-sub" style="width: 0;">
						<view 
							v-if="find.images.length >= 2" 
							class="live-goods-item" 
							style="margin-bottom: 4rpx;" 
							@tap="previewImage(find.images, 1)"
						>
							<image :src="oss(find.images[1], 120, 120)" mode="aspectFill" />
						</view>
						<view 
							v-if="find.images.length >= 3" 
							class="live-goods-item" 
							@tap="previewImage(find.images, 2)"
						>
							<image :src="oss(find.images[2], 120, 120)" mode="aspectFill" />
						</view>
					</view>
				</view>
				<!-- 图片类型 -->
				<view v-else class="overflow-hidden radius flex-wrap flex-direction radius-bock" :style="{width: width + 'px'}">
					<!-- 单个排序 -->
					<view class="find-list-item-container-item"
						v-if="find.images.length == 1"
						:style="{
							width: `${width * 0.66}px`
						}"
						@tap="previewImage(find.images, 0)">
						<image class="radius-bock" lazy-load :src="oss(find.images[0], 180, 180)" :style="{ width: `${width * 0.66}px` }" mode="aspectFill"/>
					</view>
					<!-- 三个排序 -->
					<view class="flex-direction justify-between" 
						v-else-if="find.images.length == 3"
						:style="{ width: width + 'px' }" >
						<view 
							class="live-cover align-center justify-center position-relative overflow-hidden flex-twice margin-right-s"
							@tap="previewImage(find.images, 0)"
						>
							<!-- #ifdef APP-PLUS -->
							<image :style="{ width: width * 0.666 + 'px' }" lazy-load :src="oss(find.images[0], 200, 0)" mode="aspectFill"/>
							<!-- #endif -->
							<!-- #ifndef APP-PLUS -->
							<image lazy-load :src="oss(find.images[0], 200, 0)" mode="aspectFill"/>
							<!-- #endif -->
						</view>
						<view class="flex-sub" style="width: 0;">
							<view 
								v-if="find.images.length >= 2" 
								class="live-goods-item" 
								style="margin-bottom: 4rpx;" 
								@tap="previewImage(find.images, 1)">
								<!-- #ifdef APP-PLUS -->
								<image :style="{ width: width * 0.333 + 'px'}" lazy-load :src="oss(find.images[1], 120, 120)" mode="widthFix" />
								<!-- #endif -->
								<!-- #ifndef APP-PLUS -->
								<image lazy-load :src="oss(find.images[1], 120, 120)" mode="aspectFill" />
								<!-- #endif -->
							</view>
							<view 
								v-if="find.images.length == 3" 
								class="live-goods-item" 
								@tap="previewImage(find.images, 2)">
								<!-- #ifdef APP-PLUS -->
								<image :style="{ width: width * 0.333 + 'px'}" lazy-load :src="oss(find.images[2], 120, 120)" mode="widthFix" />
								<!-- #endif -->
								<!-- #ifndef APP-PLUS -->
								<image lazy-load :src="oss(find.images[2], 120, 120)" mode="aspectFill" />
								<!-- #endif -->
							</view>
						</view>
					</view>
					<!-- 基数排序 -->
					<view class="find-list-item-container-item"
						v-else-if="(find.images.length % 2) == 1"
						v-for="(img, kes) in find.images" 
						:key="kes"
						@tap="previewImage(find.images, kes)"
						:style="{
							width: `${(width - 6) / 3}px`,
							height: `${(width - 6) / 3}px`
						}">
						<!-- #ifdef APP-PLUS -->
						<image :style="{ width: `${(width - 6) / 3}px` }" lazy-load :src="oss(img, 120, 120)" mode="widthFix" />
						<!-- #endif -->
						<!-- #ifndef APP-PLUS -->
						<image lazy-load :src="oss(img, 120, 120)" mode="aspectFill" />
						<!-- #endif -->
					</view>
					<!-- 偶数排序 -->
					<view class="find-list-item-container-item"
						v-else
						v-for="(img, kes) in find.images"
						:key="kes"
						@tap="previewImage(find.images, kes)"
						:style="{
							width: `${(width - 4) / 2}px`,
							height: `${(width - 4) / 2}px`
						}">
						<!-- #ifdef APP-PLUS -->
						<image :style="{ width: `${(width - 4) / 2}px` }" lazy-load :src="oss(img, 180, 180)" mode="aspectFill" />
						<!-- #endif -->
						<!-- #ifndef APP-PLUS -->
						<image lazy-load :src="oss(img, 180, 180)" mode="aspectFill" />
						<!-- #endif -->
					</view>
				</view>
				<!-- 商品滚动 -->
				<scroll-view v-if="find.goods.length > 0" class="find-list-item-goods margin-top-bj flex-direction" enable-flex="true" :style="{width: width + 'px'}"  scroll-x :show-scrollbar="false">
					<view 
						class="find-list-item-goods-item overflow-hidden align-center flex-direction justify-between" 
						v-for="(goods, sub) in find.goods" 
						:key="goods.id" 
						:style="{width: find.goods.length == 1 ? width : (width * 0.7) + 'px'}"
						@tap="handleGoods(goods.id)"
					>
						<image lazy-load class="find-list-item-goods-item-img margin-right-sm" :src="oss(goods.image, 50, 50)"></image>
						<view class="flex-sub" style="width: 0;">
							<text class="find-list-item-goods-item-content-title-text text-df">{{goods.title}}</text>
							<text class="text-sm text-orange">￥{{goods.price}}</text>
						</view>
						<view class="find-list-item-goods-item-icon align-center">
							<text class="icon text-sm text-gray">&#xe63f;</text>
						</view>
					</view>
				</scroll-view>
				<!-- 底部操作 -->
				<view class="padding-top-bj padding-bottom-bj flex-direction justify-between align-center">
					<view class="find-list-item-foot-read" @tap="handleFind(find)">
						<text class="text-gray text-sm" v-if="find.type == 'live'">{{find.live.views}} 阅读</text>
						<text class="text-gray text-sm" v-else>{{find.views}} 阅读</text>
					</view>
					<view class="find-list-item-foot-fabulous flex-direction align-center">
						<view class="flex-direction align-center" @tap="handleLike(index)">
							<text class="icon text-sm margin-right-xs" :class="[find.isLike === 1 ? 'text-orange' : 'text-gray']">&#xe60c;</text>
							<text class="text-sm" :class="[find.isLike === 1 ? 'text-orange' : 'text-gray']">{{find.likes}}</text>
						</view>
						<view v-if="isComment === 'Y'" class="flex-direction align-center margin-left-bj" @tap="handleFind(find, 'comments')">
							<text class="icon text-sm margin-right-xs text-gray">&#xe6fe;</text>
							<text class="text-sm text-gray">{{find.comments}}</text>
						</view>
					</view>
				</view>
				<!-- 最新发布 -->
				<view v-if="find.newGoods > 0" @tap="handleShop(find.shop.id)" class="find-list-item-new align-center padding-tb-bj">
					<view class="flex-direction align-center">
						<text class="text-df text-orange">进店查看 {{find.newGoods}} 件新品</text>
						<text class="icon text-df text-orange margin-left-xs">&#xe63f;</text>
					</view>
				</view>
			</view>
			<uni-load-more :status="status" :content-text="contentText" />
		</scroll-view>
	</view>
</template>

<script>
	// #ifdef H5
	import jssdk from '@/common/libs/jssdk';
	// #endif
	export default {
		name: "wanlFindHeader",
		props: {
			isComment: {
				type: String,
				default: 'N'
			},
			top: {
				type: Number,
				default: 0
			},
			height: {
				type: Number,
				default: 0
			},
			width: {
				type: Number,
				default: 0
			},
			currentItemId: {
				type: String,
				default: ''
			}
		},
		data() {
			return {
				typeList: {
					new: 'wanl-bg-blue',
					live: 'wanl-bg-red',
					want: 'wanl-bg-green',
					activity: 'wanl-bg-redorange',
					show: 'wanl-bg-pink'
				},
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
		watch: {
			currentItemId(newVal, oldVal) {
				if (newVal === 'find') {
					this.loadData()
				}
			}
		},
		mounted() {
			if(this.currentItemId === 'find'){
				this.loadData()
			}
		},
		methods: {
			async loadData(type) {
				let client = '';
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
				// #ifdef APP-PLUS
				client = 'app-wanlshop';
				// #endif
				// #ifdef H5
				if(jssdk.isWechat()){
					client = 'wechat-wanlshop';
				}else{
					client = 'h5-wanlshop';
				}
				// #endif
				// #ifdef MP
				client = 'mp-wanlshop';
				// #endif
				// 加载网络
				uni.request({
					url: `${this.$store.state.common.appUrl.api}/wanlshop/find/find/getList`,
					data: {
						type: 'find',
						page: this.current_page
					},
					header: { 
						'App-Client': client,
						'Accept-Language':'zh-CN,zh;q=0.9',
						token: this.$store.state.user.token
					},
					success: res => {
						if(res.data.code === 1){
							if(type === 'lower'){
								this.dataList = this.dataList.concat(res.data.data.data);
							}else{
								this.dataList = res.data.data.data;
							}
							this.current_page = res.data.data.current_page;  //当前页码
							this.last_page = res.data.data.last_page;  //总页码
							// 判断是否还有数据
							if(res.data.data.current_page === res.data.data.last_page || res.data.data.total === 0){
								this.status = 'noMore';
							}else{
								this.status = 'more';
							}
						}else{
							uni.showToast({
							    title: '服务器繁忙',
							    icon: 'none'
							});
						}
					}
				});
			},
			// 关注 & 取消关注
			async handleFollow(index, shop = null) {
				if (this.$store.state.user.isLogin) {
					var data = this.dataList[index];
					// 后续版本遍历整个find
					uni.request({
						url: `${this.$store.state.common.appUrl.api}/wanlshop/find/user/setFindUser`,
						method: 'POST',
						data: {
							id: data.user_no,
							type: 'follow'
						},
						header: { 
							'Accept-Language':'zh-CN,zh;q=0.9',
							token: this.$store.state.user.token
						},
						success: res => {
							if(res.data.code === 1){
								if(shop){
									let concern = this.$store.state.statistics.dynamic.concern;
									if(res.data.data.data === 0){
										concern -= 1;
									}else{
										concern += 1;
									}
									this.$store.commit('statistics/dynamic', {
										concern: concern,
									});
								}
								data.isFollow = res.data.data.data;
							}else{
								uni.showToast({
								    title: '服务器繁忙',
								    icon: 'none'
								});
							}
						}
					});
				} else {
					uni.navigateTo({
						url: '/pages/user/auth/auth'
					})
				}
				
			},
			// 喜欢 & 取消喜欢
			async handleLike(index) {
				if (this.$store.state.user.isLogin) {
					let data = this.dataList[index];
					// 加载网络
					uni.request({
						url: `${this.$store.state.common.appUrl.api}/wanlshop/find/user/setFindUser`,
						method: 'POST',
						data: {
							id: data.id,
							type: 'likes'
						},
						header: { 
							'Accept-Language':'zh-CN,zh;q=0.9',
							token: this.$store.state.user.token
						},
						success: res => {
							if(res.data.code === 1){
								if(res.data.data.data === 0){
									data.likes -= 1;
								}else{
									data.likes += 1;
								}
								data.isLike = res.data.data.data;
							}else{
								uni.showToast({
								    title: '服务器繁忙',
								    icon: 'none'
								});
							}
						}
					});
				} else {
					uni.navigateTo({
						url: '/pages/user/auth/auth'
					})
				}
				
			},
			handleShop(id){
				uni.navigateTo({
					url: `/pages/shop/index?id=${id}`
				})
			},
			handleLive(id){
				if(this.$store.state.user.isLogin){
					uni.navigateTo({
						url: `/pages/apps/find/details/live?id=${id}`
					})
				}else{
					uni.navigateTo({
						url: '/pages/user/auth/auth'
					})
				}
			},
			handleFind(data, comments = null){
				if (data.type == 'live') {
					this.handleLive(data.live_id);
				}else if(data.type == 'video'){
					uni.navigateTo({
						url: `/pages/apps/find/details/video?id=${data.id}`
					})
				}else{
					if(comments){
						uni.navigateTo({
							url: `/pages/apps/find/details/details?id=${data.id}&afferent=comments`
						})
					}else{
						uni.navigateTo({
							url: `/pages/apps/find/details/details?id=${data.id}`
						})
					}
				}
			},
			handleGoods(id){
				uni.navigateTo({
					url: `/pages/product/goods?id=${id}`
				})
			},
			handleUpper() {
				this.loadData('upper');
			},
			handleLower() {
				this.loadData('lower');
			},
			oss(url, w = 120, h = 120, modenum = 2, type = '', format = 'jpg', resize = 'resize', multiple = 3){
				let ossStc = this.$store.state.common.appUrl.oss;
				let image = '';
				let mode = ["m_lfit","m_mfit","m_fill","m_fixed"];
				// 图像，自适应方向：1，渐进显示：1，转换格式：jpg，图片质量：q_90，图片锐化：50，浓缩图
				let rule = ["?x-oss-process=image", "auto-orient,1", "interlace,1", "format,jpg", "quality,q_90", "sharpen,50", "resize,m_fixed,w_120,h_120"];
				// 转换格式
				if (format == 'png') {
					rule[3] = ["format", "png"].join(",");
				}
				// 判断是否有高度
				if (w == 0) {
					rule[6] = [resize, mode[modenum], `h_${h * multiple}`].join(",");
				}else if(h == 0){
					rule[6] = [resize, mode[modenum], `w_${w * multiple}`].join(",");
				}else{
					rule[6] = [resize, mode[modenum], `w_${w * multiple}`, `h_${h * multiple}`].join(",");
				}
				//当地址不存在
				if (url) {
					if ((/^data:image\//.test(url))) {
						image = url;
					}else if((/^(http|https):\/\/.+/.test(url))){
						rule.pop();
						image = url + rule.join("/");
					}else{
						image = ossStc + url + rule.join("/");
					}
				}else{
					if (type == 'transparent') {
						
					}else if(type == 'avatar'){
						image = ossStc + '/common/mine_def_touxiang_3x.png';
					}else{
						image = ossStc + '/common/img_default3x.png';
					}
				}
				return image;
			},
			//预览图片
			previewImage(image, index) {
				var imgArr = [];
				for (var item of image) {
					imgArr = imgArr.concat(this.oss(item, 500));
				}
				uni.previewImage({
					urls: imgArr,
					current: imgArr[index],
					indicator: 'number'
				});
			},
			// 格式化html
			formatHtml(content){
				return (content.replace(/<\/?.+?>/g,"").replace(/ /g,"")).substring(0, 40) + '...';
			},
			timeToDate(timestamp){
				if (timestamp == null) timestamp = Number(new Date());
				timestamp = parseInt(timestamp);
				// 判断用户输入的时间戳是秒还是毫秒,一般前端js获取的时间戳是毫秒(13位),后端传过来的为秒(10位)
				if (timestamp.toString().length == 10) timestamp *= 1000;
				var timer = (new Date()).getTime() - timestamp;
				timer = parseInt(timer / 1000);
				// 如果小于5分钟,则返回"刚刚",其他以此类推
				let tips = '';
				switch (true) {
					case timer < 300:
						tips = '刚刚';
						break;
					case timer >= 300 && timer < 3600:
						tips = parseInt(timer / 60) + '分钟前';
						break;
					case timer >= 3600 && timer < 86400:
						tips = parseInt(timer / 3600) + '小时前';
						break;
					case timer >= 86400 && timer < 2592000:
						tips = parseInt(timer / 86400) + '天前';
						break;
					default:
						var date = new Date(timestamp);
						var year = date.getFullYear(); 
						var month = date.getMonth()+1 < 10 ?  '0'+(date.getMonth()+1) : (date.getMonth()+1); 
						var date = date.getDate() < 10 ?  '0'+(date.getDate()) : (date.getDate());
						tips = `${year}-${month}-${date}`
				}
				return tips;
			}
		}
	};
</script>

<style>
	@import '@/static/style/wanlnvue.css';
		.shop-avatar{
			background-color: #f5f5f5;
			height: 64rpx;
			width: 64rpx;
		}
		.find-button{
			background-color: #f0f0f0;
			height: 64rpx;
			line-height: 1;
		}
		.find-button-radius{
			background-color: #f0f0f0;
			line-height: 1;
			height: 65rpx;
			width: 65rpx;
		}
	.find-list-item-content{
		/* #ifndef APP-PLUS */
		display: inline;
		/* #endif */
	}
	.find-list-item-new{
		border-top: 1px solid #eee;
	}
		.find-list-item-container-item{
			margin:2rpx;
			/* #ifndef APP-PLUS */
			box-sizing: border-box;
			/* #endif */
			overflow: hidden;
			background-color: #f9f9f9;
		}
			.live-cover{
				background-color: #f9f9f9;
				height: 404rpx;
			}
				.live-cover-bg{
					z-index: 1;
				}
				.live-cover-main{
					z-index: 2;
				}
				.live-cover-paly{
					height: 120rpx;
					width: 120rpx;
					border: 2rpx solid rgba(255,255,255,0.5);
				}
				.live-cover-people{
					bottom: 25rpx;
					left: 25rpx;
				}
				.live-cover-fabulous{
					bottom: 25rpx;
					right: 25rpx;
				}
				.live-cover-tag{
					height: 50rpx;
					width: 50rpx;
				}
				.live-goods-item{
					background-color: #f9f9f9;
					height: 200rpx;
					overflow: hidden;
				}
	
	
	.find-list-item-goods{
		/* #ifndef APP-PLUS */
		white-space: nowrap;
		/* #endif */
		height: 100rpx;
	}	
		.find-list-item-goods-item{
			/* #ifndef APP-PLUS */
			display: inline-flex;
			/* #endif */
			height: 100rpx;
			margin-right: 12rpx;
			border-radius: 18rpx;
			background-color: #f9f9f9;
			width: 100%;
		}
		.find-list-item-goods-item-img{
			height: 100rpx;
			width: 100rpx;
			border-top-left-radius: 18rpx;
			border-bottom-left-radius: 18rpx;
		}
		.find-list-item-goods-item-content-title-text{
			lines: 1;
			overflow: hidden;
			/* #ifndef APP-PLUS */
			white-space: nowrap;
			/* #endif */
			text-overflow: ellipsis;
		}
		.find-list-item-goods-item-icon{
			width: 60rpx;
		}
</style>