<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 10:36
 * @description:
 */

namespace Payment\Refund;


use Payment\Common\Ali\Data\RefundData;
use Payment\Common\AliConfig;
use Payment\Common\PayException;

class AliRefund implements RefundStrategy
{
    /**
     * 支付宝的配置文件
     * @var AliConfig $config
     */
    protected $config;

    /**
     * AliCharge constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        try {
            $this->config = new AliConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 支付宝处理退款业务
     * @param array $data
     * @return mixed
     * @throws PayException
     * @author helei
     */
    public function handle(array $data)
    {
        try {
            $ret = new RefundData($this->config, $data);
        } catch (PayException $e) {
            throw $e;
        }

        $ret->setSign();

        $retData = $this->config->getewayUrl . http_build_query($ret->getData());

        return $retData;
    }
}