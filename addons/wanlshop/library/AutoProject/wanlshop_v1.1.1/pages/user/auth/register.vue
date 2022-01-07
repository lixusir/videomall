<template>
	<view>
		<view class="auth">
			<view class="wanl-title">欢迎注册账号</view>
			<form @submit="formSubmit">
				<view class="auth-group radius-bock bg-gray wlian-grey-light">
					<input 
						placeholder="请输入手机号" 
						type="number" maxlength="11" 
						confirm-type="next" 
						placeholder-class="placeholder" 
						name="mobile"
						v-model="mobile"
						@input="onKeyInput"
					></input>
				</view>
				<view class="auth-button flex flex-direction">
					<button class="cu-btn bg-orange sl radius-bock" formType="submit" :disabled="submitDisabled">获取验证码</button>
				</view>
				<!-- 同意服务条款 -->
				<checkbox-group :class="checked == 1 ? 'shake-horizontal' : ''" class="auth-clause" @change="CheckboxChange">
					<checkbox class="orange" :class="checked == 2 ? 'checked' : ''" :checked="checked == 2 ? true : false" value="2" />
					<view>
						我已阅读<text @tap="onDetails($store.state.common.appConfig.user_agreement, '用户协议')">用户协议</text>及<text @tap="onDetails($store.state.common.appConfig.privacy_protection, '隐私保护')">隐私权保护声明</text>
					</view>
				</checkbox-group>
			</form>
		</view>
		<view class="auth-foot">
			<view class="menu text-grey">
				<text @tap="auth">登录</text>
				<text @tap="help">帮助</text>
			</view>
		</view>
	</view>
</template>
<script>
import graceChecker from '@/common/graceChecker';//来自 graceUI 的表单验证， 使用说明见手册 http://grace.hcoder.net/doc/info/73-3.html
export default {
	data() {
		return {
			submitDisabled: true,
			title: '表单验证',
			pageroute: '',
			mobile: '',
			checked: 0
		};
	},
	onLoad(options) {
		this.pageroute = options.url;
		if (options.mobile) {
			this.mobile = options.mobile;
			this.submitDisabled = false;
		}
	},
	methods: {
		CheckboxChange(e) {
			this.checked = e.detail.value;
		},
		onKeyInput: function(e) {
			this.submitDisabled = false
		},
		formSubmit: function(e) {
			if(this.checked == 2){
				//定义表单规则
				var rule = [
					{ name: 'mobile', checkType: 'phoneno', errorMsg: '请输入正确的手机号' }
				];
				//进行表单检查
				var formData = e.detail.value;
				var checkRes = graceChecker.check(formData, rule);
				if (checkRes) {
					this.$api.get({
						url: '/wanlshop/validate/check_mobile_available',
						data: {
							mobile: this.mobile
						},
						success: res => {
							this.$wanlshop.to(`validcode?event=register&mobile=${e.detail.value.mobile}&url=${this.pageroute}`,'slide-in-bottom',200);
						},
						fail: res => {
							uni.showModal({
							    title: '手机号已被注册',
							    content: '点击确定使用手机号登录',
							    success: (msg)=> {
							        if (msg.confirm) {
							           this.$wanlshop.to(`phone?mobile=${e.detail.value.mobile}&url=${this.pageroute}`,'slide-in-bottom',200);
							        } else if (msg.cancel) {
							            console.log('用户点击取消');
							        }
							    }
							});
						}
					});
				} else {
					this.$wanlshop.msg(graceChecker.error);
				}
			}else{
				this.checked = 1;
				setTimeout(() => {
					this.checked = 0;
					this.$wanlshop.msg('请先同意用户协议和隐私保护声明');
				}, 300)
			}
		},
		auth() {
			this.$wanlshop.to(`auth?url=${this.pageroute}`);
		},
		help() {
			this.$wanlshop.to(`/pages/user/help?url=${this.pageroute}`);
		}
	}
};
</script>

<style>
	@import url("auth.css");
</style>
