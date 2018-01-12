<?php
namespace Payment\Client;

use Payment\Common\PayException;
use Payment\Config;
use Payment\TransferContext;

/**
 * @author: helei
 * @createTime: 2017-09-02 18:20
 * @description: 转账操作客户端接口
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
 * Class Transfer
 * @package Payment\Client
 */
class Transfer
{
    private static $supportChannel = [
        Config::ALI_TRANSFER,// 支付宝

        Config::WX_TRANSFER,// 微信

        'cmb_wallet',// 招行一网通
        'applepay_upacp',// Apple Pay
    ];

    /**
     * 转账实例
     * @var TransferContext
     */
    protected static $instance;

    protected static function getInstance($channel, $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        if (is_null(self::$instance)) {
            static::$instance = new TransferContext();
        }

        try {
            static::$instance->initTransfer($channel, $config);
        } catch (PayException $e) {
            throw $e;
        }

        return static::$instance;
    }

    /**
     * @param $channel
     * @param $config
     * @param $metadata
     *
     * @return array
     * @throws PayException
     */
    public static function run($channel, $config, $metadata)
    {
        if (! in_array($channel, self::$supportChannel)) {
            throw new PayException('sdk当前不支持该退款渠道，当前仅支持：' . implode(',', self::$supportChannel));
        }

        try {
            $instance = self::getInstance($channel, $config);

            $ret = $instance->transfer($metadata);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }
}
