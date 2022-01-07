<template>
	<view class="wanl-comment">
		<!-- 评论主体-start -->
		<scroll-view
			v-if="commentList.length != 0"
			class="margin-bottom-bj"
			:style="{ height: `${mainHeight}px` }"
			scroll-y
		>
			<view class="flex flex-direction margin-bottom-sm" v-for="(item, index) in commentList" :key="index">
				<image class="comment-avatar round margin-right-bj" :src="oss(item.user.avatar, 88, 88)"></image>
				<view class="flex-sub">
					<!-- 父评论体-start -->
					<view class="flex flex-direction justify-between align-center">
						<view class="flex flex-direction align-center">
							<view class="comment-tag bg-orange flex align-center margin-right-sm" v-if="index == 0">
								<text class="text-xs text-white">沙发</text>
							</view>
							<view class="comment-tag bg-olive flex align-center margin-right-sm" v-if="index == 1">
								<text class="text-xs text-white">板凳</text>
							</view>
							<view class="comment-tag bg-blue flex align-center margin-right-sm" v-if="index == 2">
								<text class="text-xs text-white">地板</text>
							</view>
							<view>
								<text class="text-30" :class="embedded ? 'text-white-6' : 'text-black-6'">{{ item.user.nickname }}</text>
							</view>
						</view>
						<view class="comment-fabulous flex flex-direction align-center" @click="handleLike(index)">
							<text class="text-df margin-right-xs" :class="item.hasLike ? 'text-red' : (embedded ? 'text-white-6' : 'text-black-6')"> {{ item.like }} </text>
							<!-- #ifndef APP-PLUS -->
							<text class="text-df" :class="item.hasLike ? 'text-red wlIcon-dianzan1' : 'text-white-6 wlIcon-dianzan11'"/>
							<!-- #endif -->
							<!-- #ifdef APP-PLUS -->
							<text v-if="embedded" class="icon text-df" :class="item.hasLike ? 'text-red' : 'text-white-6'">{{item.hasLike ? '&#xe6f9;' : '&#xe6fd;'}}</text>
							<text v-else class="text-df" :class="item.hasLike ? 'text-red wlIcon-dianzan1' : 'text-white-6 wlIcon-dianzan11'"/>
							<!-- #endif -->
						</view>
					</view>
					<view class="margin-tb-s">
						<text class="text-sm" :class="embedded ? 'text-white-9' : 'text-black-9'">{{formatHtml(item.content)}}</text>
					</view>
					<view class="flex flex-direction align-center margin-bottom-sm">
						<view class="margin-right-sm">
							<text class="text-sm" :class="embedded ? 'text-white-5' : 'text-black-5'">{{ item.createtime_text }}</text>
						</view>
						<view
							class="margin-right-sm"
							@click="handleReply(item.user.nickname, item.user.nickname, item.id)"
						>
							<text class="text-sm text-blue">回复</text>
						</view>
						<view
							class="margin-right-sm"
							v-if="item.owner"
							@click="handleDelete(index)"
						>
							<text class="text-sm text-red">删除</text>
						</view>
					</view>
					<!-- 父评论体-end -->
					<!-- 子评论列表-start -->
					<view v-if="item.childlist.length > 0" class="comment-chil-dlist padding-lr-sm padding-top-sm margin-bottom-sm radius-bock">
						<view
							class="flex flex-direction"
							v-for="(each, keys) in item.childlist"
							:key="keys"
						>
							<image class="comment-avatar round margin-right-bj" :src="oss(each.user.avatar, 88, 88)"></image>
							<view class="flex-sub">
								<view class="flex flex-direction justify-between align-center">
									<view class="nick-name">
										<text class="text-30" :class="embedded ? 'text-white-6' : 'text-black-6'">{{ each.user.nickname }}</text>
									</view>
									<view class="comment-fabulous flex flex-direction align-center" @click="handleLike(index, keys)">
										<text class="text-df margin-right-xs" :class="each.hasLike ? 'text-red' : (embedded ? 'text-white-6' : 'text-black-6')"> {{ each.like }} </text>
										<!-- #ifndef APP-PLUS -->
										<text class="text-df" :class="each.hasLike ? 'text-red wlIcon-dianzan1' : 'text-black-6 wlIcon-dianzan11'"/>
										<!-- #endif -->
										<!-- #ifdef APP-PLUS -->
										<text v-if="embedded" class="icon text-df" :class="each.hasLike ? 'text-red' : 'text-white-6'">{{each.hasLike ? '&#xe6f9;' : '&#xe6fd;'}}</text>
										<text v-else class="text-df" :class="each.hasLike ? 'text-red wlIcon-dianzan1' : 'text-black-6 wlIcon-dianzan11'"/>
										<!-- #endif -->
									</view>
								</view>
								<view class="margin-tb-s">
									<text class="text-sm" :class="embedded ? 'text-white-9' : 'text-black-9'">{{formatHtml(each.content)}}</text>
								</view>
								<view class="comment-main-foot flex flex-direction align-center margin-bottom">
									<view class="margin-right-sm">
										<text class="text-sm" :class="embedded ? 'text-white-5' : 'text-black-5'">{{ each.createtime_text }}</text>
									</view>
									<view
										class="margin-right-sm"
										@click=" handleReply( item.user.nickname, each.user.nickname, item.id ) "
									>
										<text class="text-sm text-blue">回复</text>
									</view>
									<view
										class="margin-right-sm"
										v-if="each.owner"
										@click="handleDelete(index, keys)"
									>
										<text class="text-sm text-red">删除</text>
									</view>
								</view>
							</view>
						</view>
					</view>
					<!-- 子评论列表-end -->
				</view>
			</view>
		</scroll-view>
		<!-- 评论不存在 -->
		<view
			class="flex align-center justify-center"
			:style="{ height: `${mainHeight}px` }"
			v-else
		>
			<view>
				<image
					class="comment-none-image"
					src="@/static/images/default/network_default3x.png"
				></image>
				<view class="flex-direction text-center justify-center margin-top-bj">
					<text class="text-gray-dark text-sm">还没有任何评论，</text>
					<text class="text-orange text-sm" @click="commentInput">抢沙发！</text>
				</view>
			</view>
		</view>
		<!-- 底部评论条 -->
		<view class="flex flex-direction justify-between align-center" :style="{ height: `${tabbarHeight}px`, paddingBottom: `${tabbarTop}px`}">
			<input
				class="comment-input flex-sub padding-lr-sm"
				v-model="commentReq.content"
				placeholder="有爱评论, 讲点好听的把~"
				placeholder-style="fontSize: 14px; color: #999999;"
				:maxlength="100"
				:focus="focus"
				@blur="handleBlur"
			/>
			<view class="flex comment-button margin-left-bj justify-center align-center text-white" @click="handleAdd">
				<!-- #ifndef APP-PLUS -->
				<text class="wlIcon-shangchuan"></text>
				<!-- #endif -->
				<!-- #ifdef APP-PLUS -->
				<text v-if="embedded" class="icon text-sm">&#xe640;</text>
				<text v-else class="wlIcon-shangchuan"/>
				<!-- #endif -->
			</view>
		</view>
	</view>
