<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/7
 * Time: 下午3:16
 */

namespace Payment\Client;


use Payment\Common\PayException;
use Payment\Config;
use Payment\TransferContext;

class Transfer
{

    private static $supportChannel = [
        Config::ALI_TRANSFER,// 支付宝

        Config::WX_TRANSFER,// 微信

        'cmb_wallet',// 招行一网通
        'applepay_upacp',// Apple Pay
    ];

    /**
     * 异步通知类
     * @var TransferContext
     */
    protected static $instance;

    protected static function getInstance($channel, $config)
    {
        if (is_null(self::$instance)) {
            static::$instance = new TransferContext();

            try {
                static::$instance->initTransfer($channel, $config);
            } catch (PayException $e) {
                throw $e;
            }
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