<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/7
 * Time: 上午10:50
 */

namespace Payment\Client;

use Payment\Common\PayException;
use Payment\Config;
use Payment\RefundContext;

/**
 * 退款操作客户端接口
 * Class Refund
 * @package Payment\Client
 */
class Refund
{
    private static $supportChannel = [
        Config::ALI_REFUND,// 支付宝

        Config::WX_REFUND,// 微信

        Config::CMB_REFUND,// 招行一网通

        'applepay_upacp',// Apple Pay
    ];

    /**
     * 异步通知类
     * @var RefundContext
     */
    protected static $instance;

    protected static function getInstance($channel, $config)
    {
        if (is_null(self::$instance)) {
            static::$instance = new RefundContext();

            try {
                static::$instance->initRefund($channel, $config);
            } catch (PayException $e) {
                throw $e;
            }
        }

        return static::$instance;
    }

    public static function run($channel, $config, $refundData)
    {
        if (! in_array($channel, self::$supportChannel)) {
            throw new PayException('sdk当前不支持该退款渠道，当前仅支持：' . implode(',', self::$supportChannel));
        }

        try {
            $instance = self::getInstance($channel, $config);

            $ret = $instance->refund($refundData);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }
}