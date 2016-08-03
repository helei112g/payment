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

// 转款数据
$transData = [
    'trans_no' => createPayid(),
    'trans_data'   => [
        [
            'serial_no' => createPayid(),
            'user_account' => 'dayugog@gmail.com',
            'user_name' => '愚不可及',
            'trans_fee' => '0.01',
            'desc'  => '测试批量转款',
        ]
    ],
];

$refund = new TransferContext();
try {
    $refund->initTransfer(Config::ALI, $aliconfig);
    $ret = $refund->transfer($transData);
} catch (PayException $e) {
    echo $e->errorMessage();exit;
}

// 跳转支付宝
header("Location:{$ret}");