</template>

<script>
export default {
	name: 'wanlComment',
	props: {
		findId: {
			type: Number,
			default: 0
		},
		embedded: {
			type: Boolean,
			default: false
		},
		commenCount: {
			type: Number,
			default: 0
		},
		commentList: {
			type: Array,
			default() {
				return [];
			}
		},
		deleteTip: {
			type: String,
			default: '删除后不可找回，子评论将一并删除'
		}
	},
	data() {
		return {
			mainHeight: 0,
			tabbarTop: 0,
			tabbarHeight: 0,
			commentReq: {
				pid: null, // 评论父id
				content: null // 评论内容
			},
			pUser: null, // 标签-回复人
			focus: false // 输入框自动聚焦
		};
	},
	async mounted() {
		let sys = uni.getSystemInfoSync();
		this.mainHeight =  sys.windowHeight / 1.8;
		this.tabbarTop = this.embedded ? 0 : sys.safeAreaInsets.bottom;
		this.tabbarHeight = (this.embedded ? 0 : sys.safeAreaInsets.bottom) + uni.upx2px(70);
	},
	methods: {
		// 回复评论
		handleReply(pUser, reUser, pId) {
			this.pUser = pUser;
			this.commentReq.pid = pId;
			if (reUser) {
				this.commentReq.content = '@' + reUser + ' ';
			} else {
				this.commentReq.content = '';
			}
			this.commentInput();
		},
		// 点赞评论
		async handleLike(index, keys = null) {
			if (this.$store.state.user.isLogin) {
				let comment = keys === null ? this.commentList[index] : this.commentList[index].childlist[keys];
				// 加载网络
				uni.request({
					url: `${this.$store.state.common.appUrl.api}/wanlshop/find/comments/likeData`,
					data: {
						id: comment.id
					},
					header: { 
						'Accept-Language':'zh-CN,zh;q=0.9',
						token: this.$store.state.user.token
					},
					success: res => {
						if(res.data.code === 1){
							comment.hasLike = res.data.data.data;
							if(res.data.data.data){
								comment.like += 1;
							}else{
								comment.like -= 1;
							}
						}else{
							uni.showToast({
							    title: res.data.msg,
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
		// 新增评论
		async handleAdd() {
			if (this.$store.state.user.isLogin) {
				if (this.commentReq.content == null || this.commentReq.content.length < 2) {
					uni.showToast({
						title: '评论内容过短',
						duration: 2000
					});
					return;
				}
				this.commentReq.find_id = this.findId;
				uni.request({
					url: `${this.$store.state.common.appUrl.api}/wanlshop/find/comments/addData`,
					data: this.commentReq,
					header: { 
						'Accept-Language':'zh-CN,zh;q=0.9',
						token: this.$store.state.user.token
					},
					method: 'POST',
					success: res => {
						if(res.data.code === 1){
							uni.showToast({
							    title: '评论成功',
							    icon: 'none'
							});
							this.tagClose();
							this.$emit('complete', {type: 'add', count: this.commenCount + 1});
						}else{
							uni.showToast({
							    title: res.data.msg,
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
		// 删除评论前确认
		async handleDelete(index, keys = null) {
			if (this.$store.state.user.isLogin) {
				uni.showModal({
					content: this.deleteTip,
					confirmText: '确认删除',
					success: (res)=> {
						if (res.confirm) {
							let comment = keys === null ? this.commentList[index] : this.commentList[index].childlist[keys];
							uni.request({
								url: `${this.$store.state.common.appUrl.api}/wanlshop/find/comments/delData`,
								data: {
									id: comment.id,
									find_id: comment.find_id
								},
								header: { 
									'Accept-Language':'zh-CN,zh;q=0.9',
									token: this.$store.state.user.token
								},
								method: 'POST',
								success: res => {
									if(res.data.code === 1){
										let commentList = this.commentList;
										let childlist = this.commentList[index].childlist;
										if(keys === null){
											commentList.splice(index, 1);
										}else{
											childlist.splice(keys, 1);
										}
										this.$emit('complete', {type: 'del', count: res.data.data.count});
									}else{
										uni.showToast({
										    title: res.data.msg,
										    icon: 'none'
										});
									}
								}
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
		// 标签关闭
		tagClose() {
			this.pUser = null;
			this.commentReq.pid = null;
			this.commentReq.content = null;
		},
		// 输入框失去焦点
		handleBlur() {
			this.focus = false;
		},
		// 输入评论
		commentInput() {
			this.focus = true;
			// TODO 调起键盘方法
		},
		// 格式化html
		formatHtml(content) {
			return content.replace(/<\/?.+?>/g, '').replace(/ /g, '');
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
	}
};
</script>

<style>
@import '@/static/style/wanlnvue.css';
.comment-avatar{
    width: 70rpx;
    height: 70rpx;
	background-color: rgba(100,100,100,0.2);
}
.comment-tag{
	padding: 0 10rpx;
	border-radius: 10rpx;
	height: 32rpx;
	overflow: hidden;
}

.comment-none-image {
	height: 360rpx;
	width: 360rpx;
}
.comment-input{
	background-color: #ececec;
	height: 70rpx;
	border-radius: 5000rpx;
	font-size: 14px;
	color: #333333;
}
.comment-button{
	background-color: #02DC6B;
	border-radius: 5000rpx;
	height: 70rpx;
	width: 70rpx;
}
.comment-chil-dlist{
	background-color: rgba(100,100,100,0.05);
}
.comment-fabulous{
	height: 40rpx;
}
</style>