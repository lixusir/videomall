<?php
namespace addons\wanlshop\library\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;

use Workerman\Worker;
use Workerman\Lib\Timer;
use addons\wanlshop\library\WanlChat\WanlChat;

// 自动加载类
require_once ADDON_PATH . 'wanlshop' . DS . 'library' . DS . 'GatewayWorker' . DS . 'vendor' . DS . 'autoload.php';


class Order extends Command
{
    protected function configure()
    {
        $this->setName('wanlshop:order')
			->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
			->addOption('daemon', 'd', Option::VALUE_NONE, 'Run the workerman server in daemon mode.')
			->setDescription('Wanlshop order refund planning task');
    }

    protected function execute(Input $input, Output $output)
    {
    	$action = $input->getArgument('action');
    	if (DIRECTORY_SEPARATOR !== '\\') {
    	    if (!in_array($action, ['start', 'stop', 'reload', 'restart', 'status', 'connections'])) {
    	        $output->writeln("<error>Invalid argument action:{$action}, Expected start|stop|restart|reload|status|connections .</error>");
    	        return false;
    	    }
    	    global $argv;
    	    array_shift($argv);
    	    array_shift($argv);
    	    array_unshift($argv, 'think', $action);
    	} elseif ('start' != $action) {
    	    $output->writeln("<error>Not Support action:{$action} on Windows.</error>");
    	    return false;
    	}
		
		if ('start' == $action) {
		    $output->writeln('Starting GatewayWorker server...');
		}
		
		// 启动计划任务
		$this->plan();
    	Worker::runAll();
    }
    
	
	// 初始化 计划任务 进程
	public function plan()
	{
		// 全局静态属性
		if($this->input->hasOption('daemon')){
			// 以daemon(守护进程)方式运行
			Worker::$daemonize = true;
		}
		Worker::$pidFile = '/var/run/wanlorder.pid';
		$worker = new Worker();
		$worker->count = 1; 
		$worker->onWorkerStart = function($worker)
		{
			echo "\r\n";
			echo "WanlShop 计划任务 启动成功";
			echo "\r\n";
			// 拼团任务
			Timer::add(30, array($this, 'groups'));
			// 优惠券任务
			Timer::add(40, array($this, 'coupon'));
			// 订单任务
			Timer::add(50, array($this, 'order'));
			// 退款任务
			Timer::add(60, array($this, 'refund'));
		};
	}
	
