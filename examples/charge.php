<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 09:25
 * @description: 测试支付接口
 */

require_once __DIR__ . '/../autoload.php';

use Pyament\ChargeContext;
use Pyament\Config;


// 订单信息
$payData = [
    "order_no"	=> 'F616699445072025',
    "amount"	=> '0.01',
    "client_ip"	=> '127.0.0.1',
    "subject"	=> 'Older Driver',
    "body"	=> '购买Older Driver',
    "success_url"	=> 'http://mall.devtiyushe.com/order/default/ali-pay-notify.html',
    "return_url"	=> 'http://mall.devtiyushe.com/order/default/pay-return-url.html',
    "time_expire"	=> '14',
    "description"	=> '',
];

$aliconfig = require_once __DIR__ . 'aliconfig.php';

// 初始化相关数据
$charge = new ChargeContext();
$charge->initCharge(Config::ALI_CHANNEL_WEB, $aliconfig);

// 调起支付
$charge->charge($payData);
