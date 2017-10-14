<?php
/**
 * 招商一网通支付
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 上午11:55
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$cmbConfig = require_once __DIR__ . '/../cmbconfig.php';

$orderNo = time() . rand(1000, 9999);
// 订单信息
$payData = [
    'order_no'    => $orderNo,// 招行订单位数变更为32位
    'timeout_express' => time() + 600,// 表示必须 600s 内付款
    'amount'    => '0.01',// 单位为元 ,最小为0.01
    'return_param' => 'tatata',
    'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
    'date' => date('Ymd'),
    'agr_no' => '430802198004014358',// 建议用身份证
    'serial_no' => time() . rand(1000, 9999),// 协议开通请求流水号，开通协议时必填
    'user_id' => '888',
    'mobile' => '13500007107',
    'lon' => '',
    'lat' => '',
    'risk_level' => '3',
];

try {
    $data = Charge::run(Config::CMB_CHANNEL_APP, $cmbConfig, $payData);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

$btnText = '点我开始支付';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>一网通支付</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <meta content="telephone=no" name="format-detection">
    <style>
        .box{
    padding:6px 10px
        }
        .button {
    color: #f5efef;
    background-color: #10a737;
            border-color: #EEE;
            font-weight: 300;
            font-size: 16px;
            font-family: "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            text-decoration: none;
            text-align: center;
            line-height: 40px;
            height: 100px;
            padding: 0 40px;
            margin: 0;
            width: 100%;
            display: inline-block;
            appearance: none;
            cursor: pointer;
            border: none;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            -webkit-transition-property: all;
            transition-property: all;
            -webkit-transition-duration: .3s;
            transition-duration: .3s;
        }
        .button-rounded {
    border-radius: 4px;
        }
        .button-uppercase {
    text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="box">
        <form method="post" action="<?php echo $data['url'] ?>">
            <input type="hidden" name="<?php echo $data['name'] ?>" value='<?php echo $data['value'] ?>'>
            <button type="submit" class="button button-rounded button-uppercase"><?php echo $btnText ?></button>
</form>
</div>
</body>
</html>