<?php
/**
 * @author: helei
 * @createTime: 2016-07-19 15:45
 * @description: 测试回调接口
 */

require_once __DIR__ . '/../autoload.php';

use Payment\NotifyContext;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Notify\PayNotifyInterface;

// 支付宝回调返回的数据
$_POST = array (
    'discount' => '0.00',
    'payment_type' => '1',
    'subject' => '爱羽客APP钱包充值',
    'trade_no' => '2016041921001004600214513551',
    'buyer_email' => '13957051976',
    'gmt_create' => '2016-04-19 15:19:44',
    'notify_type' => 'trade_status_sync',
    'quantity' => '1',
    'out_trade_no' => '2016041903193399',
    'seller_id' => '2088911525726514',
    'notify_time' => '2016-07-19 15:34:50',
    'body' => '爱羽客APP钱包充值',
    'trade_status' => 'TRADE_FINISHED',
    'is_total_fee_adjust' => 'N',
    'total_fee' => '200.00',
    'gmt_payment' => '2016-04-19 15:19:44',
    'seller_email' => 'colaboy2000@126.com',
    'gmt_close' => '2016-07-19 15:20:31',
    'price' => '200.00',
    'buyer_id' => '2088012032715605',
    'notify_id' => '297fe3ec79693c52e42b9f2d18cc839kmq',
    'use_coupon' => 'N',
    'sign_type' => 'RSA',
    'sign' => 'nZ1znlgGTfhZnthqpmMZ9SbRToxaff7KR8LSvta7AndNW3hC+BVbi9DcrdWWErHK/D+QC955eG9kxFPaC/GRaI7oiYnn1XBKDLgNx+eso4y0dnhdm6Jc6G7d0XCCkos3W2ehiEVpNMWcphfj+UtfW2QFtH6fm7RWq5gCXzbF9/M=',
);

$wxNotify = array (
    'appid' => 'wx346706e82a88f73e',
    'bank_type' => 'CFT',
    'cash_fee' => '1',
    'fee_type' => 'CNY',
    'is_subscribe' => 'N',
    'mch_id' => '1358947702',
    'nonce_str' => 'jor73in9yobuaxnsi9jvoruq0zj83frg',
    'openid' => 'obpX3wDm9LplSzPK6617t9rsR8iw',
    'out_trade_no' => '2016070507315045',
    'result_code' => 'SUCCESS',
    'return_code' => 'SUCCESS',
    'sign' => 'AD005CD408BB21E38500B99296C2520A',
    'time_end' => '20160705193153',
    'total_fee' => '1',
    'trade_type' => 'APP',
    'transaction_id' => '4007712001201607058425957548',
);

$aliconfig = require_once __DIR__ . '/aliconfig.php';

require_once __DIR__ . '/testNotify.php';
// 测试支付宝的回调通知
$notify = new NotifyContext();

try {
    $notify->initNotify(Config::ALI, $aliconfig);
    $notify->notify(new TestNotify());
} catch (PayException $e) {
    echo $e->errorMessage();exit;
}