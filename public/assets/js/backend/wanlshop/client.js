define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'clipboard'], function ($, undefined, Backend, Table, Form, Clipboard) {
    var Controller = {
		// 客户端配置
        client: function () {
			//绑定复制事件
			var clipboard = new Clipboard('.btn-copy');
			clipboard.on('success', function(e) {
			    layer.msg('复制成功');
			});
			clipboard.on('error', function(e) {
			    layer.msg('复制失败');
			});
            Controller.api.bindevent();
        },
		// 系统设置
		config: function () {
		    //绑定复制事件
		    var clipboard = new Clipboard('.btn-copy');
		    clipboard.on('success', function(e) {
		        layer.msg('复制成功');
		    });
		    clipboard.on('error', function(e) {
		        layer.msg('复制失败');
		    });
		    Controller.api.bindevent();
		},
		// App管理
		app: function () {
		    Controller.api.bindevent();
		},
		// H5管理
        h5: function () {
            Controller.api.bindevent();
        },
		// 微信小程序
		mpweixin: function () {
		    Controller.api.bindevent();
		},
		// 百度小程序
		mpbaidu: function () {
		    Controller.api.bindevent();
		},
		// 头条小程序
		mptoutiao: function () {
		    Controller.api.bindevent();
		},
		// 支付宝小程序
		mpalipay: function () {
		    Controller.api.bindevent();
		},
		// QQ小程序
		mpqq: function () {
		    Controller.api.bindevent();
		},
        api: {
			bindevent: function () {
				Form.api.bindevent($("form[role=form]"), function(data, ret){});
			}
        }
    };
    return Controller;
});