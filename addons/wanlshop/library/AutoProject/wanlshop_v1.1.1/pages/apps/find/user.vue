<template>
	<view class="wanlgroups-user-container">
		<view class="wanlgroups-user-container__head" :style="{ height: headHeight + 'px' }">
			<view
				class="background"
				v-if="headerOpacity > 0"
				:style="{
					height: headHeight + 'px',
					opacity: headerOpacity,
					backgroundColor: userData.color
				}"
			>
				<view class="bgcolor"></view>
			</view>
			<view
				class="navigater text-white"
				:style="{
					height: headHeight + 'px',
					paddingTop: headTop + 'px'
				}"
			>
				<view class="text-xl">
					<view class="margin-right">
						<text class="wlIcon-fanhui1 text-bold" @tap="handleBack"></text>
					</view>
					<view class="text-cut" style="width: 100rpx;">
						<text
							class="text-lg"
							v-if="headerOpacity > 0"
							:style="{ opacity: headerOpacity }"
						>
							{{ userData.user.nickname }}
						</text>
					</view>
				</view>
				<view class="text-xl">
					<text
						v-if="headerOpacity > 0 && !isMyfind && userData.isFollow === 0"
						:style="{ opacity: headerOpacity }"
						class="margin-right text-sm"
						@tap="handleFollow(userData.shop)"
					>
						+ 关注
					</text>
					<text
						class="wlIcon-fenxiangcopy margin-right-xl text-bold"
						@tap="showModal('share')"
					/>
					<text class="wlIcon-gengduo text-bold" @tap="showModal('menu')" />
				</view>
			</view>
		</view>
		<!-- 主体 -->
		<view
			class="wanlgroups-user-container__main"
			:style="{
				backgroundColor: userData.color,
				backgroundImage: `url(${$wanlshop.oss(
					userData.image,
					0,
					50,
					1,
					'transparent',
					'png'
				)})`
			}"
		>
			<view
				class="head text-white"
				:style="{ paddingTop: headHeight + 'px' }"
				@click="handleTheme"
			>
				<view class="userinfo">
					<view
						class="cu-avatar margin-right-bj round"
						:style="{
							backgroundImage: `url(${$wanlshop.oss(userData.user.avatar, 88, 88)})`
						}"
					/>
					<view>
						<view class="text-xl margin-bottom-xs">{{ userData.user.nickname }}</view>
						<view class="text-xs text-white-agba">
							<text>账户号：</text>
							<text>{{ userData.user_no }}</text>
							<text
								class="wlIcon-fuzhi margin-left-sm"
								@click.stop="$wanlshop.copy(userData.user_no)"
							></text>
						</view>
					</view>
				</view>
				<view class="margin-top-xl text-sm">
					<text>{{ userData.user.bio || '他/她未留下任何个性签名' }}</text>
					<text
						v-if="isMyfind"
						class="wlIcon-jifen margin-left-xs"
						@click.stop="handleUserEdit"
					></text>
				</view>
				<view class="findinfo margin-top">
					<view class="quantity">
						<view
							class="project"
							@click.stop="
								$wanlshop.to(`/pages/apps/find/user/follow?id=${userData.user_no}`)
							"
						>
							<view class="text-lg">
								{{
									$wanlshop.toFormat(
										userData.follow < 0 ? 0 : userData.follow,
										'thousand'
									)
								}}
							</view>
							<view class="text-xs text-white-agba">关注</view>
						</view>
						<view
							class="project"
							@click.stop="
								$wanlshop.to(`/pages/apps/find/user/fans?id=${userData.user_no}`)
							"
						>
							<view class="text-lg">
								{{
									$wanlshop.toFormat(
										userData.fans < 0 ? 0 : userData.fans,
										'thousand'
									)
								}}
							</view>
							<view class="text-xs text-white-agba">粉丝</view>
						</view>
						<view class="project" @click.stop="showModal('praised')">
							<view class="text-lg">
								{{
									$wanlshop.toFormat(
										userData.praised < 0 ? 0 : userData.praised,
										'thousand'
									)
								}}
							</view>
							<view class="text-xs text-white-agba">获赞</view>
						</view>
					</view>
					<view>
						<button
							v-if="isMyfind"
							class="cu-btn round line-white"
							@click.stop="handleUserEdit"
						>
							编辑资料
						</button>
						<block v-else>
							<block v-if="loadingFollow">
								<button
									v-if="userData.isFollow === 0"
									class="cu-btn round line-white"
								>
									<text
										class="wlIcon-jiazai1 wlIconfont-spin margin-right-xs"
									></text>
									关注中..
								</button>
								<button v-else class="cu-btn round line-white">
									<text
										class="wlIcon-jiazai1 wlIconfont-spin margin-right-xs"
									></text>
									取消中..
								</button>
							</block>
							<block v-else>
								<button
									v-if="userData.isFollow === 0"
									class="cu-btn round line-white"
									@click.stop="handleFollow(userData.shop)"
								>
									<text class="wlIcon-tianjia margin-right-xs"></text>
									关注
								</button>
								<button
									v-else
									class="cu-btn round line-white"
									@click.stop="handleFollow(userData.shop)"
								>
									已关注
								</button>
							</block>
						</block>
					</view>
				</view>
				<view
					v-if="userData.shop"
					class="shopinfo margin-top-bj solid-top padding-top-bj"
					@click.stop="onShop(userData.shop.id)"
				>
					<view
						class="cu-avatar margin-right-bj round"
						:style="{
							backgroundImage: `url(${$wanlshop.oss(userData.shop.avatar, 88, 88)})`
						}"
					/>
					<view class="subject">
						<view class="info">
							<view v-if="userData.shop.isself == 1">
								<view class="cu-tag sm line-white radius margin-right-xs">
									自营店
								</view>
								<text class="text-cut" style="width: 50%;">
									{{ userData.shop.shopname }}
								</text>
							</view>
							<view v-else>
								<view
									class="cu-tag sm line-white radius margin-right-xs"
									v-if="userData.shop.state == 0"
								>
									个人店
								</view>
								<view
									class="cu-tag sm line-white radius margin-right-xs"
									v-if="userData.shop.state == 1"
								>
									企业店
								</view>
								<view
									class="cu-tag sm line-white radius margin-right-xs"
									v-if="userData.shop.state == 2"
								>
									旗舰店
								</view>
								<text class="text-cut" style="width: 50%;">
									{{ userData.shop.shopname }}
								</text>
							</view>
						</view>
						<view class="menu">
							<button class="cu-btn wlIcon margin-right-sm line-white round">
								<text class="wlIcon-31dianpu"></text>
							</button>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="wanlgroups-user-container__list bg-white">
			<view class="questionbag" :style="{ top: `${headHeight}px` }">
				<scroll-view scroll-x class="bg-white nav text-center">
					<view
						class="cu-item"
						:class="index === currentId ? 'text-30 text-bold' : 'wanl-gray'"
						v-for="(item, index) in navList"
						:key="index"
						@tap="handleSelect(index)"
					>
						{{ item.type_text }}
					</view>
				</scroll-view>
			</view>
			<view class="main">
				<view v-show="keys === currentId" v-for="(data, keys) in navList" :key="keys">
					<block v-if="isMyfind">
						<view v-if="data.dataList.length === 0 && data.loaded" class="empty">
							<view class="text-center wanl-gray">
								<image
									:src="$wanlshop.appstc('/default/collect_default3x.png')"
								></image>
								<view class="text-30 margin-bottom-bj">
									{{
										keys === 0
											? '你还没有发布任何作品'
											: '你还没有任何喜欢的收藏'
									}}
								</view>
								<button
									v-if="keys === 0"
									class="cu-btn line-black"
									@tap="handlePublish()"
								>
									立即发布
								</button>
								<button
									v-else
									class="cu-btn line-black"
									@click="$wanlshop.to('/pages/apps/find/list')"
								>
									去看看
								</button>
							</view>
						</view>
					</block>
					<block v-else>
						<wanl-empty
							v-if="data.dataList.length === 0 && data.loaded"
							:text="keys === 0 ? '他/她还没有发布过任何作品' : '他/她还没有任何收藏'"
							src="collect_default3x"
						/>
					</block>
					<view class="list" v-if="data.dataList.length > 0">
						<view class="item" v-for="(item, index) in data.dataList" :key="index">
							<view
								v-if="item.state === 'normal'"
								class="examine radius-bock"
								:style="{
									backgroundImage:
										'url(' + $wanlshop.oss(item.images[0], 200, 0) + ')'
								}"
								@tap="onFind(item)"
							>
								<view
									class="cover radius-bock"
									:style="{
										backgroundImage:
											'url(' + $wanlshop.oss(item.images[0], 200, 0) + ')'
									}"
								/>
								<view v-if="item.type === 'video'" class="state padding-sm text-white text-xxxl">
									<text class="wlIcon-video" />
								</view>
							</view>
							<view
								v-else
								class="examine radius-bock"
								@tap="$wanlshop.msg(`内容${stateText[item.state].text}, 无法查看`)"
							>
								<view
									class="cover"
									:style="{
										backgroundImage:
											'url(' + $wanlshop.oss(item.images[0], 200, 0) + ')'
									}"
								/>
								<view class="state flex justify-center align-center">
									<view
										class="text-xxl text-center"
										:style="{ color: stateText[item.state].color }"
									>
										<text :class="stateText[item.state].icon" />
										<view class="text-df">
											{{ stateText[item.state].text }}
										</view>
									</view>
								</view>
							</view>
							<view class="padding-xs  text-bold5">
								<view class="text-cut-2">
									<view
										class="cu-tag sm radius margin-right-xs"
										:class="[typeList[item.type]]"
									>
										{{ item.type_text }}
									</view>
									{{ formatHtml(item.content) }}
								</view>
							</view>
							<view class="flex wanl-gray text-min padding-lr-xs justify-between">
								<view>
									<text>
										{{ $wanlshop.timeFormat(item.createtime, 'mm-dd') }} 发布
									</text>
									<text
										v-if="isMyfind && keys === 0"
										class="text-red margin-left-xs"
										@tap="handleDel(index)"
									>
										删除
									</text>
								</view>
								<view
									:class="{ 'text-red': item.isLike === 1 }"
									@tap="handleLike(index)"
								>
									<text
										:class="[
											item.isLike === 1 ? 'wlIcon-yiguanzhu1' : 'wlIcon-likes'
										]"
									></text>
									<text class="margin-left-xs">{{ item.likes }}</text>
								</view>
							</view>
						</view>
					</view>
					<uni-load-more :status="data.loadingType" :content-text="contentText" />
				</view>
			</view>
		</view>
		<!-- 模态框 -->
		<view class="WANL-MODAL wanlgroups-user-container__modal">
			<view
				class="cu-modal top-modal"
				:class="modalName == 'menu' ? 'show' : ''"
				@tap="hideModal"
			>
				<view class="wanl-modal-menu cu-dialog" @tap.stop="">
					<wanl-direct @change="hideModal" />
				</view>
			</view>
			<!-- 分享 -->
			<view
				class="cu-modal bottom-modal"
				:class="modalName == 'share' ? 'show' : ''"
				@tap="hideModal"
			>
				<view class="cu-dialog" @tap.stop="">
					<wanl-share
						:scrollAnimation="scrollAnimation"
						shareTitle="我发现了一个很好的线上购物商城，这是商城类目"
						shareText="品质好而且很省钱如果自己在上面购买，每年可以省下1%~40%的钱"
						:image="$wanlshop.appstc('/common/logo.png')"
						:href="
							common.appConfig.domain +
								'/pages/apps/find/user?QRtype=user'
						"
						@change="hideModal"
					/>
				</view>
			</view>
			<view
				class="cu-modal praised"
				:class="modalName == 'praised' ? 'show' : ''"
				@tap="hideModal"
			>
				<view class="cu-dialog" @tap.stop="">
					<image
						:src="$wanlshop.appstc('/common/find_fans3x.png')"
						mode="widthFix"
					></image>
					<view class="margin-tb-lg">
						<text class="wanl-gray-dark">
							{{
								`“${userData.user.nickname}”共收获得 ${$wanlshop.toFormat(
									userData.praised < 0 ? 0 : userData.praised,
									'thousand'
								)} 个赞`
							}}
						</text>
					</view>
					<view class="padding-tb-bj solid-top" @tap="hideModal">
						<text class="text-bold">确认</text>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
