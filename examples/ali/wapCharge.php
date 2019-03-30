<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Client\Charge;
use Payment\Common\PayException;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

// 订单信息
$orderNo = time() . rand(1000, 9999);
$payData = [
    'body'            => 'ali wap pay',
    'subject'         => '测试支付宝手机网站支付',
    'order_no'        => $orderNo,
    'timeout_express' => time() + 600, // 表示必须 600s 内付款
    'amount'          => '0.01', // 单位为元 ,最小为0.01
    'return_param'    => 'tata', // 一定不要传入汉字，只能是 字母 数字组合
    // 'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
    'goods_type' => '1', // 0—虚拟类商品，1—实物类商品
    'store_id'   => '',
    'quit_url'   => 'http://Leo112g.github.io', // 收银台的返回按钮（用户打断支付操作时返回的地址,4.0.3版本新增）
];

try {
    $url = Charge::run(Config::ALI_CHANNEL_WAP, $aliConfig, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

header('Location:' . $url);
