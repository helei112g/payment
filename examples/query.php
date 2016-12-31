<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:28
 * @description: 交易状态查询
 */

require_once __DIR__ . '/../autoload.php';

$aliconfig = require_once __DIR__ . '/aliconfig.php';

$wxconfig = require_once __DIR__ . '/wxconfig.php';

use Payment\QueryContext;
use Payment\Common\PayException;
use Payment\Config;

$query = new QueryContext();

// 通过支付宝交易号查询，  推荐
$data = [
    'transaction_id'    => '2016123121001004350200119946',// 支付宝流水号
    'refund_no'         => '2016123107452483',// 新版支付宝查询 退款 状态，必须传入该值.  即：商户自己生成的退款单号
    //'order_no'    => '201612311430',// 商户订单号

    //'transaction_id'    => '4007572001201607098672633287',// 微信订单查询  微信退款单查询
    //'trans_no'  => '1007570439201601142692427764', // 微信批量转款查询  使用商户生成的转款单号
];

try {
    // 支付宝订单查询
    //$query->initQuery(Config::ALI, $aliconfig);

    // 支付宝退款订单状态查询
    $query->initQuery(Config::ALI_REFUND, $aliconfig);

    // 微信订单查询
    //$query->initQuery(Config::WEIXIN, $wxconfig);

    // 微信退款订单状态查询
    //$query->initQuery(Config::WEIXIN_REFUND, $wxconfig);

    // 微信企业付款查询
    //$query->initQuery(Config::WEIXIN_TRANS, $wxconfig);

    $ret = $query->query($data);

} catch (PayException $e) {
    echo $e->errorMessage();exit;
}

var_dump($ret);