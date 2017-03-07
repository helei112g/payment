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

        $refund = new TransferContext();

        try {
            $refund->initTransfer($channel, $config);

            $ret = $refund->transfer($metadata);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }
}