import { mapState } from 'vuex';
export default {
	data() {
		return {
			headHeight: 0,
			headerOpacity: 0,
			windowHeight: 0,
			headTop: 0,
			isMyfind: false,
			loadingFollow: false,
			modalName: null,
			scrollAnimation: 300,
			userData: {
				user_no: '',
				user: {
					avatar: '',
					nickname: '加载中..'
				},
				follow: 0,
				fans: 0,
				likes: 0,
				praised: 0,
				color: '#e9dacd',
				image: '',
				isFollow: 0
			},
			stateText: {
				publish: { icon: 'wlIcon-jiazai wlIconfont-spin', text: '发布中', color: '#fff' },
				examine: {
					icon: 'wlIcon-shouhoufuwu-zidongpingjia',
					text: '待审核',
					color: '#e6442b'
				},
				hazard: { icon: 'wlIcon-zhuyidapx', text: '内容违规', color: '#e6442b' },
				transcoding: {
					icon: 'wlIcon-jiazai1 wlIconfont-spin',
					text: '转码中',
					color: '#fff'
				},
				screenshot: {
					icon: 'wlIcon-tupian wlIconfont-spin',
					text: '生成封面中',
					color: '#fff'
				}
			},
			currentId: 0,
			typeList: {
				new: 'bg-gradual-yellow',
				live: 'wanl-bg-orange',
				want: 'bg-gradual-green',
				activity: 'bg-gradual-orange',
				show: 'wanl-bg-pink',
				video: 'wanl-bg-pink'
			},
			navList: [
				{
					type: 'works',
					type_text: '作品',
					dataList: [],
					loadingType: 'more',
					current_page: 1
				},
				{
					type: 'likes',
					type_text: '喜欢',
					dataList: [],
					loadingType: 'more',
					current_page: 1
				}
			],
			contentText: {
				contentdown: ' ',
				contentrefresh: '正在疯狂加载中...',
				contentnomore: '我是有底线的'
			}
		};
	},
	computed: {
		...mapState(['common'])
	},
	onLoad(option) {
		// 计算页面尺寸
		let sys = this.$wanlshop.wanlsys();
		this.headTop = sys.top;
		this.headHeight = sys.height;
		this.windowHeight = sys.windowHeight;
		// 加载 Api
		if (option.id) {
			this.loadData(option);
		} else {
			this.loadData({ id: null });
		}
	},
	// 下拉刷新
	onReachBottom() {
		this.loadFindData();
	},
	onPageScroll(e) {
		let tmpY = 170;
		e.scrollTop = e.scrollTop > tmpY ? 170 : e.scrollTop;
		this.headerOpacity = e.scrollTop * (1 / tmpY);
	},
	methods: {
		async loadData(data) {
			this.$api.post({
				url: '/wanlshop/find/user/userInfo',
				data: data,
				success: res => {
					if (res.user_id === this.$store.state.user.id) {
						this.isMyfind = true;
					}
					// this.navList[0].type_text = `作品(${res.works})`;
					// this.navList[1].type_text = `喜欢(${res.likes})`;
					this.userData = res;
					this.loadFindData();
				}
			});
		},
		loadFindData(source) {
			//这里是将订单挂载到tab列表下
			let navItem = this.navList[this.currentId];
			//判断是否最后一页
			if (navItem.loadingType == 'noMore') {
				return;
			}
			//tab切换只有第一次需要加载数据
			if (source === 'tabChange' && navItem.loaded === true) {
				return;
			}
			//防止重复加载
			if (navItem.loadingType === 'loading') {
				return;
			}
			navItem.loadingType = 'loading';
			let type =
				navItem.type === 'works' ? (this.isMyfind ? 'myfind' : navItem.type) : navItem.type;
			// 获取列表
			this.$api.get({
				url: '/wanlshop/find/find/getList',
				data: {
					type: type,
					user_no: this.userData.user_no,
					page: navItem.current_page
				},
				success: res => {
					navItem.current_page = res.current_page; //当前页码
					if (res.last_page === res.current_page) {
						navItem.loadingType = 'noMore';
					} else {
						navItem.loadingType = 'more';
						navItem.current_page++;
					}
					res.data.forEach(item => {
						navItem.dataList.push(item);
					});
					this.$set(navItem, 'loaded', true);
				}
			});
		},
		async handleFollow(shop) {
			if (this.$store.state.user.isLogin) {
				this.loadingFollow = true;
				this.$api.post({
					url: '/wanlshop/find/user/setFindUser',
					data: {
						id: this.userData.user_no,
						type: 'follow'
					},
					success: res => {
						if (shop) {
							let concern = this.$store.state.statistics.dynamic.concern;
							if (res.data === 0) {
								concern -= 1;
							} else {
								concern += 1;
							}
							this.$store.commit('statistics/dynamic', {
								concern: concern
							});
						}
						if (res.data === 1) {
							this.userData.fans += 1;
						} else if (res.data === 0) {
							this.userData.fans -= 1;
						}
						this.userData.isFollow = res.data;
						this.loadingFollow = false;
					}
				});
			} else {
				this.$wanlshop.to('/pages/user/auth/auth');
			}
		},
		async handleLike(index) {
			if (this.$store.state.user.isLogin) {
				let data = this.navList[this.currentId].dataList[index];
				this.$api.post({
					url: '/wanlshop/find/user/setFindUser',
					data: {
						id: data.id,
						type: 'likes'
					},
					success: res => {
						if (res.data === 0) {
							data.likes -= 1;
						} else {
							data.likes += 1;
						}
						data.isLike = res.data;
					}
				});
			} else {
				this.$wanlshop.to('/pages/user/auth/auth');
			}
		},
		async handleDel(index) {
			if (this.$store.state.user.isLogin) {
				uni.showModal({
					content: '确定要删除此作品？',
					success: res => {
						if (res.confirm) {
							let data = this.navList[this.currentId].dataList;
							this.$api.post({
								url: '/wanlshop/find/find/delData',
								data: {
									id: data[index].id
								},
								loadingTip: '删除作品中',
								success: del => {
									data.splice(index, 1);
								}
							});
						}
					}
				});
			} else {
				this.$wanlshop.to('/pages/user/auth/auth');
			}
		},
		async handleTheme() {
			if (this.isMyfind) {
				uni.showActionSheet({
					itemList: ['从主题库选择背景', '从相册上传图片'],
					success: action => {
						if (action.tapIndex === 0) {
							this.$wanlshop.to('/pages/user/theme');
						} else if (action.tapIndex === 1) {
							uni.chooseImage({
								success: res => {
									this.$api.get({
										url: '/wanlshop/common/uploadData',
										success: updata => {
											this.$api.upload({
												url: updata.uploadurl,
												loadingTip: '主题上传中...',
												filePath: res.tempFilePaths[0],
												name: 'file',
												formData:
													updata.storage == 'local'
														? null
														: updata.multipart,
												success: data => {
													this.editTheme({
														url: data.url,
														type: 'upload'
													});
												}
											});
										}
									});
								}
							});
						}
					}
				});
			}
		},
		editTheme(data) {
			this.$api.post({
				url: '/wanlshop/find/user/editTheme',
				loadingTip: '更改主题中...',
				data: data,
				success: res => {
					this.userData.image = res.image;
					this.userData.color = res.color;
				}
			});
		},
		handlePublish() {
			let itemList = [], url = [];
			if(this.userData.shop){
				if(this.userData.isVideo){
					if(this.userData.isLive){
						itemList = ['发布种草', '发布短视频', '商家直播'];
						url = ['/pages/apps/find/user/add?type=want', '/pages/apps/find/user/add?type=video', '/pages/shop/live/live'];
					}else{
						itemList = ['发布种草', '发布短视频'];
						url = ['/pages/apps/find/user/add?type=want', '/pages/apps/find/user/add?type=video'];
					}
				}else{
					if(this.userData.isLive){
						itemList = ['发布种草', '商家直播'];
						url = ['/pages/apps/find/user/add?type=want', '/pages/apps/find/user/add?type=video'];
					}else{
						itemList = ['发布种草'];
						url = ['/pages/apps/find/user/add?type=want'];
					}
				}
			}else{
				if(this.userData.isVideo){
					itemList = ['发布种草', '发布短视频'];
					url = ['/pages/apps/find/user/add?type=want', '/pages/apps/find/user/add?type=video'];
				}else{
					itemList = ['发布种草'];
					url = ['/pages/apps/find/user/add?type=want'];
				}
			}
			uni.showActionSheet({
				itemList: itemList,
				success: res => {
					if (res.tapIndex === 2) {
						// #ifdef APP-PLUS || MP-WEIXIN
						this.$wanlshop.to(url[res.tapIndex]);
						// #endif
						// #ifndef APP-PLUS || MP-WEIXIN
						this.$wanlshop.msg('目前只开放App和微信小程序直播');
						// #endif
					}else{
						this.$wanlshop.to(url[res.tapIndex]);
					}
				}
			});
		},
		// 选择Tag
		handleSelect(index) {
			this.currentId = index;
			this.loadFindData('tabChange');
		},
		handleBack() {
			this.$wanlshop.back(1);
		},
		handleUserEdit() {
			this.$wanlshop.auth('/pages/user/setting/user');
		},
		// 格式化html
		formatHtml(content) {
			return content.replace(/<\/?.+?>/g, '').replace(/ /g, '');
		},
		// 弹出层
		showModal(name) {
			// 滚动下分享
			if (name == 'share' && this.modalName != 'share') {
				setTimeout(() => {
					this.scrollAnimation = 300;
				}, 300);
			}
			this.modalName = name;
		},
		hideModal(name) {
			if (name) {
				this.showModal(name);
			} else {
				this.modalName = null;
			}
		}
	}
};
</script>

