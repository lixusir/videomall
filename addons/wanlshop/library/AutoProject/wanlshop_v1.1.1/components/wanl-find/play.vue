<!-- 
	版本声明：
	* 由于 WanlLive、WanlChat、WanlPay 以下代码开发难度较大，已将相关代码独立申请著作权，受法律保护！！！
	* 无论你购买任何版本，均不允许复制到第三方直接、间接使用，且也不能以学习为目的参考和借鉴！！
	* 你仅有在 WanlShop 中使用、二次开发权利，否则我们会追究法律责任 
	* 深圳前海万联科技有限公司 @www.i36k.com
	* 法律顾问：易法通 @http://www.yifatong.com/
-->
<template>
	<view class="video">
		<view class="empty position-center align-center justify-center" v-if="videoList.length === 0 && status !== 'loading'">
			<view>
				
			</view>
		</view>
		<swiper 
			vertical
			acceleration
			:current="videoIndex" 
			:style="{width: width + 'px', height: height + 'px'}"
			@change="changeCurrent"
			@animationfinish="animationFinish">
			<swiper-item v-for="(item, index) in videoList" :key="index">
				<!-- 视频预渲染 +1 -->
				<block v-if="Math.abs(videoIndex - index) <= 1">
				<!-- <block v-if="videoIndex === index"> -->
					<view @click="handleClicked">
						<!-- 播放界面 -->
						<wanl-find-video 
							ref="wanlVideo"
							:width="width" 
							:height="height"
							:src="item.video.url"
							:play="item.play"
							:videoId="item.id"
							:coverUrl="item.video.cover_url"
							:objectFit="item.video.fit"
							:gDuration="item.video.duration" 
							:initialTime="item.initialTime"
							@pause="pauseVideo"
						/>
						<!-- 组件 -->
						<view class="position-bottom-center" :style="{bottom: `${safeBottom}`}">
							<view class="flex-direction justify-between align-end padding-lr-bj padding-bottom-sm">
								<view class="flex-sub padding-right-bj">
									<view v-if="item.shop" class="left-title" @click.stop="handleShop(item.shop.id)">
										<text class="text-white margin-bottom-s text-lg">@{{item.shop.shopname}}</text>
									</view>
									<view v-if="item.user" class="left-title" @click.stop="handleUser(item.user_no)">
										<text class="text-white margin-bottom-s text-lg">@{{item.user.nickname}}</text>
									</view>
									<view class="left-describe flex-direction">
										<!-- #ifndef APP-PLUS -->
										<!-- <text class="text-orange text-df" @click.stop="handleTag">#标题测试#</text> -->
										<!-- #endif -->
										<text class="text-lines-3 flex-sub text-df text-white-8">{{formatHtml(item.content)}}</text>
									</view>
								</view>
								<view class="main-right align-center">
									<view v-if="item.shop" class="main-right-user position-relative margin-bottom-xs align-center">
										<view class="main-right-image justify-center bg-white round align-center" @click.stop="handleUser(item.user_no)">
											<image class="main-right-img round" :src="oss(item.shop.avatar, 35, 35, 2, 'avatar')"/>
										</view>
										<view v-if="item.isFollow === 0" class="main-right-user-follow bg-red round position-absolute justify-center align-center" @click.stop="handleFollow(item.shop)">
											<text class="text-min icon">&#xe644;</text>
										</view>
										<view v-if="item.isFollow === 1" class="main-right-user-follow bg-red round position-absolute justify-center align-center" @click.stop="handleFollow(item.shop)">
											<text class="text-min icon text-bold">&#xe66c;</text>
										</view>
									</view>
									<view v-if="item.user" class="main-right-user position-relative margin-bottom-xs align-center">
										<view class="main-right-image justify-center bg-white round align-center" @click.stop="handleUser(item.user_no)">
											<image class="main-right-img round" :src="oss(item.user.avatar, 35, 35, 2, 'avatar')"/>
										</view>
										<view v-if="item.isFollow === 0" class="main-right-user-follow bg-red round position-absolute justify-center align-center" @click.stop="handleFollow()">
											<text class="text-min icon">&#xe644;</text>
										</view>
									</view>
									<view v-if="item.content" class="margin-top align-center" @click.stop="handleLike">
										<text :class="{'text-red': item.isLike === 1}" class="icon text-xxxxl ">&#xe635;</text>
										<text class="text-white text-min">{{item.likes}}</text>
									</view>
									<view v-if="isComment === 'Y'" class="margin-top align-center" @click.stop="handleComment">
										<text class="icon text-xxxxl">&#xe68c;</text>
										<text class="text-white text-min">{{item.comments}}</text>
									</view>
									<view v-if="item.content" class="margin-top align-center" @click.stop="handleShare">
										<text class="icon text-xxxxl">&#xe72f;</text>
									</view>
								</view>
							</view>
							<!-- 商品 -->
							<scroll-view 
								class="goods flex-direction" 
								:style="{width: width + 'px'}" 
								scroll-x
								enable-flex="true"
								:show-scrollbar="false">
								<view class="goods-item" :style="{width: width * 0.8 + 'px'}" v-for="(goods, keys) in item.goods" :key="keys">
									<view class="flex-direction">
										<image class="goods-item-img" :src="oss(goods.image, 60, 60)"></image>
										<view class="flex-sub justify-between">
											<view>
												<text class="text-sm text-lines-mp text-black-8">{{goods.title}}</text>
											</view>
											<view class="flex-direction justify-between">
												<view class="goods-item-content-details-price">
													<text class="text-df text-orange">￥{{goods.price}}</text>
												</view>
												<view class="padding-tb-s wanl-bg-orange radius padding-lr-xs align-center flex-direction" @click.stop="handleGoods(goods.id)">
													<text class="icon text-min">&#xe636;</text>
													<text class="text-min text-white margin-left-s">立即购买</text>
												</view>
											</view>
										</view>
									</view>
								</view>
							</scroll-view>
						</view>
					</view>
				</block>
			</swiper-item>
		</swiper>
		<!-- 弹出层 -->
		<uni-popup ref="wanlCommentPopup" type="bottom">
			<view class="comment padding-bj">  
				<view class="flex-direction justify-between align-center margin-bottom-bj">
					<text></text>
					<text class="text-white text-30">全部评论（{{commentData.count}}）</text>
					<text class="icon text-30" @tap="handleCommentClose">&#xe66b;</text>
				</view>
				<wanl-comment
					:embedded="true"
					:findId="commentData.find_id"
					:commentList="commentData.list"
					:commenCount="commentData.count"
					@complete="handleComplete"
				/>
				<view :style="{height: `${bottom}px`}" />
			</view>  
		</uni-popup>
		<!-- 弹出层 -->
		<uni-popup ref="wanlSharePopup" type="bottom">
			<view class="share bg-white padding-bj">  
				<view class="flex-direction justify-between align-center margin-bottom-bj">
					<text></text>
					<text class="text-black text-30">分享到</text>
					<text class="icon text-30 text-black" @tap="handleShareClose">&#xe66b;</text>
				</view>
				<scroll-view class="share-scroll-view flex-direction" scroll-x :show-scrollbar="false" scroll-with-animation>
					<!-- #ifdef MP -->
					<view class="share-scroll-view-item">
						<button class="share-scroll-view-item-button" open-type="share">
							<view class="share-scroll-view-item-button-icon justify-center align-center bg-green">
								<text class="icon text-xxl text-white">&#xe608;</text>
							</view>
							<view class="margin-top-xs">
								<text class="text-sm text-black">分享</text>
							</view>
						</button>
					</view>
					<!-- #endif -->
					<view class="share-scroll-view-item" v-for="(item, index) in providerList" :key="index" v-if="item">
						<button class="share-scroll-view-item-button" @tap="handleOnShare(item)">
							<view class="share-scroll-view-item-button-icon justify-center align-center" :class="item.background">
								<text v-if="item.id === 'weixin'" class="icon text-xxl text-white">&#xe608;</text>
								<text v-if="item.id === 'sinaweibo'" class="icon text-xxl text-white">&#xe6b6;</text>
								<text v-if="item.id === 'qq'" class="icon text-xxl text-white">&#xe60b;</text>
								<text v-if="item.id === 'poster'" class="icon text-xxl text-black">&#xe6c8;</text>
								<text v-if="item.id === 'link'" class="icon text-xxl text-black">&#xe63b;</text>
								<text v-if="item.id === 'report'" class="icon text-xxl text-black">&#xe652;</text>
							</view>
							<view class="margin-top-xs">
								<text class="text-sm text-black">{{ item.name }}</text>
							</view>
						</button>
					</view>
				</scroll-view>
				<view :style="{height: `${bottom}px`}" /> 
			</view>  
		</uni-popup>
	</view>
