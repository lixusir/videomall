<?php
$menu = [
    [
        'name'    => 'wanlshop',
        'title'   => 'B2B2C商城',
        'icon'    => 'fa fa-magic',
		'weigh'	  => '8000',
        'sublist' => [
    		[
    		    'name'    => 'wanlshop/dashboard/index',
    		    'title'   => '控制台',
    		    'icon'    => 'fa fa-dashboard',
    			'weigh'	  => '7000',
    			'remark'  => '商城数据统计',
    			'sublist' => [
    				['name' => 'wanlshop/dashboard/statis', 'ismenu' => 0, 'title' => '获取订单销量销售额统计数据']
    			]
    		],
			[
			    'name'    => 'wanlnav/shop',
			    'title'   => '店铺管理',
			    'icon'    => 'fa fa-archive',
				'weigh'	  => '6000',
			    'sublist' => [
			        ['name' => 'wanlshop/shop', 'title' => '店铺列表', 'weigh' => '906', 'ismenu' => 1, 'remark' => '用于管理商城所有商家', 'sublist' => [
						['name' => 'wanlshop/shop/index', 'title' => '查看'],
						['name' => 'wanlshop/shop/edit', 'title' => '修改'],
						['name' => 'wanlshop/shop/del', 'title' => '删除'],
						['name' => 'wanlshop/shop/multi', 'title' => '批量更新'],
						["name" => "wanlshop/shop/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/shop/restore", "title" => "还原"],
						["name" => "wanlshop/shop/destroy", "title" => "真实删除"]
					]],
			        ['name' => 'wanlshop/auth', 'title' => '新店铺审核', 'weigh' => '905', 'ismenu' => 1, 'remark' => '用于管理申请入驻店铺', 'sublist' => [
						['name' => 'wanlshop/auth/index', 'title' => '查看'],
						['name' => 'wanlshop/auth/edit', 'title' => '修改'],
						['name' => 'wanlshop/auth/del', 'title' => '删除'],
						['name' => 'wanlshop/auth/multi', 'title' => '批量更新'],
						["name" => "wanlshop/auth/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/auth/restore", "title" => "还原"],
						["name" => "wanlshop/auth/destroy", "title" => "真实删除"],
						["name" => "wanlshop/auth/detail", "title" => "查看详情"],
						["name" => "wanlshop/auth/agree", "title" => "同意申请"],
						["name" => "wanlshop/auth/refuse", "title" => "拒绝申请"]
					]],
			        ['name' => 'wanlshop/address', 'title' => '地址管理', 'weigh' => '904', 'ismenu' => 1, 'remark' => '用于监管用户地址', 'sublist' => [
						['name' => 'wanlshop/address/index', 'title' => '查看'],
						['name' => 'wanlshop/address/add', 'title' => '添加'],
						['name' => 'wanlshop/address/edit', 'title' => '修改'],
						['name' => 'wanlshop/address/del', 'title' => '删除'],
						['name' => 'wanlshop/address/multi', 'title' => '批量更新'],
						["name" => "wanlshop/address/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/address/restore", "title" => "还原"],
						["name" => "wanlshop/address/destroy", "title" => "真实删除"]
					]],
			        ['name' => 'wanlshop/service', 'title' => '服务管理', 'weigh' => '903', 'ismenu' => 1, 'remark' => '用于管理店铺服务项目', 'sublist' => [
						['name' => 'wanlshop/service/index', 'title' => '查看'],
						['name' => 'wanlshop/service/add', 'title' => '添加'],
						['name' => 'wanlshop/service/edit', 'title' => '修改'],
						['name' => 'wanlshop/service/del', 'title' => '删除'],
						['name' => 'wanlshop/service/multi', 'title' => '批量更新'],
						["name" => "wanlshop/service/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/service/restore", "title" => "还原"],
						["name" => "wanlshop/service/destroy", "title" => "真实删除"],
						["name" => "wanlshop/service/chat", "title" => "聊天窗口"],
					]],
			        ['name' => 'wanlshop/freight', 'title' => '运费模板', 'weigh' => '902', 'ismenu' => 1, 'sublist' => [
						['name' => 'wanlshop/freight/index', 'title' => '查看'],
						['name' => 'wanlshop/freight/del', 'title' => '删除'],
						['name' => 'wanlshop/freight/multi', 'title' => '批量更新'],
						["name" => "wanlshop/freight/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/freight/restore", "title" => "还原"],
						["name" => "wanlshop/freight/destroy", "title" => "真实删除"]
					]],
					['name' => 'wanlshop/brand', 'title' => '品牌管理', 'weigh' => '901', 'ismenu' => 1, 'sublist' => [
						['name' => 'wanlshop/brand/index', 'title' => '查看'],
						['name' => 'wanlshop/brand/add', 'title' => '添加'],
						['name' => 'wanlshop/brand/edit', 'title' => '修改'],
						['name' => 'wanlshop/brand/del', 'title' => '删除'],
						['name' => 'wanlshop/brand/multi', 'title' => '批量更新'],
						["name" => "wanlshop/brand/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/brand/restore", "title" => "还原"],
						["name" => "wanlshop/brand/destroy", "title" => "真实删除"],
						["name" => "wanlshop/brand/examine", "title" => "审核品牌"]
					]]
			    ]
			],
			[
				'name'    => 'wanlshop/category/goods',
				'title'   => '电商类目',
				'icon'    => 'fa fa-list-ol',
				'weigh'	  => '5900',
				'remark'  => '用于管理类目，快速生成类目和管理类型下属性商品',
				'sublist' => [
					['name' => 'wanlshop/category/goods/index', 'title' => '查看'],
					['name' => 'wanlshop/category/add', 'title' => '添加'],
					['name' => 'wanlshop/category/edit', 'title' => '修改'],
					['name' => 'wanlshop/category/del', 'title' => '删除'],
					['name' => 'wanlshop/category/multi', 'title' => '批量更新'],
					['name' => 'wanlshop/category/create', 'title' => '生成菜单'],
					['name' => 'wanlshop/category/select', 'title' => '选择']
				]
			],
            [
                'name'    => 'wanlnav/order',
                'title'   => '订单监管',
                'icon'    => 'fa fa-first-order',
    			'weigh'	  => '5800',
                'sublist' => [
                    ['name' => 'wanlshop/order', 'title' => '商品订单', 'weigh' => '804', 'ismenu' => 1, 'remark' => '仅用户监管商城订单，具体操作请在商家中心管理', 'sublist' => [
    					['name' => 'wanlshop/order/index', 'title' => '查看'],
    					['name' => 'wanlshop/order/detail', 'title' => '详情'],
    					['name' => 'wanlshop/order/relative', 'title' => '快递查询'],
    					['name' => 'wanlshop/order/add', 'title' => '添加'],
    					['name' => 'wanlshop/order/edit', 'title' => '修改'],
    					['name' => 'wanlshop/order/del', 'title' => '删除'],
    					['name' => 'wanlshop/order/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/order/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/order/restore", "title" => "还原"],
    					["name" => "wanlshop/order/destroy", "title" => "真实删除"]
    				]],
					['name' => 'wanlshop/groups/order', 'title' => '拼团订单', 'weigh' => '803', 'ismenu' => 1, 'remark' => '仅用户监管商城订单，具体操作请在商家中心管理', 'sublist' => [
						['name' => 'wanlshop/groups/order/index', 'title' => '查看'],
						['name' => 'wanlshop/groups/orderDetail', 'title' => '详情'],
						['name' => 'wanlshop/groups/orderRelative', 'title' => '快递查询'],
						['name' => 'wanlshop/groups/orderAdd', 'title' => '添加'],
						['name' => 'wanlshop/groups/orderEdit', 'title' => '修改'],
						['name' => 'wanlshop/groups/orderDel', 'title' => '删除'],
						['name' => 'wanlshop/groups/orderMulti', 'title' => '批量更新'],
						["name" => "wanlshop/groups/orderRecyclebin", "title" => "回收站"],
						["name" => "wanlshop/groups/orderRestore", "title" => "还原"],
						["name" => "wanlshop/groups/orderDestroy", "title" => "真实删除"]
					]],
                    ['name' => 'wanlshop/comment', 'title' => '评论管理', 'weigh' => '802', 'ismenu' => 1, 'remark' => '仅用户监管商城商家评论，具体操作请在商家中心管理', 'sublist' => [
    					['name' => 'wanlshop/comment/index', 'title' => '查看'],
    					['name' => 'wanlshop/comment/detail', 'title' => '详情'],
    					['name' => 'wanlshop/comment/add', 'title' => '添加'],
    					['name' => 'wanlshop/comment/edit', 'title' => '修改'],
    					['name' => 'wanlshop/comment/del', 'title' => '删除'],
    					['name' => 'wanlshop/comment/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/comment/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/comment/restore", "title" => "还原"],
    					["name" => "wanlshop/comment/destroy", "title" => "真实删除"]
    				]],
                    ['name' => 'wanlshop/refund', 'title' => '退款管理', 'weigh' => '801', 'ismenu' => 1, 'remark' => '仅用户监管商城退款，具体操作请在商家中心管理', 'sublist' => [
    					['name' => 'wanlshop/refund/index', 'title' => '查看'],
    					['name' => 'wanlshop/refund/detail', 'title' => '退款详情'],
    					['name' => 'wanlshop/refund/agree', 'title' => '同意退款'],
    					['name' => 'wanlshop/refund/refuse', 'title' => '平台判定拒绝退款'],
    					['name' => 'wanlshop/refund/del', 'title' => '删除'],
    					['name' => 'wanlshop/refund/multi', 'title' => '批量更新']
    				]]
                ]
            ],
            [
                'name'    => 'wanlnav/page',
                'title'   => '装修商城',
                'icon'    => 'fa fa-desktop',
    			'weigh'	  => '5700',
                'sublist' => [
					['name' => 'wanlshop/page/template', 'title' => '模板管理', 'weigh' => '705', 'ismenu' => 1],
                    ['name' => 'wanlshop/page', 'title' => '页面管理', 'weigh' => '704', 'ismenu' => 1, 'remark' => '用于管理客户端和商家自定义页面', 'sublist' => [
    					['name' => 'wanlshop/page/index', 'title' => '查看'],
    					['name' => 'wanlshop/page/add', 'title' => '添加'],
    					['name' => 'wanlshop/page/edit', 'title' => '修改'],
    					['name' => 'wanlshop/page/del', 'title' => '删除'],
    					['name' => 'wanlshop/page/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/page/recyclebin", "title" => "历史页面"],
    					["name" => "wanlshop/page/restore", "title" => "恢复历史"],
    					["name" => "wanlshop/page/destroy", "title" => "真实删除"]
    				]],
                    ['name' => 'wanlshop/page/style', 'title' => '全局样式', 'weigh' => '703', 'ismenu' => 1, 'remark' => '客户端初始风格，也可以在节日、活动时修改'],
    				['name' => 'wanlshop/link', 'title' => '链接管理', 'weigh' => '702', 'ismenu' => 1, 'remark' => '用于自定义页面选择客户端或生成相关链接', 'sublist' => [
    					['name' => 'wanlshop/link/index', 'title' => '查看'],
    					['name' => 'wanlshop/link/add', 'title' => '添加'],
    					['name' => 'wanlshop/link/edit', 'title' => '修改'],
    					['name' => 'wanlshop/link/del', 'title' => '删除'],
    					['name' => 'wanlshop/link/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/link/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/link/restore", "title" => "还原"],
    					["name" => "wanlshop/link/destroy", "title" => "真实删除"],
    					['name' => 'wanlshop/link/select', 'title' => '选择']
    				]],
    				['name' => 'wanlshop/icon', 'title' => '图标管理', 'weigh' => '701', 'ismenu' => 1, 'remark' => '用于自定义页面配置图标', 'sublist' => [
    					['name' => 'wanlshop/icon/index', 'title' => '查看'],
    					['name' => 'wanlshop/icon/add', 'title' => '添加'],
    					['name' => 'wanlshop/icon/edit', 'title' => '修改'],
    					['name' => 'wanlshop/icon/del', 'title' => '删除'],
    					['name' => 'wanlshop/icon/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/icon/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/icon/restore", "title" => "还原"],
    					["name" => "wanlshop/icon/destroy", "title" => "真实删除"],
    					['name' => 'wanlshop/icon/select', 'title' => '选择']
    				]]
                ]
            ],
    		[
    		    'name'    => 'wanlnav/kefu',
    		    'title'   => '客服服务',
    		    'icon'    => 'fa fa-comments',
    			'weigh'	  => '5600',
    		    'sublist' => [
    		        ['name' => 'wanlshop/complaint', 'title' => '投诉管理', 'weigh' => '602', 'ismenu' => 1, 'remark' => '用于管理用户在客户端发起的用户、商品、店铺举报投诉', 'sublist' => [
    					['name' => 'wanlshop/complaint/index', 'title' => '查看'],
    					['name' => 'wanlshop/complaint/detail', 'title' => '详情'],
    					['name' => 'wanlshop/complaint/del', 'title' => '删除'],
    					['name' => 'wanlshop/complaint/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/complaint/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/complaint/restore", "title" => "还原"],
    					["name" => "wanlshop/complaint/destroy", "title" => "真实删除"]
    				]],
    				['name' => 'wanlshop/feedback', 'title' => '意见反馈', 'weigh' => '601', 'ismenu' => 1, 'remark' => '用户管理用户在客户端提供给平台意见反馈', 'sublist' => [
    					['name' => 'wanlshop/feedback/index', 'title' => '查看'],
    					['name' => 'wanlshop/feedback/detail', 'title' => '退款详情'],
    					['name' => 'wanlshop/feedback/del', 'title' => '删除'],
    					['name' => 'wanlshop/feedback/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/feedback/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/feedback/restore", "title" => "还原"],
    					["name" => "wanlshop/feedback/destroy", "title" => "真实删除"]
    				]]
    		    ]
    		],
    		[
    		    'name'    => 'wanlnav/finance',
    		    'title'   => '财务管理',
    		    'icon'    => 'fa fa-paypal',
    			'weigh'	  => '5500',
    		    'sublist' => [
    		        ['name' => 'wanlshop/money', 'title' => '资金账单', 'weigh' => '502', 'ismenu' => 1, 'remark' => '用于查看平台商品交易、充值、提现、退款', 'sublist' => [
    					['name' => 'wanlshop/money/index', 'title' => '查看'],
    					['name' => 'wanlshop/money/detail', 'title' => '详情']
    				]],
    				['name' => 'wanlshop/withdraw', 'title' => '用户提现', 'weigh' => '501', 'ismenu' => 1, 'remark' => '用于管理用户提现审核、同意、拒绝', 'sublist' => [
    					['name' => 'wanlshop/withdraw/index', 'title' => '查看'],
    					['name' => 'wanlshop/withdraw/detail', 'title' => '详情'],
    					['name' => 'wanlshop/withdraw/agree', 'title' => '同意'],
    					['name' => 'wanlshop/withdraw/refuse', 'title' => '拒绝'],
    					['name' => 'wanlshop/withdraw/del', 'title' => '删除'],
    					['name' => 'wanlshop/withdraw/multi', 'title' => '批量更新']
    				]]
    		    ]
    		],
			[
			    'name'    => 'wanlnav/groups',
			    'title'   => '阶梯拼团',
			    'icon'    => 'fa fa-users',
				'weigh'	  => '5400',
			    'sublist' => [
			        ['name' => 'wanlshop/groups', 'title' => '拼团管理', 'weigh' => '403', 'ismenu' => 1, 'remark' => '用于查看商城实时拼团状态', 'sublist' => [
						['name' => 'wanlshop/groups/index', 'title' => '查看'],
						['name' => 'wanlshop/groups/detail', 'title' => '详情'],
						["name" => "wanlshop/groups/select", "title" => "选择"]
					]],
					['name' => 'wanlshop/groups/goods', 'title' => '商品管理', 'weigh' => '402', 'ismenu' => 1, 'sublist' => [
						['name' => 'wanlshop/groups/goods/index', 'title' => '查看'],
						['name' => 'wanlshop/groups/goods/goodsDel', 'title' => '删除'],
						["name" => "wanlshop/groups/goods/goodsRecyclebin", "title" => "回收站"],
						["name" => "wanlshop/groups/goods/goodsRestore", "title" => "还原"],
						["name" => "wanlshop/groups/goods/goodsDestroy", "title" => "真实删除"]
					]],
					['name' => 'wanlshop/groups/orders', 'title' => '订单管理', 'weigh' => '401', 'ismenu' => 0, 'url' => 'wanlshop/groups/order']
			    ]
			],
			[
			    'name'    => 'wanlnav/find',
			    'title'   => '种草短视频',
			    'icon'    => 'fa fa-video-camera',
				'weigh'	  => '5300',
			    'sublist' => [
			        ['name' => 'wanlshop/find', 'title' => '作品列表', 'weigh' => '304', 'ismenu' => 1, 'remark' => '用于管理直播种草短视频新品等相关数据', 'sublist' => [
						['name' => 'wanlshop/find/index', 'title' => '查看'],
						['name' => 'wanlshop/find/detail', 'title' => '作品详情'],
						['name' => 'wanlshop/find/examine', 'title' => '审核作品'],
						['name' => 'wanlshop/find/del', 'title' => '删除'],
						['name' => 'wanlshop/find/multi', 'title' => '批量更新'],
						["name" => "wanlshop/find/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/find/restore", "title" => "还原"],
						["name" => "wanlshop/find/destroy", "title" => "真实删除"],
						["name" => "wanlshop/find/select", "title" => "选择"]
					]],
					['name' => 'wanlshop/comments', 'title' => '社交评论', 'weigh' => '303', 'ismenu' => 1, 'remark' => '用于监管查看全部社交评论动态', 'sublist' => [
						['name' => 'wanlshop/comments/index', 'title' => '查看'],
						['name' => 'wanlshop/comments/detail', 'title' => '评论列表'],
						['name' => 'wanlshop/comments/edit', 'title' => '修改'],
						['name' => 'wanlshop/comments/del', 'title' => '删除'],
						['name' => 'wanlshop/comments/multi', 'title' => '批量更新'],
						["name" => "wanlshop/comments/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/comments/restore", "title" => "还原"],
						["name" => "wanlshop/comments/destroy", "title" => "真实删除"],
						["name" => "wanlshop/comments/select", "title" => "选择"]
					]],
					['name' => 'wanlshop/live', 'title' => '直播回放', 'weigh' => '302', 'ismenu' => 1, 'remark' => '用于监管用户当前正在直播或回放数据', 'sublist' => [
						['name' => 'wanlshop/live/index', 'title' => '查看'],
						['name' => 'wanlshop/live/detail', 'title' => '作品详情'],
						['name' => 'wanlshop/live/edit', 'title' => '修改'],
						['name' => 'wanlshop/live/del', 'title' => '删除'],
						['name' => 'wanlshop/live/multi', 'title' => '批量更新'],
						["name" => "wanlshop/live/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/live/restore", "title" => "还原"],
						["name" => "wanlshop/live/destroy", "title" => "真实删除"],
						["name" => "wanlshop/live/select", "title" => "选择"]
					]],
					['name' => 'wanlshop/video', 'title' => '视频点播', 'weigh' => '301', 'ismenu' => 1, 'remark' => '用于监管用户智能审核完成后的视频点播数据', 'sublist' => [
						['name' => 'wanlshop/video/index', 'title' => '查看'],
						['name' => 'wanlshop/video/detail', 'title' => '作品详情'],
						['name' => 'wanlshop/video/del', 'title' => '删除'],
						['name' => 'wanlshop/video/multi', 'title' => '批量更新'],
						["name" => "wanlshop/video/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/video/restore", "title" => "还原"],
						["name" => "wanlshop/video/destroy", "title" => "真实删除"],
						["name" => "wanlshop/video/select", "title" => "选择"]
					]],
			        
			    ]
			],
    		[
    		    'name'    => 'wanlnav/article',
    		    'title'   => '内容管理',
    		    'icon'    => 'fa fa-pencil-square',
    			'weigh'	  => '5200',
    		    'sublist' => [
    		        ['name' => 'wanlshop/article', 'title' => '文章列表', 'weigh' => '202', 'ismenu' => 1, 'remark' => '用于管理客户端的新闻、协议、帮助、及智能客服相关解答', 'sublist' => [
    					['name' => 'wanlshop/article/index', 'title' => '查看'],
    					['name' => 'wanlshop/article/add', 'title' => '添加'],
    					['name' => 'wanlshop/article/edit', 'title' => '修改'],
    					['name' => 'wanlshop/article/del', 'title' => '删除'],
    					['name' => 'wanlshop/article/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/article/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/article/restore", "title" => "还原"],
    					["name" => "wanlshop/article/destroy", "title" => "真实删除"],
    					["name" => "wanlshop/article/select", "title" => "选择"]
    				]],
    		        ['name' => 'wanlshop/category/article', 'title' => '分类管理', 'weigh' => '201', 'ismenu' => 1, 'remark' => '用于管理客户端文章类目']
    		    ]
    		],
			[
			    'name'    => 'wanlnav/wechat',
			    'title'   => '微信管理',
			    'icon'    => 'fa fa-weixin',
				'weigh'	  => '5100',
			    'sublist' => [
			        ['name' => 'wanlshop/wechat/menu', 'title' => '菜单管理', 'weigh' => '202', 'ismenu' => 1, 'sublist' => [
						['name' => 'wanlshop/wechat/menu/index', 'title' => '查看'],
						['name' => 'wanlshop/wechat/menu/add', 'title' => '添加'],
						['name' => 'wanlshop/wechat/menu/edit', 'title' => '修改'],
						['name' => 'wanlshop/wechat/menu/del', 'title' => '删除'],
						['name' => 'wanlshop/wechat/menu/remote', 'title' => '加载远程菜单'],
						['name' => 'wanlshop/wechat/menu/sync', 'title' => '同步'],
						['name' => 'wanlshop/wechat/menu/multi', 'title' => '批量更新'],
					]],
					['name' => 'wanlshop/wechat/response', 'title' => '资源管理', 'weigh' => '201', 'ismenu' => 1, 'sublist' => [
						['name' => 'wanlshop/wechat/response/index', 'title' => '查看'],
						['name' => 'wanlshop/wechat/response/add', 'title' => '添加'],
						['name' => 'wanlshop/wechat/response/edit', 'title' => '修改'],
						['name' => 'wanlshop/wechat/response/del', 'title' => '删除'],
						['name' => 'wanlshop/wechat/response/select', 'title' => '选择'],
						['name' => 'wanlshop/wechat/response/multi', 'title' => '批量更新']
					]]
			    ]
			],
            [
                'name'    => 'wanlnav/client',
                'title'   => 'UNI-APP客户端',
                'icon'    => 'fa fa-cloud',
    			'weigh'	  => '5000',
                'sublist' => [
    				['name' => 'wanlshop/client/edit', 'title' => '全局修改配置', 'weigh' => '5101'],
    				['name' => 'wanlshop/client/download', 'title' => '打包下载', 'weigh' => '109'],
    				['name' => 'wanlshop/client/client', 'title' => '客户端配置', 'weigh' => '108', 'ismenu' => 1, 'remark' => '用于客户端（应用）全局配置'],
                    ['name' => 'wanlshop/client/app', 'title' => 'App管理', 'weigh' => '107', 'ismenu' => 1, 'remark' => '用于App客户端参数配置和本地一键打包安卓 & 苹果应用'],
                    ['name' => 'wanlshop/client/h5', 'title' => 'H5管理', 'weigh' => '106', 'ismenu' => 1, 'remark' => '用于H5客户端参数配置和一键本地打包H5客户端'],
                    ['name' => 'wanlshop/client/mpweixin', 'title' => '微信小程序', 'weigh' => '105', 'ismenu' => 1, 'remark' => '用于微信小程序参数配置和一键本地打包微信小程序'],
                    ['name' => 'wanlshop/client/mpbaidu', 'title' => '百度小程序', 'weigh' => '104', 'ismenu' => 1, 'remark' => '用于百度小程序参数配置和一键本地打包百度小程序'],
                    ['name' => 'wanlshop/client/mptoutiao', 'title' => '字节跳动小程序', 'weigh' => '103', 'ismenu' => 1, 'remark' => '用于字节跳动小程序参数配置和一键本地打包字节跳动小程序'],
                    ['name' => 'wanlshop/client/mpalipay', 'title' => '支付宝小程序', 'weigh' => '102', 'ismenu' => 1, 'remark' => '用于支付宝小程序参数配置和一键本地打包支付宝小程序'],
                    ['name' => 'wanlshop/client/mpqq', 'title' => 'QQ小程序', 'weigh' => '101', 'ismenu' => 1, 'remark' => '用于QQ小程序参数配置和一键本地打包QQ小程序']
                ]
            ],
            [
                'name'    => 'wanlnav/config',
                'title'   => '商城配置',
                'icon'    => 'fa fa-cog',
    			'weigh'	  => '4900',
                'sublist' => [
                    ['name' => 'wanlshop/client/config', 'title' => '系统设置', 'weigh' => '6', 'ismenu' => 1, 'remark' => '用于电商系统核心配置'],
                    ['name' => 'wanlshop/advert', 'title' => '广告管理', 'weigh' => '5', 'ismenu' => 1, 'remark' => '用于广告系统在线管理', 'sublist' => [
    					['name' => 'wanlshop/advert/index', 'title' => '查看'],
    					['name' => 'wanlshop/advert/add', 'title' => '添加'],
    					['name' => 'wanlshop/advert/edit', 'title' => '修改'],
    					['name' => 'wanlshop/advert/del', 'title' => '删除'],
    					['name' => 'wanlshop/advert/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/advert/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/advert/restore", "title" => "还原"],
    					["name" => "wanlshop/advert/destroy", "title" => "真实删除"],
    					["name" => "wanlshop/advert/select", "title" => "选择"]
    				]],
					['name' => 'wanlshop/version', 'title' => 'App热更新', 'weigh' => '4', 'ismenu' => 1, 'remark' => '仅用于App客户端在线升级配置', 'sublist' => [
						['name' => 'wanlshop/version/index', 'title' => '查看'],
						['name' => 'wanlshop/version/add', 'title' => '添加'],
						['name' => 'wanlshop/version/edit', 'title' => '修改'],
						['name' => 'wanlshop/version/del', 'title' => '删除'],
						['name' => 'wanlshop/version/multi', 'title' => '批量更新']
					]],
					['name' => 'wanlshop/theme', 'title' => '用户封面模板', 'weigh' => '3', 'ismenu' => 1, 'remark' => '用于管理客户端用户创作中心系统封面模板', 'sublist' => [
						['name' => 'wanlshop/theme/index', 'title' => '查看'],
						['name' => 'wanlshop/theme/add', 'title' => '添加'],
						['name' => 'wanlshop/theme/edit', 'title' => '修改'],
						['name' => 'wanlshop/theme/del', 'title' => '删除'],
						['name' => 'wanlshop/theme/multi', 'title' => '批量更新'],
						["name" => "wanlshop/theme/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/theme/restore", "title" => "还原"],
						["name" => "wanlshop/theme/destroy", "title" => "真实删除"]
					]],
                    ['name' => 'wanlshop/search', 'title' => '搜索管理', 'weigh' => '2', 'ismenu' => 1, 'remark' => '用于查看用户搜索记录和配置热门搜索置顶关键字', 'sublist' => [
    					['name' => 'wanlshop/search/index', 'title' => '查看'],
    					['name' => 'wanlshop/search/add', 'title' => '添加'],
    					['name' => 'wanlshop/search/edit', 'title' => '修改'],
    					['name' => 'wanlshop/search/del', 'title' => '删除'],
    					['name' => 'wanlshop/search/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/search/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/search/restore", "title" => "还原"],
    					["name" => "wanlshop/search/destroy", "title" => "真实删除"]
    				]],
                    ['name' => 'wanlshop/qrcode', 'title' => '配置二维码', 'weigh' => '1', 'ismenu' => 1, 'remark' => '用于二维码海报生成配置', 'sublist' => [
    					['name' => 'wanlshop/qrcode/index', 'title' => '查看'],
    					['name' => 'wanlshop/qrcode/add', 'title' => '添加'],
    					['name' => 'wanlshop/qrcode/edit', 'title' => '修改'],
    					['name' => 'wanlshop/qrcode/del', 'title' => '删除'],
    					['name' => 'wanlshop/qrcode/multi', 'title' => '批量更新'],
    					["name" => "wanlshop/qrcode/recyclebin", "title" => "回收站"],
    					["name" => "wanlshop/qrcode/restore", "title" => "还原"],
    					["name" => "wanlshop/qrcode/destroy", "title" => "真实删除"]
    				]]
                ]
            ],
			[
			    'name'    => 'wanlnav/hide',
			    'title'   => '不显示菜单',
			    'icon'    => 'fa fa-circle-o',
				'weigh'	  => '4800',
				'ismenu' => 0,
			    'sublist' => [
					['name' => 'wanlshop/service/lists', 'title' => '聊天列表', 'ismenu' => 0],
					['name' => 'wanlshop/service/bind', 'title' => '绑定', 'ismenu' => 0],
					['name' => 'wanlshop/service/history', 'title' => '查询历史记录', 'ismenu' => 0],
					['name' => 'wanlshop/service/read', 'title' => '全部已读', 'ismenu' => 0],
					['name' => 'wanlshop/service/send', 'title' => '发送', 'ismenu' => 0],
					['name' => 'wanlshop/service/close', 'title' => '关闭聊天窗口', 'ismenu' => 0],
					['name' => 'wanlshop/client/update', 'title' => '更新检测服务', 'ismenu' => 0],
					['name' => 'wanlshop/page/qrcode', 'title' => '二维码生成工具', 'ismenu' => 0],
					['name' => 'wanlshop/page/history', 'title' => '云端历史数据', 'ismenu' => 0],
					['name' => 'wanlshop/goods', 'title' => '商品列表', 'ismenu' => 0, 'sublist' => [
						['name' => 'wanlshop/goods/index', 'title' => '查看'],
						['name' => 'wanlshop/goods/edit', 'title' => '修改'],
						['name' => 'wanlshop/goods/del', 'title' => '删除'],
						['name' => 'wanlshop/goods/multi', 'title' => '批量更新'],
						["name" => "wanlshop/goods/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/goods/restore", "title" => "还原"],
						["name" => "wanlshop/goods/destroy", "title" => "真实删除"],
						['name' => 'wanlshop/goods/select', 'title' => '选择']
					]],
					['name' => 'wanlshop/attribute', 'title' => '类目属性', 'ismenu' => 0, 'sublist' => [
						['name' => 'wanlshop/attribute/index', 'title' => '查看'],
						['name' => 'wanlshop/attribute/add', 'title' => '添加'],
						['name' => 'wanlshop/attribute/edit', 'title' => '修改'],
						['name' => 'wanlshop/attribute/del', 'title' => '删除'],
						['name' => 'wanlshop/attribute/multi', 'title' => '批量更新'],
						["name" => "wanlshop/attribute/recyclebin", "title" => "回收站"],
						["name" => "wanlshop/attribute/restore", "title" => "还原"],
						["name" => "wanlshop/attribute/destroy", "title" => "真实删除"]
					]],
					['name' => 'wanlshop/shopsort', 'title' => '商家类目管理', 'ismenu' => 0, 'sublist' => [
						['name' => 'wanlshop/shopsort/index', 'title' => '查看'],
						['name' => 'wanlshop/shopsort/add', 'title' => '添加'],
						['name' => 'wanlshop/shopsort/edit', 'title' => '修改'],
						['name' => 'wanlshop/shopsort/del', 'title' => '删除'],
						['name' => 'wanlshop/shopsort/multi', 'title' => '批量更新']
					]]
			    ]
			],
        ]
    ]
];
return $menu;
