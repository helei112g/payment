<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    'use_sandbox' => true, // 是否使用 招商测试系统

    'branch_no' => 'xxxx',  // 商户分行号，4位数字
    'mch_id'    => 'xxxxxx', // 商户号，6位数字
    'mer_key'   => 'xxxaaaabbbccccc1', // 秘钥16位，包含大小写字母 数字

    // 招商的公钥，建议每天凌晨2:15发起查询招行公钥请求更新公钥。
    'cmb_pub_key' => 'xxxxxx',

    'op_pwd'    => 'xxxxxx', // 操作员登录密码。
    'sign_type' => 'SHA-256', // 签名算法,固定为“SHA-256”
    'limit_pay' => 'A', // 允许支付的卡类型,默认对支付卡种不做限制，储蓄卡和信用卡均可支付   A:储蓄卡支付，即禁止信用卡支付

    'notify_url' => 'https://dayutalk.cn/notify/cmb', // 支付成功的回调

    'sign_notify_url' => 'https://dayutalk.cn/notify/cmb', // 成功签约结果通知地址
    'sign_return_url' => 'https://dayutalk.cn', // 成功签约结果通知地址

    'return_url' => 'https://dayutalk.cn', // 如果是h5支付，可以设置该值，返回到指定页面
];