	/**
	 * 实时优惠券（方法内使用）
	 *
	 * @ApiSummary  (WanlShop 实时拼团)
	 * 拼团订单状态:1=待支付,2=待成团,3=待发货,4=待收货,5=待评论,6=已完成,7=已取消
	 * 订单状态:1=待支付,2=待发货,3=待收货,4=待评论,5=售后订单(已弃用),6=已完成,7=已取消
	 *
	 */
	public function groups()
	{
		echo date("Y-m-d H:i:s").' [自动拼团]--开始运行';
		echo "\r\n";
		$config = get_addon_config('wanlshop');
		$canceltime = time() - ($config['order']['cancel'] * 86400);  // 取消未支付时间
		$receivingtime = time() - ($config['order']['receiving'] * 86400);  // 自动收货时间
		$commenttime = time() - ($config['order']['comment'] * 86400);  // 自动评论时间 
		// $groupstime = time() - ($config['groups']['time'] * 86400);  // 自动拼团时间
		
		// 过期拼团--------------------------------------------------------
		$close = model('app\api\model\wanlshop\groups\Groups')
			->where('validitytime', '<', time())
			->where('state', '=', 'start')
			->select();
		
		if($close){
			$list = [];
			$group_no = array_column($close, 'group_no');
			foreach(model('app\api\model\wanlshop\groups\OrderGoods')->where('group_no', 'in', $group_no)->select() as $goods){
				// 查询商品是否需要释放库存
				if(model('app\api\model\wanlshop\groups\Goods')->get($goods['goods_id'])['stock'] == 'porder'){
					model('app\api\model\wanlshop\groups\GoodsSku')->where('id', $goods['goods_sku_id'])->setInc('stock', $goods['number']);
				}
				$order = model('app\api\model\wanlshop\groups\Order')->get($goods['order_id']);
				// 释放优惠券 1.0.3升级
				if($order['coupon_id'] != 0){
					model('app\api\model\wanlshop\CouponReceive')->where(['id' => $order['coupon_id']])->update(['state' => 1]);
				}
				//退款给用户 1.1.2修复
				$pay = model('app\api\model\wanlshop\Pay')->get(['order_id' => $order['id'], 'type' => 'groups']);
				
				if($pay){
					controller('addons\wanlshop\library\WanlPay\WanlPay')->money(+$pay['price'], $pay['user_id'], '拼团失败自动退还到余额', 'groups_refund', $pay['order_no']);
				}
				$list[] = ['id' => $order['id'], 'state' => 7];
			}
			// 事务操作
			Db::startTrans();
			try {
			  	// 更新订单
			  	model('app\api\model\wanlshop\groups\Order')->isUpdate()->saveAll($list);
			  	// 更新拼团
			  	model('app\api\model\wanlshop\groups\Groups')->where('group_no', 'in', $group_no)->setField('state', 'fail');
				Db::commit();
			} catch (\Exception $e) {
				Db::rollback();
			}
			// 日志
			echo '[自动关闭过期拼团]--已清理'.count($close).'个过期拼团-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
		
		// 自动成团--------------------------------------------------------
		// $automatic = model('app\api\model\wanlshop\groups\Groups')
		// 	->where([
		// 		'state' => ['=', 'start'],
		// 		'createtime' => ['<', $groupstime]
		// 	])
		// 	->select();
		// // 阶梯拼团是否参与自动成团
		// if($automatic){
			
		// 	// 日志
		// 	echo '[自动成团]--已成功自动拼团'.count($automatic).'个订单-----'.date("Y-m-d H:i:s");
		// 	echo "\r\n";
		// }
		
		// 取消未支付------------------------------------------------------
		$cancel = model('app\api\model\wanlshop\groups\Order')
			->where([
				'state' => 1,
				'createtime' => ['<', $canceltime]
		    ])
			->select();
		if($cancel){
			$list = [];
			foreach($cancel as $order){
				// 查询商品库存计算方式
				foreach(model('app\api\model\wanlshop\groups\OrderGoods')->all(['order_id' => $order['id']]) as $vo){
					// 查询商品是否需要释放库存
					if(model('app\api\model\wanlshop\groups\Goods')->get($vo['goods_id'])['stock'] == 'porder'){
						model('app\api\model\wanlshop\groups\GoodsSku')->where('id', $vo['goods_sku_id'])->setInc('stock', $vo['number']);
					}
					// 删除拼团-1.0.8临时方案
					model('app\api\model\wanlshop\groups\Groups')
						->where([
							'group_no' => $vo['group_no'], 
							'user_id' => $this->auth->id,
							'state' => 'ready'
						])
						->setField('deletetime', time());
				}
				// 释放优惠券 1.0.3升级
				if($order['coupon_id'] != 0){
					model('app\api\model\wanlshop\CouponReceive')->where(['id' => $order['coupon_id']])->update(['state' => 1]);
				}
				$list[] = ['id' => $order['id'], 'state' => 7];
			}
			model('app\api\model\wanlshop\groups\Order')->isUpdate()->saveAll($list);
			// 日志
			echo '[取消未支付]--已成功取消'.count($cancel).'个订单未支付订单-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
		
		// 自动收货 1.0.5升级-------------------------------------------------------- 后续版本优化 Db::startTrans();
		$receiving = model('app\api\model\wanlshop\groups\Order')
			->where([
				'state' => 4,
				'delivertime' => ['<', $receivingtime]
		    ])
			->select();
		if($receiving){
			$list = [];
			foreach($receiving as $order){
				// 更新条件
				$list[] = ['id' => $order['id'], 'state' => 5, 'taketime' => time()];
				// 平台转款给商家 1.1.2升级
				$pay = model('app\api\model\wanlshop\Pay')->get(['order_id' => $order['id'], 'type' => 'groups']);
				controller('addons\wanlshop\library\WanlPay\WanlPay')->money(+$pay['price'], $order['shop']['user_id'], '自动确认收货', 'groups', $order['order_no']);
				// 查询是否有退款
				$refund = model('app\api\model\wanlshop\Refund')
					->where([
						'order_id' => $order['id'], 
						'order_type' => 'groups', 
						'state' => 5
					])
					->select();
				// 退款存在
				if($refund){
					foreach($refund as $value){
						controller('addons\wanlshop\library\WanlPay\WanlPay')->money(-$value['price'], $order['shop']['user_id'], '该订单存在的退款', 'groups_refund', $order['order_no']);
					}
				}
				$this->pushOrder($order['id'], '已自动收货', 'groups');
			}
			model('app\api\model\wanlshop\groups\Order')->isUpdate()->saveAll($list);
			echo '[自动确认收货]--已确认'.count($receiving).'个订单自动收货-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
		
		// 自动评论--------------------------------------------------------
		$comment = model('app\api\model\wanlshop\groups\Order')
			->where([
				'state' => 5,
				'taketime' => ['<', $commenttime]
		    ])
			->select();
		if($comment){
			$list = [];
			foreach($comment as $order){
				$orderGoods = model('app\api\model\wanlshop\groups\OrderGoods')
					->where(['order_id' => $order['id']])
					->select();
				$commentData = [];
				foreach ($orderGoods as $goods) {
					$commentData[] = [
						'user_id' => $order['user_id'],
						'shop_id' => $goods['shop_id'],
						'order_id' => $goods['order_id'],
						'goods_id' => $goods['goods_id'],
						'order_goods_id' => $goods['id'],
						'order_type' => 'groups',
						'state' => 0,
						'content' => '系统默认好评',
						'suk' => $goods['difference'],
						'score' => 5,
						'score_describe' => 5,
						'score_service' => 5,
						'score_deliver' => 5,
						'score_logistics' => 5
					];
					//评论暂不考虑并发，为列表提供好评付款率，减少并发只能写进商品中
					model('app\api\model\wanlshop\groups\Goods')->where(['id' => $goods['goods_id']])->setInc('comment');
					model('app\api\model\wanlshop\groups\Goods')->where(['id' => $goods['goods_id']])->setInc('praise');
				}
				$list[] = ['id' => $order['id'], 'state' => 6, 'dealtime' => time()];
			}
			// 事务操作
			Db::startTrans();
			try {
			  	model('app\api\model\wanlshop\GoodsComment')->isUpdate(false)->saveAll($commentData);
			  	model('app\api\model\wanlshop\groups\Order')->isUpdate(true)->saveAll($list);
				Db::commit();
			} catch (\Exception $e) {
				Db::rollback();
			}
			echo '[自动默认评论]--已成功评论'.count($comment).'个订单未评论订单-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
	}
	
	/**
	 * 实时订单（方法内使用）
	 *
	 * @ApiSummary  (WanlShop 实时订单)
	 *
	 */
	public function order()
	{
		echo date("Y-m-d H:i:s").' [实时订单]--开始运行';
		echo "\r\n";
		$config = get_addon_config('wanlshop');
		$canceltime = time() - ($config['order']['cancel'] * 86400);  // 取消未支付时间
		$receivingtime = time() - ($config['order']['receiving'] * 86400);  // 自动收货时间
		$commenttime = time() - ($config['order']['comment'] * 86400);  // 自动评论时间 
		
		// 取消未支付-1.0.3升级-释放库存、优惠券------------------------------------------------------
		$cancel = model('app\api\model\wanlshop\Order')
			->where([
				'state' => 1,
				'createtime' => ['<', $canceltime]
		    ])
			->select();
		if($cancel){
			$list = [];
			foreach($cancel as $order){
				// 查询商品库存计算方式
				foreach(model('app\api\model\wanlshop\OrderGoods')->all(['order_id' => $order['id']]) as $vo){
					// 查询商品是否需要释放库存
					if(model('app\api\model\wanlshop\Goods')->get($vo['goods_id'])['stock'] == 'porder'){
						model('app\api\model\wanlshop\GoodsSku')->where('id', $vo['goods_sku_id'])->setInc('stock', $vo['number']);
					}
				}
				// 释放优惠券 1.0.3升级
				if($order['coupon_id'] != 0){
					model('app\api\model\wanlshop\CouponReceive')->where(['id' => $order['coupon_id']])->update(['state' => 1]);
				}
				$list[] = ['id' => $order['id'], 'state' => 7];
			}
			model('app\api\model\wanlshop\Order')->isUpdate()->saveAll($list);
			// 日志
			echo '[取消未支付]--已成功取消'.count($cancel).'个订单未支付订单-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
		
		// 自动收货 1.0.5升级-------------------------------------------------------- 后续版本优化 Db::startTrans();
		$receiving = model('app\api\model\wanlshop\Order')
			->where([
				'state' => 3,
				'delivertime' => ['<', $receivingtime]
		    ])
			->select();
		if($receiving){
			$list = [];
			foreach($receiving as $order){
				// 更新条件
				$list[] = ['id' => $order['id'], 'state' => 4, 'taketime' => time()];
				// 平台转款给商家 1.1.2升级
				$pay = model('app\api\model\wanlshop\Pay')->get(['order_id' => $order['id'], 'type' => 'goods']);
				controller('addons\wanlshop\library\WanlPay\WanlPay')->money(+$pay['price'], $order['shop']['user_id'], '自动确认收货', 'pay', $order['order_no']);
				// 查询是否有退款
				$refund = model('app\api\model\wanlshop\Refund')
					->where([
						'order_id' => $order['id'], 
						'order_type' => 'goods', 
						'state' => 4
					])
					->select();
				// 退款存在
				if($refund){
					foreach($refund as $value){
						controller('addons\wanlshop\library\WanlPay\WanlPay')->money(-$value['price'], $order['shop']['user_id'], '该订单存在的退款', 'refund', $order['order_no']);
					}
				}
				$this->pushOrder($order['id'], '已自动收货', 'order');
			}
			model('app\api\model\wanlshop\Order')->isUpdate()->saveAll($list);
			echo '[自动确认收货]--已确认'.count($receiving).'个订单自动收货-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
		
		// 自动评论--------------------------------------------------------
		$comment = model('app\api\model\wanlshop\Order')
			->where([
				'state' => 4,
				'taketime' => ['<', $commenttime]
		    ])
			->select();
		if($comment){
			$list = [];
			foreach($comment as $order){
				$orderGoods = model('app\api\model\wanlshop\OrderGoods')
					->where(['order_id' => $order['id']])
					->select();
				$commentData = [];
				foreach ($orderGoods as $goods) {
					$commentData[] = [
						'user_id' => $order['user_id'],
						'shop_id' => $goods['shop_id'],
						'order_id' => $goods['order_id'],
						'goods_id' => $goods['goods_id'],
						'order_goods_id' => $goods['id'],
						'order_type' => 'goods',
						'state' => 0,
						'content' => '系统默认好评',
						'suk' => $goods['difference'],
						'score' => 5,
						'score_describe' => 5,
						'score_service' => 5,
						'score_deliver' => 5,
						'score_logistics' => 5
					];
					//评论暂不考虑并发，为列表提供好评付款率，减少并发只能写进商品中
					model('app\api\model\wanlshop\Goods')->where(['id' => $goods['goods_id']])->setInc('comment');
					model('app\api\model\wanlshop\Goods')->where(['id' => $goods['goods_id']])->setInc('praise');
				}
				$list[] = ['id' => $order['id'], 'state' => 6, 'dealtime' => time()];
			}
			// 事务操作
			Db::startTrans();
			try {
			  	model('app\api\model\wanlshop\GoodsComment')->isUpdate(false)->saveAll($commentData);
			  	model('app\api\model\wanlshop\Order')->isUpdate(true)->saveAll($list);
				Db::commit(); 
			} catch (\Exception $e) {
				Db::rollback();
			}
			echo '[自动默认评论]--已成功评论'.count($comment).'个订单未评论订单-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
	}
	
	/**
	 * 实时退款（方法内使用）
	 *
	 * @ApiSummary  (WanlShop 实时退款)
	 *
	 */
	public function refund()
	{
		echo date("Y-m-d H:i:s").' [实时退款]--开始运行';
		echo "\r\n";
		$config = get_addon_config('wanlshop');
		$agreetime = time() - ($config['order']['autoagree'] * 86400);  // 卖家 自动同意时间
		$returntime = time() - ($config['order']['returntime'] * 86400);  // 买家退货时间
		$receivingtime = time() - ($config['order']['receivingtime'] * 86400);  // 卖家 收货时间 
		
		
		// 自动同意退货--------------------------------------------------------
		$agreeGoods = model('app\api\model\wanlshop\Refund')
			->where([
				'state' => 0,
				'type' => 1, //退货退款
				'createtime' => ['<', $agreetime]
		    ])
			->select();
		if($agreeGoods){
			$list = [];
			foreach($agreeGoods as $refund){
				$list[] = [
					'id' => $refund['id'],
					'state' => 1,
					'agreetime' => time()
				];
				// 写入日志
				$this->refundLog($refund['id'], '自动同意退货');
				// 1.0.5升级 更新商品状态 
				$this->setOrderGoodsState(2, $refund['goods_ids'], $refund['order_type']);
				// 更新订单状态
				$this->setRefundState($refund['order_id'], $refund['order_type']);
				// 推送消息
				$this->pushRefund($refund['id'], $refund['order_id'], $refund['goods_ids'], '卖家超时退款自动同意', $refund['order_type']);
			}
			// 更新退款
			model('app\api\model\wanlshop\Refund')->isUpdate()->saveAll($list);
			// 日志
			echo '[自动同意退货]--已成功同意'.count($agreeGoods).'个退款-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
		
		// 自动同意退款--------------------------------------------------------
		$agreeMoney = model('app\api\model\wanlshop\Refund')
			->where([
				'state' => 0,
				'type' => 0, //我要退款(无需退货)
				'createtime' => ['<', $agreetime]
		    ])
			->select();
		if($agreeMoney){
			$list = [];
			foreach($agreeMoney as $refund){
				// 订单支付
				$orderPay = false;
				// 判断业务类型
				if($refund['order_type'] === 'groups'){
					// 查询订单是已确定收货
					$order = model('app\api\model\wanlshop\groups\Order')->get($refund['order_id']);
					// 订单状态:1=待支付,2=待成团,3=待发货,4=待收货,5=待评论,6=已完成,7=已取消
					$orderPay = $order['state'] == 5 ? true : false;
				}else{
					$order = model('app\api\model\wanlshop\Order')->get($refund['order_id']);
					// 订单状态:1=待支付,2=待发货,3=待收货,4=待评论,5=已弃用,6=已完成,7=已取消
					$orderPay = $order['state'] == 4 ? true : false;
				}
				// 更新钱包 1.此订单如果已确认收货扣商家 2.此订单没有确认收货，平台退款
				if($orderPay){
					// 扣商家
					controller('addons\wanlshop\library\WanlPay\WanlPay')->money(-$row['price'], $order['shop']['user_id'], '确认收货，同意退款', 'refund', $order['order_no']);
					// 退款给用户
					controller('addons\wanlshop\library\WanlPay\WanlPay')->money(+$row['price'], $row['user_id'], '卖家同意退款', 'refund', $order['order_no']);
				}else{
					//退款给用户
					controller('addons\wanlshop\library\WanlPay\WanlPay')->money(+$row['price'], $row['user_id'], '卖家同意退款', 'refund', $order['order_no']);
				}
				// 更新所有状态
				$list[] = [
					'id' => $refund['id'],
					'state' => 4,
					'agreetime' => time()
				];
				
				// 写入日志
				$this->refundLog($refund['id'], '自动完成退款');
				// 更新商品状态 
				$this->setOrderGoodsState(3, $refund['goods_ids'], $refund['order_type']);
				// 更新订单状态
				$this->setRefundState($refund['order_id'], $refund['order_type']);
				// 推送消息
				$this->pushRefund($refund['id'], $refund['order_id'], $refund['goods_ids'], '卖家超时退款自动完成', $refund['order_type']);
				
			}
			// 更新退款
			model('app\api\model\wanlshop\Refund')->isUpdate()->saveAll($list);
			// 日志
			echo '[自动完成退款]--已成功完成'.count($agreeGoods).'个退款-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
		
		// 买家退货时间，如果超时则关闭订单--------------------------------------------------------
		$returns = model('app\api\model\wanlshop\Refund')
			->where([
				'state' => 1,
				'type' => 1, //退货退款
				'agreetime' => ['<', $returntime]
		    ])
			->select();
		if($returns){
			$list = [];
			foreach($returns as $refund){
				$list[] = [
					'id' => $refund['id'],
					'state' => 5,
					'closingtime' => time()
				];
				// 写入日志
				$this->refundLog($refund['id'], '退货超时，系统自动关闭退款');
				// 更新商品状态  退款状态:0=未退款,1=退款中,2=待退货,3=退款完成,4=退款关闭,5=退款被拒
				$this->setOrderGoodsState(4, $refund['goods_ids'], $refund['order_type']);
				// 更新订单状态
				$this->setRefundState($refund['order_id'], $refund['order_type']);
				// 推送消息
				$this->pushRefund($refund['id'], $refund['order_id'], $refund['goods_ids'], '退货超时退款已关闭', $refund['order_type']);
			}
			// 更新退款
			model('app\api\model\wanlshop\Refund')->isUpdate()->saveAll($list);
			echo '[自动同意退款]--已成功同意'.count($returns).'个退款-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
		
		// 卖家自动收货--------------------------------------------------------
		$receiving = model('app\api\model\wanlshop\Refund')
			->where([
				'state' => 6,
				'returntime' => ['<', $receivingtime]
		    ])
			->select();
		if($receiving){
			$list = [];
			foreach($receiving as $refund){
				// 订单支付
				$orderPay = false;
				// 判断业务类型
				if($refund['order_type'] === 'groups'){
					// 查询订单是已确定收货
					$order = model('app\api\model\wanlshop\groups\Order')->get($refund['order_id']);
					// 订单状态:1=待支付,2=待成团,3=待发货,4=待收货,5=待评论,6=已完成,7=已取消
					$orderPay = $order['state'] == 5 ? true : false;
				}else{
					$order = model('app\api\model\wanlshop\Order')->get($refund['order_id']);
					// 订单状态:1=待支付,2=待发货,3=待收货,4=待评论,5=已弃用,6=已完成,7=已取消
					$orderPay = $order['state'] == 4 ? true : false;
				}
				// 更新钱包 1.此订单如果已确认收货扣商家 2.此订单没有确认收货，平台退款
				if($orderPay){
					// 扣商家
					controller('addons\wanlshop\library\WanlPay\WanlPay')->money(-$row['price'], $order['shop']['user_id'], '确认收货，同意退款', 'refund', $order['order_no']);
					// 退款给用户
					controller('addons\wanlshop\library\WanlPay\WanlPay')->money(+$row['price'], $row['user_id'], '卖家同意退款', 'refund', $order['order_no']);
				}else{
					//退款给用户
					controller('addons\wanlshop\library\WanlPay\WanlPay')->money(+$row['price'], $row['user_id'], '卖家同意退款', 'refund', $order['order_no']);
				}
				// 更新所有状态
				$list[] = [
					'id' => $refund['id'],
					'state' => 4,
					'completetime' => time()
				];
				
				// 写入日志
				$this->refundLog($refund['id'], '收货超时，系统自动同意退款');
				// 更新商品状态
				$this->setOrderGoodsState(3, $refund['goods_ids'], $refund['order_type']);
				// 更新订单状态
				$this->setRefundState($refund['order_id'], $refund['order_type']);
				// 推送消息
				$this->pushRefund($refund['id'], $refund['order_id'], $refund['goods_ids'], '退款已完成', $refund['order_type']);
			}
			// 更新退款
			model('app\api\model\wanlshop\Refund')->isUpdate()->saveAll($list);
			echo '[自动同意退款]--已成功同意'.count($receiving).'个退款-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
	}
	
	/**
	 * 实时优惠券（方法内使用）
	 *
	 * @ApiSummary  (WanlShop 实时优惠券)
	 *
	 */
	public function coupon()
	{
		echo date("Y-m-d H:i:s").' [自动优惠券]--开始运行';
		echo "\r\n";
		// 过期优惠券--------------------------------------------------------
		$coupon = model('app\api\model\wanlshop\Coupon')
			->where([
				'invalid' => 0,
				'pretype' => 'fixed',
				'enddate' => ['< time', date("Y-m-d")]
			])
			->setField('invalid', 1);
		if($coupon){
			echo '[自动优惠券]--已清理'.$coupon.'个过期优惠券-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
		
		// 用户领取过期优惠券--------------------------------------------------
		$couponReceive = model('app\api\model\wanlshop\CouponReceive');
		// 根据优惠券结束日期 清理已过期
		$fixedTime = $couponReceive->where([
			'state' => 1,
			'pretype' => 'fixed',
			'enddate' => ['< time', date("Y-m-d")]
		])->setField('state', 3);
		if($fixedTime){
			echo '[自动优惠券]--已清理'.$fixedTime.'个过期优惠券-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
		// 根据领取时间 清理已过期
		$list = $couponReceive->where([
			'state' => 1,
			'pretype' => 'appoint',
			'validity' => ['>', 0]
		])->select();
		$coupon_id = [];
		foreach ($list as $row) {
			$endtime = $row['createtime'] + ($row['validity'] * 86400);
			if(time() > $endtime){
				$coupon_id[] = $row['id'];
			}
		}
		$update = $couponReceive
			->where('id', 'in', $coupon_id)
			->setField('state', 3);
		if($update){
			echo '[自动优惠券]--已清理'.$update.'个过期优惠券-----'.date("Y-m-d H:i:s");
			echo "\r\n";
		}
	}
	
	/**
	 * 订单推送消息（方法内使用）
	 * 
	 * @param string order_id 订单ID
	 * @param string state 状态
	 */
	private function pushOrder($order_id = 0, $state = '已发货', $order_type = 'order')
	{
		if($order_type === 'order'){
			$orderModel = model('app\api\model\wanlshop\Order');
			$orderGoodsModel = model('app\api\model\wanlshop\OrderGoods');
		}else if($order_type === 'groups'){
			$orderModel = model('app\api\model\wanlshop\groups\Order');
			$orderGoodsModel = model('app\api\model\wanlshop\groups\OrderGoods');
		}
		// 1.0.5升级
		$order = $orderModel->get($order_id);
		$orderGoods = $orderGoodsModel
			->where(['order_id' => $order_id])
			->select();
		$msgData = [];
		foreach ($orderGoods as $goods) {
			$msg = [
				'user_id' => $order['user_id'], // 推送目标用户
				'shop_id' => $order['shop_id'], 
				'title' => '您的订单'.$state, // 推送标题
				'image' => $goods['image'], // 推送图片
				'content' => '您购买的商品 '.(mb_strlen($goods['title'],'utf8') >= 25 ? mb_substr($goods['title'],0,25,'utf-8').'...' : $goods['title']).' '.$state, 
				'type' => 'order',  // 推送类型
				'modules' => $order_type === 'groups' ? 'groupsorder' : 'order',  // 模块类型
				'modules_id' => $order_id,  // 模块ID
				'come' => '订单'.$order['order_no'] // 来自
			];
			$msgData[] = $msg;
			$chat = new WanlChat();
			$chat->send($order['user_id'], $msg);
		}
		$notice = model('app\api\model\wanlshop\Notice')->saveAll($msgData);
	}
	
    /**
     * 退款日志（方法内使用）
     *
     * @ApiSummary  (WanlShop 退款日志)
     * @ApiMethod   (POST)
     * 
     * @param string $refund_id 退款ID
     * @param string $content 日志内容
     */
    private function refundLog($refund_id = 0, $content = '')
    {
    	return model('app\api\model\wanlshop\RefundLog')->save([
    		'user_id' => 0,
    		'type' => 3,
    		'refund_id' => $refund_id,
    		'content' => $content
    	]);
    }
	
	/**
	 * 更新订单商品状态（方法内使用）1.0.5升级
	 *
	 * @ApiSummary  (WanlShop 更新订单商品状态)
	 * @ApiMethod   (POST)
	 * 
	 * @param string $status 状态
	 * @param string $goods_id 商品ID
	 */
	private function setOrderGoodsState($status = 0, $goods_id = 0, $order_type = 'goods')
	{
		if($order_type === 'goods'){
			$orderGoodsModel = model('app\api\model\wanlshop\OrderGoods');
		}else if($order_type === 'groups'){
			$orderGoodsModel = model('app\api\model\wanlshop\groups\OrderGoods');
		}
		return $orderGoodsModel->save(['refund_status' => $status],['id' => $goods_id]);
	}
	
	/**
	 * 修改订单状态（方法内使用） 1.0.5升级
	 *
	 * @ApiSummary  (WanlShop 修改订单状态)
	 * @ApiMethod   (POST)
	 * 
	 * @param string $id 订单ID
	 */
	private function setRefundState($order_id = 0, $order_type = 'goods')
	{
		if($order_type === 'goods'){
			$orderModel = model('app\api\model\wanlshop\Order');
			$orderGoodsModel = model('app\api\model\wanlshop\OrderGoods');
		}else if($order_type === 'groups'){
			$orderModel = model('app\api\model\wanlshop\groups\Order');
			$orderGoodsModel = model('app\api\model\wanlshop\groups\OrderGoods');
		}
		
		$list = $orderGoodsModel
			->where(['order_id' => $order_id])
			->select();
		$refundStatusCount = 0;
		foreach($list as $row){
			// 退款状态:0=未退款,1=退款中,2=待退货,3=退款完成,4=退款关闭,5=退款被拒
			if($row['refund_status'] == 3) $refundStatusCount += 1;
		}
		// 订单状态:1=待支付,2=待发货,3=待收货,4=待评论,5=售后订单(已弃用),6=已完成,7=已取消 1.0.5升级
		// 如果订单下所有商品全部退款完毕则关闭订单
		if(count($list) == $refundStatusCount){
			$orderModel->save(['state'  => 7],['id' => $order_id]);
			return true;
		}
		return false;
	}
	
	
    /**
     * 推送退款消息（方法内使用）
     *
     * @param string refund_id 订单ID
     * @param string order_id 订单ID
     * @param string goods_id 订单ID
     * @param string title 标题
     */
    private function pushRefund($refund_id = 0, $order_id = 0, $goods_id = 0, $title = '', $order_type = 'goods')
    {
		if($order_type === 'groups'){
			$orderModel = model('app\api\model\wanlshop\groups\Order');
			$orderGoodsModel = model('app\api\model\wanlshop\groups\OrderGoods');
		}else{
			$orderModel = model('app\api\model\wanlshop\Order');
			$orderGoodsModel = model('app\api\model\wanlshop\OrderGoods');
		}
		
    	$order = $orderModel->get($order_id);
    	$goods = $orderGoodsModel->get($goods_id);
    	$msg = [
    		'user_id' => $order['user_id'], // 推送目标用户
    		'shop_id' => $order['shop_id'], 
    		'title' => $title,  // 推送标题
    		'image' => $goods['image'], // 推送图片
    		'content' => '您申请退款的商品 '.(mb_strlen($goods['title'],'utf8') >= 25 ? mb_substr($goods['title'],0,25,'utf-8').'...' : $goods['title']).' '.$title, 
    		'type' => 'order',  // 推送类型
			'modules' => $order_type === 'groups' ? 'groupsrefund' : 'refund',  // 模块类型
    		'modules_id' => $refund_id,  // 模块ID
    		'come' => '订单'.$order['order_no'] // 来自
    	];
		$chat = new WanlChat();
    	$chat->send($order['user_id'], $msg);
    	$notice = model('app\index\model\wanlshop\Notice');
    	$notice->data($msg);
    	$notice->allowField(true)->save();
    }
}