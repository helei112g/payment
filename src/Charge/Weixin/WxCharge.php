<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:12
 * @description: 
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Charge\Weixin;


use Payment\Charge\ChargeStrategy;
use Payment\Common\WxConfig;

abstract class WxCharge implements ChargeStrategy
{
    /**
    * 微信的配置文件
    * @var WxConfig
    */
    protected $config;

    public function __construct(array $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        $this->config = new WxConfig($config);
    }
}