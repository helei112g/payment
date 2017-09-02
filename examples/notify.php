<?php
/**
 * 第三方支付回调处理
 * @author: helei
 * @createTime: 2016-07-25 15:57
 * @description: 支付通知回调
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/testNotify.php';

use Payment\Common\PayException;
use Payment\Client\Notify;

date_default_timezone_set('Asia/Shanghai');

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig = require_once __DIR__ . '/wxconfig.php';
$cmbConfig = require_once __DIR__ . '/cmbconfig.php';

$callback = new TestNotify();

$from = $_GET['from'] ? $_GET['from'] : 'ali';

if ($from === 'ali') {
    $config = $aliConfig;
    $type = 'ali_charge';
    parse_str('body=test+body&subject=test+subject&sign_type=RSA2&buyer_logon_id=aaq***%40sandbox.com&auth_app_id=2016073100130857&notify_type=trade_status_sync&out_trade_no=14893825198432&point_amount=0.00&version=1.0&fund_bill_list=%5B%7B%22amount%22%3A%220.01%22%2C%22fundChannel%22%3A%22ALIPAYACCOUNT%22%7D%5D&passback_params=123&buyer_id=2088102169940354&total_amount=0.01&trade_no=2017031321001004350200145287&notify_time=2017-03-13+13%3A23%3A05&charset=UTF-8&invoice_amount=0.01&gmt_payment=2017-03-13+13%3A23%3A04&trade_status=TRADE_SUCCESS&sign=SrfDm1whLHx8PeFcPbAEn7S43%2BOTMy5ZnTxv42jpCeRXz8poKS0n542Nf4eAq7%2BJfta1vMqybMFf9C4Cl%2B3WEPFbndU2WGpboyU2CPUcSoYaBE68H1%2FImNUomEi3vMjJe3H4s%2Fz%2BLOnVcH8luO0bbSB79kKupec0fdm9V9Wg2axaZD9UkRLwBvoXsDx9tFOAwhqHyY1ZPq%2F1SQj5cwhQ2luKhJaqjO4L4Z819b%2BvHZfuaKX3xt5pgCQXiSVLo%2BfA%2FY0RmDfNngZML8UndYyXpXmgTMH2grR7D65ODPlatDt3JsNe9U2Kj%2F7uVXdPR2Tey3ikL4W4Pn4%2FULq8ow3YHw%3D%3D&gmt_create=2017-03-13+13%3A23%3A03&buyer_pay_amount=0.01&receipt_amount=0.01&seller_id=2088102169252684&app_id=2016073100130857&seller_email=naacvg9185%40sandbox.com&notify_id=27d63b0f7da1e21d932b6ec9176a052ipa', $data);
    $_POST = $data;
} elseif ($from === 'wx') {
    $config = $wxConfig;
    $type = 'wx_charge';
} else {
    $config = $cmbConfig;
    $type = 'cmb_charge';
}

try {
    //$retData = Notify::getNotifyData($type, $config);// 获取第三方的原始数据，未进行签名检查

    $ret = Notify::run($type, $config, $callback);// 处理回调，内部进行了签名检查
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

var_dump($ret);
exit;
