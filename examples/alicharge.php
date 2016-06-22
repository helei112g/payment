<?php
/**
 * @author: helei
 * @createTime: 2016-06-14 15:33
 * @description:
 */

require_once __DIR__ . '/../autoload.php';

use Payment\Factory\ChargeFactory;
use Payment\Common\ChargeChannel;
use Payment\Common\PayException;

$payData = [
    "order_no"	=> 'F616699445072025',// 必须， 商户订单号，适配每个渠道对此参数的要求，必须在商户系统内唯一
    "amount"	=> '0.01',// 必须， 订单总金额, 人民币为元
    "client_ip"	=> '127.0.0.1',//  可选， 发起支付请求客户端的 IP 地址，格式为 IPV4
    "subject"	=> 'Older Driver',// 必须， 商品的标题，该参数最长为 32 个 Unicode 字符
    "body"	=> '购买Older Driver',// 必须， 商品的描述信息
    "success_url"	=> 'http://mall.devtiyushe.com/order/default/ali-pay-notify.html',// 必须， 支付成功的回调地址  统一使用异步通知  该url后，不能带任何参数。
    "return_url"	=> 'http://mall.devtiyushe.com/order/default/pay-return-url.html',// 移动支付不要传这个参数
    "time_expire"	=> '14',// 可选， 订单失效时间，单位是 分钟
    "description"	=> '',//  可选，如果用户请求时传递了该参数，则返回给商户时会回传该参数
];


// 获取支付宝手机支付实例
//$pcCharge = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_ALIPAY);

// 获取支付宝网站支付实例
$pcCharge = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_ALIPAY_DIRECT);
try {
    $ret = $pcCharge->charges($payData);

    if (is_array($ret)) {
        var_dump($ret);
    } else {
        header("Location:{$ret}");
    }
} catch (PayException $e) {
    echo $e->errorMessage();
}
