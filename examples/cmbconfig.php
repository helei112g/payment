<?php
/**
 * 招商一网通配置文件
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/27
 * Time: 上午11:29
 */

return [
    'use_sandbox'   => true,// 是否使用 招商测试系统

    'branch_no'     => '0021',  // 商户分行号，4位数字
    'merchant_no'   => '000250',// 商户号，6位数字
    'mer_key'       => 'WWWyimishijicom1',// 秘钥

    // 招商的公钥，建议每天凌晨2:15发起查询招行公钥请求更新公钥。
    'cmb_pub_key'   => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDZs4l8Ez3F4MG0kF7RRSL+pn8MmxVE3nfdXzjx6d3rH8IfDbNvNRLS0X0b5iJnPyFO8sbbUo1Im4zX0M8XA0xnnviGyn5E6occiyUXJRgokphWb5BwaYdVhnLldctdimHoJTk3NFEQFav3guygR54i3tymrDc8lWtuG8EczVu8FwIDAQAB',

    'op_pwd'        => '000250',// 操作员登录密码。
    'sign_type'     => 'SHA-256',// 签名算法,固定为“SHA-256”
    'limit_pay'     => [
        //'A',
    ],// 允许支付的卡类型,默认对支付卡种不做限制，储蓄卡和信用卡均可支付   A:储蓄卡支付，即禁止信用卡支付

    'notify_url'    => 'http://114.215.86.31/__readme/phpinfo.php',

    'sign_notify_url'   => 'http://114.215.86.31/__readme/phpinfo.php',// 成功签约结果通知地址

    'return_url'    => 'https://helei112g.github.io/',// 如果是h5支付，可以设置该值，返回到指定页面

    'return_raw'                => true,// 在处理回调时，是否直接返回原始数据，默认为true
];