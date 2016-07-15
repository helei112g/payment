<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:12
 * @description:
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
        $this->config = new WxConfig($config);
    }
}