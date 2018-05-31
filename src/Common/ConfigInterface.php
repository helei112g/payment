<?php
namespace Payment\Common;

/**
 * @author: helei
 * @createTime: 2016-07-15 17:42
 * @description: 配置文件接口，主要提供返回属性数组的功能
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
abstract class ConfigInterface
{
    // 是否返回原始数据
    public $returnRaw = true;

    // 是否使用测试模式
    public $useSandbox = true;

    // 禁止使用的支付渠道
    public $limitPay;

    // 用于异步通知的地址
    public $notifyUrl;

    // 加密方式
    // 支付宝：默认使用RSA   目前支持RSA2和RSA
    // 微信： 默认使用MD5
    public $signType = 'RSA';

    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * 初始化配置文件
     * WxConfig constructor.
     * @param array $config
     * @throws PayException
     */
    final public function __construct(array $config)
    {
        try {
            $this->initConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 配置文件初始化具体实现
     * @param array $config
     */
    abstract protected function initConfig(array $config);
}
