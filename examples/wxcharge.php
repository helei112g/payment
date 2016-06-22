<?php
/**
 * @author: helei
 * @createTime: 2016-06-14 16:41
 * @description:
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Factory\ChargeFactory;
use Payment\Common\ChargeChannel;
use Payment\Common\PayException;

// 微信APP支付实例
//$appCharge = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_WX);

// 微信公众号支付实例
//$appCharge = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_WX_PUB);

// 微信扫码支付实例
$appCharge = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_WX_QR);

$payData = [
    "order_no"	=> 'F2016062417161',// 必须， 商户订单号，适配每个渠道对此参数的要求，必须在商户系统内唯一
    "amount"	=> '0.01',// 订单总金额, 人民币为元
    "subject"	=> '测试即时到帐接口',// 必须， 商品的标题，该参数最长为 32 个 Unicode 字符
    "body"	=> '即时到帐接口，就是爱支付',// 必须， 商品的描述信息
    "client_ip"	=> '127.0.0.1',//  必须， 微信支付，必须提供该ip
    "success_url"	=> 'http://mall.tys.tiyushe.net/pay-test/notify.html',// 必须， 支付成功的回调地址  统一使用异步通知  该url后，不能带任何参数。
    "time_expire"	=> '15',// 可选， 订单失效时间，单位是 分钟
    'product_id'    => 'dfsfadsf', // 可选，支付方式为 CHANNEL_IS_WX_QR 时，必须提供
    'openid'    => '324234ddfg', // 可选，支付方式为 CHANNEL_IS_WX_PUB 时，必须提供
    "description"	=> '这是附带的业务数据',//  可选，如果用户请求时传递了该参数，则返回给商户时会回传该参数
];

// 必须设置时区，否则微信支付可能带来意外的结果
date_default_timezone_set('Asia/Shanghai');

try {
    $reqArr = $appCharge->charges($payData);// 调用该函数，会抛出  PayException 异常

    if (isset($reqArr['data']['code_url'])) {
        $url = $reqArr['data']['code_url'];
        echo "<img alt='模式二扫码支付' src='http://paysdk.weixin.qq.com/example/qrcode.php?data={$url}' style='width:150px;height:150px;'/>";exit;
    }
    var_dump($reqArr);
} catch (PayException $e) {
    echo $e->errorMessage();
}