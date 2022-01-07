<template>
	<view class="wanlfind-add-container">
		<view class="edgeInsetTop"></view>
		<view class="wanlfind-add-container__main">
			<!-- 发布种草 -->
			<view v-if="params.type === 'want'" class="want">
				<view class="padding-bj flex justify-between align-center">
					<view class="text-30">上传图片</view>
				</view>
				<view class="grid col-4 grid-square flex-sub padding-lr-bj">
					<view
						v-if="params.images.length > 0"
						v-for="(item, index) in params.images"
						:key="index"
					>
						<view class="item" @tap="handlePreview(index)">
							<image :src="$wanlshop.oss(item, 100, 100)" mode="widthFix"></image>
						</view>
						<view class="tag" @tap="handleDelImage(index)">
							<text class="wlIcon-31guanbi text-white"></text>
						</view>
					</view>
					<view v-if="params.images.length < 9" @tap="handleImage()">
						<view class="item">
							<view class="text-lg text-gray">
								<text class="wlIcon-tianjia"></text>
								<view v-if="params.images.length === 0" class="margin-top-xs text-sm">添加图片</view>
								<view v-else class="margin-top-xs text-sm">继续添加</view>
							</view>
						</view>
					</view>
				</view>
				<!-- 种草内容 -->
				<view class="padding-bj flex justify-between align-center">
					<view class="text-30">
						<text class="wlIcon-xiugaioryijian margin-right-xs text-bold"></text>
						种草内容
					</view>
				</view>
				<view class="padding-bj bg-gray margin-lr-bj radius-bock">
					<editor
						id="editor"
						@ready="onEditorReady"
						@input="onEditorInput"
						placeholder="编辑种草文案~"
					/>
				</view>
			</view>
			<!-- 发布视频 -->
			<view v-if="params.type === 'video'" class="video">
				<view class="padding-bj flex">
					<view
						class="upload margin-right-bj flex align-center justify-center radius-bock bg-gray"
					>
						<view v-if="video.tempFilePath" @tap="handleVideo">
							<!-- #ifdef APP-PLUS -->
							<video class="radius-bock" :controls="false" :show-center-play-btn="false" :src="video.tempFilePath" object-fit="fill" autoplay muted/>
							<!-- #endif -->
							<!-- #ifndef APP-PLUS -->
							<video class="radius-bock" :controls="false" :show-center-play-btn="false" :src="video.tempFilePath" object-fit="cover" />
							<!-- #endif -->
						</view>
						<block v-else>
							<view v-if="video.progress === 0 && video.progress !== 100" class="text-lg text-gray text-center" @tap="handleVideo">
								<text class="wlIcon-tianjia"></text>
								<view class="margin-top-xs text-sm">添加视频</view>
							</view>
						</block>
						<view v-if="video.progress > 0 && video.progress !== 100" class="loading flex align-center justify-center">
							<view class="text-xxl text-gray text-center">
								<text class="wlIcon-jiazai wlIconfont-spin"></text>
								<view class="margin-top-xs text-min">
									{{$wanlshop.conver(video.totalBytesSent, false)}}/{{$wanlshop.conver(video.totalBytesExpectedToSend)}}
								</view>
							</view>
						</view>
					</view>
					<editor
						id="editor"
						@ready="onEditorReady"
						@input="onEditorInput"
						placeholder="给视频添加标题，提升曝光率获取更多赞赏~"
					/>
				</view>
			</view>
			<!-- 选择商品 -->
			<view class="solid-top">
				<view class="padding-bj flex justify-between align-center" @tap="handleGoods()">
					<view class="text-30">
						<text class="wlIcon-goods margin-right-xs text-bold"></text>
						选择商品
					</view>
					<view class="text-30"><text class="wlIcon-fanhui2"></text></view>
				</view>
				<view class="grid col-4 grid-square flex-sub padding-lr-bj">
					<view v-if="chooseList.length === 0" @tap="handleGoods()">
						<view class="item">
							<view class="text-lg text-gray">
								<text class="wlIcon-tianjia"></text>
								<view class="margin-top-xs text-sm">添加商品</view>
							</view>
						</view>
					</view>
					<view
						v-if="chooseList.length > 0"
						v-for="(item, index) in chooseList"
						:key="index"
						@tap="handleGoods()"
					>
						<view class="item">
							<image
								:src="$wanlshop.oss(item.image, 100, 100)"
								mode="widthFix"
							></image>
						</view>
					</view>
				</view>
			</view>
		</view>
		<!-- 底部 -->
		<view class="safeAreaBottom"></view>
		<view class="wanlfind-add-container__foot">
			<view class="cu-bar tabbar foot flex flex-direction">
				<button class="cu-btn bg-red lg" @tap="handleSubmit">
					<text class="wlIcon-fabu margin-right-sm"></text>
					发布{{ params.type === 'want' ? '种草' : '短视频' }}
				</button>
			</view>
		</view>
		<!-- 模态框 -->
		<view class="wanlfind-add-container__modal">
			<view
				class="cu-modal bottom-modal"
				:class="modalName == 'goods' ? 'show' : ''"
				@tap="handleHideModal"
			>
				<view class="cu-dialog bg-bgcolor" @tap.stop="">
					<view class="wanl-modal">
						<view class="head padding-bj">
							<view class="content"><view class="text-lg">购买过的商品</view></view>
							<view class="close wlIcon-31guanbi" @tap="handleHideModal"></view>
						</view>
						<scroll-view class="scroll-y" scroll-y @scrolltoupper="handleUpper">
							<wanl-empty v-if="orderList.length === 0 && status !== 'loading'" text="还没有购买过任何商品"/>
							<view v-if="orderList.length > 0">
								<view
									class="item bg-white padding-bj margin-bottom-bj radius-bock"
									v-for="(item, index) in orderList"
									:key="index"
									@tap="handleChoose(index)"
								>
									<image
										class="radius margin-right-bj"
										:src="$wanlshop.oss(item.image, 100, 100)"
										mode="aspectFill"
									></image>
									<view class="details margin-right-bj">
										<view class="text-cut-2 text-sm">
											<text>{{ item.title }}</text>
										</view>
										<view class="text-orange">
											<text class="text-price">{{ item.price }}</text>
										</view>
									</view>
									<view class="flex align-center text-xxl">
										<text
											v-if="item.choose"
											class="wlIcon-xuanze-danxuan text-red"
										/>
										<text v-else class="wlIcon-xuanze text-gray" />
									</view>
								</view>
							</view>
							<uni-load-more :status="status" :content-text="contentText" />
						</scroll-view>
						<view class="foot padding-lr-bj">
							<button class="cu-btn bg-red text-bold complete" @tap="handleHideModal">
								完成
							</button>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			modalName: null,
			orderList: [],
			current_page: 1, //当前页码
			last_page: 1, //总页码
			status: 'loading',
			contentText: {
				contentdown: '下拉加载更多',
				contentrefresh: '加载中...',
				contentnomore: ''
			},
			chooseList: [],
			video:{
				tempFilePath: null, // 本地缓存
				progress: 0, //上传进度
				totalBytesSent: 0, //已经上传的数据长度
				totalBytesExpectedToSend: 0, //预期需要上传的数据总长度
			},
			params: {
				type: null,
				content: '',
				goods_ids: [],
				video_id: null,
				images: []
			},
		};
	},
	onLoad(option) {
		this.params.type = option.type;
	},
	methods: {
		// 发布
		handleSubmit() {
			const params = this.params;
			if(params.type === 'video'){
				const video = this.video;
				// 判断视频是否上传完成
				if(video.tempFilePath === null ||  params.video_id === null ){
					this.$wanlshop.msg('请上传视频');
					return;
				}else{
					if(video.progress === 0){
						this.$wanlshop.msg('异常请重开此页面');
						return;
					}
					if(video.progress < 100 || video.totalBytesSent !== video.totalBytesExpectedToSend){
						this.$wanlshop.msg('视频还没有上传完成哦~');
						return;
					}
				}
				if(!params.content){
					this.$wanlshop.msg('请编辑视频标题~');
					return;
				}
			}else if(params.type === 'want'){
				if(params.images.length === 0){
					this.$wanlshop.msg('至少添加一张图片');
					return;
				}
				if(!params.content){
					this.$wanlshop.msg('请编辑种草详情~');
					return;
				}
			}
			if(params.goods_ids.length === 0){
				this.$wanlshop.msg('至少选择一个商品');
				this.handleGoods();
				return;
			}
			this.$api.post({
				url: '/wanlshop/find/find/addData',
				data: params,
				loadingTip: '发布中...',
				success: res => {
					this.$wanlshop.msg('发布成功！');
					this.$wanlshop.to('/pages/page/success?type=find');
				}
			});
		},
		// 选择视频
		handleVideo(e) {
			uni.chooseVideo({
				success: ch => {
					let name = ch.name ? ch.name : `${this.$wanlshop.guid(12)}.mp4`;
					this.$api.get({
						url: '/wanlshop/video/getUploadProof',
						data: {
							name: name,
						},
						success: sts => {
							const uploadTask = uni.uploadFile({
								url: sts.ossUrl, 
								filePath: ch.tempFilePath,
								name: sts.file,
								formData: sts.formData,
								success: upload => {
									if(upload.statusCode === 200){
										this.params.video_id = sts.videoId;
										this.video.tempFilePath = ch.tempFilePath;
									}else{
										this.params.video_id = null;
										this.video.tempFilePath = null;
										this.$wanlshop.msg('视频点播接口异常');
									}
								},
								fail: (res) => {
									console.info(res);
								}
							});
							uploadTask.onProgressUpdate((res) => {
								this.video.progress = res.progress;
								this.video.totalBytesSent = res.totalBytesSent;
								this.video.totalBytesExpectedToSend = res.totalBytesExpectedToSend;
							});
						}
					});
				}
			});
		},
		handleImage() {
			uni.chooseImage({
				crop: {
					quality: 60
				},
				success: res => {
					this.$api.get({
						url: '/wanlshop/common/uploadData',
						success: updata => {
							for (let i = 0; i < res.tempFilePaths.length; i++) {
								// 读取图片宽高
								uni.getImageInfo({
									src: res.tempFilePaths[i],
									success: image => {
										this.$api.upload({
											url: updata.uploadurl,
											filePath: image.path,
											name: 'file',
											formData:
												updata.storage == 'local' ? null : updata.multipart,
											success: data => {
												this.params.images.push(data.url);
											}
										});
									}
								});
							}
						}
					});
				}
			});
		},
		handlePreview(index) {
			let imgArr = [];
			for (let item of this.params.images) {
				imgArr = imgArr.concat(this.$wanlshop.oss(item, 500));
			}
			uni.previewImage({
				urls: imgArr,
				current: imgArr[index],
				indicator: 'number'
			});
		},
		handleDelImage(index) {
			this.params.images.splice(index, 1);
		},
		handleUpper() {
			if (this.last_page === this.current_page) {
				this.status = 'noMore';
			} else {
				this.status = 'loading';
				this.current_page++;
				this.handleGoods('upper');
			}
		},
		handleGoods(type) {
			this.modalName = 'goods';
			if (this.orderList.length > 0 && type !== 'upper') {
				return;
			}
			this.$api.get({
				url: '/wanlshop/order/getBuyList',
				data: {
					page: this.current_page
				},
				success: res => {
					this.last_page = res.last_page; // 总页码
					this.current_page = res.current_page; // 当前页面
					if (res.last_page === res.current_page) {
						this.status = 'noMore';
					} else {
						this.status = 'more';
					}
					if (res.total === 0) {
						this.status = 'noMore';
					}
					this.orderList = type === 'upper' ? this.orderList.concat(res.data) : res.data;
				}
			});
		},
		handleHideModal() {
			this.modalName = null;
		},
		handleChoose(index) {
			if (this.chooseList.length >= 8) {
				this.$wanlshop.msg('最多选择 8 个商品');
			} else {
				let goods = this.orderList[index];
				if (!goods.choose) {
					this.$set(goods, 'choose', true);
				} else {
					goods.choose = false;
				}
				this.formatGoods();
			}
		},
		formatGoods() {
			var list = [];
			var ids = [];
			this.orderList.map(item => {
				if (item.choose) {
					list.push(item);
					ids.push(item.id);
				}
			});
			this.chooseList = list;
			this.params.goods_ids = ids;
		},
		onEditorInput(e) {
			this.params.content = e.detail.html;
		},
		onEditorReady() {
			// #ifdef MP-BAIDU
			this.editorCtx = requireDynamicLib('editorLib').createEditorContext('editorId');
			// #endif

			// #ifdef APP-PLUS || H5 ||MP-WEIXIN
			uni.createSelectorQuery()
				.select('#editor')
				.context(res => {
					this.editorCtx = res.context;
				})
				.exec();
			// #endif
		}
	}
};
</script>