<style lang="scss">
page {
	background-color: #ffffff;
}
.wanlgroups-user-container {
	&__head {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		z-index: 9;

		.background {
			position: absolute;
			left: 0;
			right: 0;
			z-index: 1;

			.bgcolor {
				width: 100%;
				height: 100%;
				background-color: rgba(0, 0, 0, 0.4);
			}
		}

		.navigater {
			display: flex;
			justify-content: space-between;
			align-items: center;
			position: absolute;
			left: 0;
			right: 0;
			z-index: 9;
			padding-left: 25rpx;
			padding-right: 25rpx;
			/* #ifdef MP */
			padding-right: 220rpx;
			/* #endif */
			& > view {
				display: flex;
				align-items: center;
			}
		}
	}

	&__main {
		background-size: 100% auto;
		background-repeat: no-repeat;
		background-position-y: center;
		.text-white-agba {
			color: rgba(255, 255, 255, 0.9);
		}
		.head {
			padding: 0 30rpx;
			padding-bottom: 50rpx;
			background-color: rgba(0, 0, 0, 0.4);

			.userinfo {
				display: flex;
				align-items: center;
				.cu-avatar {
					width: 130rpx;
					height: 130rpx;
				}
			}

			.findinfo {
				display: flex;
				align-items: center;
				justify-content: space-between;

				.quantity {
					display: flex;
					.project {
						width: 100rpx;
						margin-right: 10rpx;
						overflow: hidden;
					}
				}
			}

			.shopinfo {
				display: flex;
				align-items: center;
				justify-content: space-between;
				&.solid-top {
					&::after {
						border-top: 1rpx solid rgba(255, 255, 255, 0.92);
					}
				}
				.cu-avatar {
					background-color: rgba(0, 0, 0, 0.1);
					width: 65rpx;
					height: 65rpx;
				}
				.subject {
					flex: 1;
					display: flex;
					align-items: center;
					justify-content: space-between;
					.info {
						& > view {
							display: flex;
							align-items: center;
						}
					}
				}
			}
		}
	}

	&__list {
		margin-top: -30rpx;
		border-radius: 24rpx 24rpx 0 0;
		padding-top: 2rpx;
		.questionbag {
			position: -webkit-sticky;
			position: sticky;
			z-index: 99;
			margin: 0 16rpx;
		}
		.main {
			.empty {
				padding-top: 100rpx;
				display: flex;
				justify-content: center;
				image {
					width: 320rpx;
					height: 320rpx;
				}
			}
			.list {
				display: flex;
				flex-direction: row;
				flex-wrap: wrap;
				padding: 0 16rpx;
				justify-content: space-between;
				.item {
					margin-bottom: 40rpx;
					flex: 0 0 calc((100% - 16rpx) / 2);
					overflow: hidden;
					.examine {
						height: 460rpx;
						position: relative;
						.cover {
							height: 460rpx;
							background-size: cover;
							background-position: center;
							background-repeat: no-repeat;
						}
						.state {
							position: absolute;
							top: 0;
							right: 0;
							left: 0;
							bottom: 0;
							background-color: rgba(0, 0, 0, 0.2);
						}
					}
				}
			}
		}
	}

	&__modal {
		.praised {
			.cu-dialog {
				width: 70%;
				overflow: hidden;
				image {
					height: 100%;
				}
			}
		}
	}
}
</style>
