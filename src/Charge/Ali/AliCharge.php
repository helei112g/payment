<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:10
 * @description:
 */

namespace Payment\Charge\Ali;


use Payment\Charge\ChargeStrategy;
use Payment\Common\AliConfig;

abstract class AliCharge implements ChargeStrategy
{
    /**
     * 支付宝的配置文件
     * @var AliConfig $config
     */
    protected $config;

    /**
     * 支付的数据
     * @var array $payData
     */
    protected $payData;

    public function __construct(array $config)
    {
        $this->config = new AliConfig($config);
    }
}