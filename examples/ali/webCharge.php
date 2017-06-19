<?php
/**
 * 电脑支付  即时到账接口
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午4:34
 */

require_once __DIR__ . '/../../autoload.php';

use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$aliConfig = require_once __DIR__ . '/../aliconfig.php';

// 订单信息
$orderNo = time() . rand(1000, 9999);
$payData = [
    'body'    => 'ali web pay',
    'subject'    => '测试支付宝电脑网站支付',
    'order_no'    => $orderNo,
    'timeout_express' => time() + 600,// 表示必须 600s 内付款
    'amount'    => '0.01',// 单位为元 ,最小为0.01
    'return_param' => '123123',
    'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
    'goods_type' => '1',
    'store_id' => '',

    // 说明地址：https://doc.open.alipay.com/doc2/detail.htm?treeId=270&articleId=105901&docType=1
    // 建议什么也不填
    'qr_mod' => '',
];

try {
    $url = Charge::run(Config::ALI_CHANNEL_WEB, $aliConfig, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

header('Location:' . $url);