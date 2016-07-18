<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:10
 * @description:
 */

namespace Payment\Charge\Ali;


use Payment\Charge\ChargeStrategy;
use Payment\Common\AliConfig;
use Payment\Common\PayException;

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

    /**
     * AliCharge constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        try {
            $this->config = new AliConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }
}