</template>

<script>
	import wanlFindVideo from '@/components/wanl-find/video';
	// #ifdef H5
	import jssdk from '@/common/libs/jssdk';
	// #endif
	let timer = null
	export default {
		name: "wanlFindPlay",
		components: {
			wanlFindVideo
		},
		props: {
			find_id: {
				type: String,
				default: ''
			},
			isComment: {
				type: String,
				default: 'N'
			},
			height: {
				type: Number,
				default: 0
			},
			width: {
				type: Number,
				default: 0
			},
			bottom: {
				type: Number,
				default: 0
			},
			safeBottom: {
				type: Number,
				default: 0
			}
		},
		data() {
			return {
				preload: 2, //当前页剩余个数换页
				clickNum: 0, //记录点击次数
				videoList: [],
				current_page: 1, //当前页码
				last_page: 1, //总页码
				status: 'loading',
				videoIndex: 0,
				oldVideoIndex: 0,
				commentData: {},
				providerList: []
			};
		},
		mounted() {
			this.loadData();
			// 分享
			uni.getProvider({
				service: 'share',
				success: e => {
					let data = [];
					// #ifdef APP-PLUS
					for (let i = 0; i < e.provider.length; i++) {
						switch (e.provider[i]) {
							case 'weixin':
								data.push(
									{
										name: '微信好友',
										id: 'weixin',
										background: 'bg-green',
										sort: 0
									},
									{
										name: '微信朋友圈',
										id: 'weixin',
										background: 'bg-green',
										type: 'WXSenceTimeline',
										sort: 1
									}
								);
								break;
							case 'sinaweibo':
								data.push({
									name: '新浪微博',
									id: 'sinaweibo',
									background: 'bg-red',
									sort: 2
								});
								break;
							case 'qq':
								data.push({
									name: 'QQ',
									id: 'qq',
									background: 'bg-blue',
									sort: 3
								});
								break;
							default:
								break;
						}
					}
					// #endif
					data.push(
						{
							name: '生成海报',
							id: 'poster',
							background: 'bg-gray',
							sort: 4
						},
						{
							name: '链接',
							id: 'link',
							background: 'bg-gray',
							sort: 5
						}
					);
					if(this.isReport){
						data.push(
							{
								name: '举报',
								id: 'report',
								background: 'bg-gray',
								sort: 6
							}
						);
					}
					this.providerList = data.sort((x, y) => {
						return x.sort - y.sort;
					});
				},
				fail: e => {
					uni.showModal({
						content: '获取分享通道失败',
						showCancel: false
					});
				}
			});
		},
		methods: {
			async loadData(type){
				this.status = 'loading';
				if(type === 'update'){
					if (this.current_page >= this.last_page) {
						this.status = 'noMore';
						return false;
					} else {
						this.current_page += 1;
					}
				}
				// 获取客户端类型
				let client = '';
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
				
				uni.request({
					url: `${this.$store.state.common.appUrl.api}/wanlshop/find/find/getList`,
					data: {
						type: 'video',
						page: this.current_page,
						find_id: this.find_id
					},
					header: { 
						'App-Client': client,
						'Accept-Language':'zh-CN,zh;q=0.9',
						token: this.$store.state.user.token
					},
					success: res => {
						if(res.data.code === 1){
							if(type === 'lower'){
								this.videoList = this.videoList.concat(res.data.data.data);
							}else{
								this.videoList = res.data.data.data;
								if(res.data.data.data.length > 0){
									this.$nextTick(() => {
										this.videoPlay(this.videoIndex)
									})
								}else{
									uni.showToast({
									    title: '系统还未上传任何视频！',
									    icon: 'none'
									});
								}
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
			async handleFollow(shop = null) {
				if (this.$store.state.user.isLogin) {
					var data = this.videoList[this.videoIndex];
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
			async handleLike() {
				if (this.$store.state.user.isLogin) {
					let data = this.videoList[this.videoIndex];
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
			async handleComment(){
				if (this.$store.state.user.isLogin) {
					let data = this.videoList[this.videoIndex];
					// 加载网络
					uni.request({
						url: `${this.$store.state.common.appUrl.api}/wanlshop/find/comments/getList`,
						data: {
							id: data.id
						},
						header: { 
							'Accept-Language':'zh-CN,zh;q=0.9',
							token: this.$store.state.user.token
						},
						success: res => {
							if(res.data.code === 1){
								this.commentData = res.data.data;
								this.commentData.find_id = data.id;
								data.comments = res.data.data.count;
								this.$refs.wanlCommentPopup.open();
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
			handleComplete(e){
				if(e.type === 'add'){
					this.handleCommentClose();
				}
				this.commentData.count = e.count;
				this.videoList[this.videoIndex].comments = e.count;
			},
			handleUser(user_no){
				uni.navigateTo({
					url: `/pages/apps/find/user?id=${user_no}`
				})
			},
			handleShop(id){
				uni.navigateTo({
					url: `/pages/shop/index?id=${id}`
				})
			},
			handleGoods(id){
				uni.navigateTo({
					url: `/pages/product/goods?id=${id}`
				})
			},
			handleCommentClose(){
				this.$refs.wanlCommentPopup.close();
			},
			handleShare(){
				this.$refs.wanlSharePopup.open();
			},
			handleOnShare(e){
				let find = this.videoList[this.videoIndex];
				let href = `${this.$store.state.common.appConfig.domain}/pages/apps/find/details/video?id=${find.id}`;
				let shareType = 0; //shareType 分享类型 0.图文 1.纯文字 2.纯图片 5.小程序
				let shareText = this.formatHtml(find.content);
				let image = find.images[0];
				let shareTitle = '短视频分享';
				if (e.id == 'poster') {
					this.$wanlshop.to('/pages/user/qrcode/qrcode?url='+encodeURIComponent(href));
				} else if (e.id == 'link') {
					this.copy(href);
				} else {
					// 开始分享
					let shareOPtions = {
						provider: e.id,
						scene: e.type && e.type === 'WXSenceTimeline' ? 'WXSenceTimeline' : 'WXSceneSession', //WXSceneSession”分享到聊天界面，“WXSenceTimeline”分享到朋友圈，“WXSceneFavorite”分享到微信收藏     
						type: shareType,
						success: (e) => {
							uni.showModal({
								content: '已分享',
								showCancel:false
							})
						},
						fail: (e) => {
							uni.showModal({
								content: e.errMsg,
								showCancel:false
							})
						}
					}
					switch (shareType){
						case 0:
							shareOPtions.summary = shareText;
							shareOPtions.imageUrl = image;
							shareOPtions.title = shareTitle;
							shareOPtions.href = href;
							break;
						case 1:
							shareOPtions.summary = shareText;
							break;
						case 2:
							shareOPtions.imageUrl = image;
							break;
						case 5:
							var pages = getCurrentPages();  
							var page = (pages[pages.length - 1]).route;  
							shareOPtions.imageUrl = image;
							shareOPtions.title = shareTitle;
							shareOPtions.miniProgram = {
								id: this.$store.state.common.appConfig.mp_weixin_id,
								path: page, //微信小程序ID
								webUrl: href,
								type:0
							};
							break;
						default:
							break;
					}
					// if(shareOPtions.type === 0 && plus.os.name === 'iOS'){//如果是图文分享，且是ios平台，则压缩图片 
					// 	shareOPtions.imageUrl = await this.compress();
					// }
					if(shareOPtions.type === 1 && shareOPtions.provider === 'qq'){//如果是分享文字到qq，则必须加上href和title
						shareOPtions.href = href;
						shareOPtions.title = shareTitle;
					}
					uni.share(shareOPtions);
				}
				this.handleShareClose();
			},
			handleShareClose(){
				this.$refs.wanlSharePopup.close();
			},
			handleTag(){
				console.log('点击标签')
			},
			// 点击视频事件
			handleClicked() {
			    if(timer){
			        clearTimeout(timer)
			    }
			    this.clickNum++
			    timer = setTimeout(() => {
			        if(this.clickNum >= 2){
						this.handleLike()
			        }else{
						this.videoList[this.videoIndex].play = !this.videoList[this.videoIndex].play;
			        }
			        this.clickNum = 0
			    }, 300)
			},
			// 播放当前视频
			videoPlay(index) {
				this.videoList[index].play = !this.videoList[index].play;
			},
			handleVideoPlay(){
				if(this.videoList.length > 0)
				this.$nextTick(() => {
					this.videoList[this.videoIndex].play = true;
				})
			},
			handleVideoPause(){
				for (let item of this.videoList) {
					item.play = false
				}
			},
			// 时间打标
			pauseVideo(val) {
				// 1.0.9临时
				// if (typeof this.videoList[this.oldVideoIndex].initialTime != 'undefined') {
				// 	this.videoList[this.oldVideoIndex].initialTime = val
				// }
			},
			animationFinish(e) {
				//#ifdef APP-PLUS
				this.changeCurrent(e)
				//#endif
			},
			changeCurrent(e) {
				this.videoIndex = e.detail.current;
				this.$nextTick(() => {
					for (let item of this.videoList) {
						item.play = false
					}
					this.videoList[this.videoIndex].play = true
				})
			},
			// 格式化html
			formatHtml(content){
				return (content.replace(/<\/?.+?>/g,"").replace(/ /g,""));
			},
			copy(content){
				// 复制内容，必须字符串，数字需要转换为字符串
				content = typeof content === 'string' ? content : content.toString();
				//#ifndef H5
				uni.setClipboardData({
					data: content,
					success: function() {
						uni.showToast({
						    title: `复制成功 ${content}`,
						    icon: 'none'
						});
					},
					fail:function(){
						uni.showToast({
						    title: `复制失败`,
						    icon: 'none'
						});
					}
				});
				//#endif
				// #ifdef H5
				if (!document.queryCommandSupported('copy')) { //为了兼容有些浏览器 queryCommandSupported 的判断
					msg('浏览器不支持');
				}
				let textarea = document.createElement("textarea")
				textarea.value = content
				textarea.readOnly = "readOnly"
				document.body.appendChild(textarea)
				textarea.select() // 选择对象
				textarea.setSelectionRange(0, content.length) //核心
				let result = document.execCommand("copy") // 执行浏览器复制命令
				if(result){
					uni.showToast({
					    title: `复制成功 ${content}`,
					    icon: 'none'
					});
				}else{
					uni.showToast({
					    title: `复制失败`,
					    icon: 'none'
					});
				}	
				textarea.remove()
				// #endif
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
			}
		},
		watch: {
			// 加载新的视频
			videoIndex(newVal, oldVal) {
				if (this.videoList.length - this.videoIndex - 1 <= this.preload) {
					this.loadData('update')
				}
				this.oldVideoIndex = oldVal
			}
		}
	};
</script>

<style>
	@import '@/static/style/wanlnvue.css';
	
	.share{
		border-top-left-radius: 26rpx;
		border-top-right-radius: 26rpx;
	}
	.share-scroll-view{
		/* #ifndef APP-PLUS */
		white-space: nowrap;
		/* #endif */
	}
	.share-scroll-view-item{
		/* #ifndef APP-PLUS */
		display: inline-flex;
		/* #endif */
		margin-right: 36rpx;
	}
	.share-scroll-view-item-button{
		background-color: rgba(0, 0, 0, 0);
		border: 0;
		padding: 0;
		flex-direction: column;
	}
	.share-scroll-view-item-button-icon{
		width: 100rpx;
		height: 100rpx;
		border-radius: 5000rpx;
	}
	/* #ifndef APP-PLUS */
	button:after{
		border: 0;
	}
	/* #endif */
	
	.video{
		background-color: #000000;
	}
	
	.left-describe{
		/* #ifndef APP-PLUS */
		display: inline;
		/* #endif */
	}
	.main-right{
		width: 98rpx;
	}
		.main-right-user{
			height: 116rpx;
			width: 98rpx;
		}
			.main-right-user-follow{
				width: 40rpx;
				height: 40rpx;
				bottom: 0;
				z-index: 999;
			}
		.main-right-image{
			width: 98rpx;
			height: 98rpx;
		}
			.main-right-img{
				width: 90rpx;
				height: 90rpx;
			}
	.goods {
		/* #ifndef APP-PLUS */
		white-space: nowrap;
		/* #endif */
		height: 174rpx;
	}
	.goods-item {
		/* #ifndef APP-PLUS */
		display: inline-block;
		/* #endif */
		background-color: rgba(255,255,255,.9);
		border-radius: 18rpx;
		margin-left: 14rpx;
		padding: 14rpx;
		height: 154rpx;
	}
		.goods-item-img{
			height: 126rpx;
			width: 126rpx;
			margin-right: 14rpx;
			border-radius: 4rpx;
		}
	.comment{
		background-color: rgba(10,10,10,0.9);
		border-top: 2rpx solid rgba(255,255,255,0.1);
		border-top-left-radius: 26rpx;
		border-top-right-radius: 26rpx;
	}
	
</style>
