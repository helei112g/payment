<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 15:46
 * @description: 批量转款测试
 */

require_once __DIR__ . '/../autoload.php';

use Payment\TransferContext;
use Payment\Common\PayException;
use Payment\Config;

//  生成转款单号 便于测试
function createPayid()
{
    return date('Ymdhis', time()).substr(floor(microtime()*1000),0,1).rand(0,9);
}

$aliconfig = require_once __DIR__ . '/aliconfig.php';

// 微信的配置文件
$wxconfig = require_once __DIR__ . '/wxconfig.php';

// 转款数据
$transData = [
    'trans_no' => createPayid(),
    'trans_data'   => [
        [
            'serial_no' => createPayid(),
            //'user_account' => 'dayugog@gmail.com',// 支付宝转款时，为支付宝账号
            'user_account' => 'otijfvr2oMz3tXnaQdKKbQeeBmhM',// 微信转款时，为用户所关注公众号的openid
            'user_name' => '愚不可及',
            'trans_fee' => '1',
            'desc'  => '测试批量转款',
        ]
    ],
];

$refund = new TransferContext();
try {
    // 支付宝的企业付款,支持批量
    // $type = Config::ALI;
    //$refund->initTransfer($type, $aliconfig);

    // 微信的企业付款， 仅支持单笔
    $type = Config::WEIXIN;
    $refund->initTransfer(Config::WEIXIN, $wxconfig);

    $ret = $refund->transfer($transData);
} catch (PayException $e) {
    echo $e->errorMessage();exit;
}

if ($type == Config::WEIXIN) {
    var_dump($ret);
} else {
    // 跳转支付宝
    header("Location:{$ret}");
}