<style lang="scss">
.wanlfind-add-container {
	&__main {
		.video {
			.upload {
				position: relative;
				width: 220rpx;
				height: 300rpx;
				video{
					width: 220rpx;
					height: 300rpx;
				}
				.loading{
					position: absolute;
					top: 0;
					bottom: 0;
					right: 0;
					left: 0;
				}
			}
			#editor {
				flex: 1;
				height: 240rpx;
				min-height: 100rpx;
			}
		}
		.grid {
			position: relative;
			.item {
				position: absolute;
				top: 0;
				bottom: 0;
				left: 0;
				right: 0;
				z-index: 1;
				display: flex;
				justify-content: center;
				align-items: center;
				text-align: center;
				border-radius: 18rpx;
				overflow: hidden;
				background-color: #f0f0f0;
			}
			.tag {
				position: absolute;
				top: 0rpx;
				right: 0rpx;
				z-index: 3;
				width: 40rpx;
				height: 40rpx;
				border-radius: 5500rpx;
				display: flex;
				justify-content: center;
				align-items: center;
				text-align: center;
				overflow: hidden;
				background-color: #e6162c;
			}
		}
	}
	&__foot {
		.tabbar {
			z-index: 10;
			background-color: transparent;
			.cu-btn {
				width: calc(100% - 50rpx);
				font-size: 32rpx;
				height: 86rpx;
			}
		}
	}
	&__modal {
		.scroll-y {
			padding-bottom: 0;
			.item {
				display: flex;
				image {
					width: 120rpx;
					height: 120rpx;
					flex-shrink: 1;
				}
				.details {
					& > view {
						width: 100%;
					}
					flex: 1;
					display: flex;
					flex-wrap: wrap;
					align-content: space-between;
				}
			}
		}
	}
}
</style>
