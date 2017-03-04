<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/4
 * Time: 下午5:40
 */

namespace Payment\Client;


use Payment\ChargeContext;
use Payment\Common\PayException;

class Charge
{
    private static $supportChannel = [
        'alipay_app',// 支付宝 APP 支付
        'alipay_wap', // 支付宝手机网页支付
        'alipay_pc_direct', // 支付宝电脑网站支付
        'alipay_qr', // 支付宝当面付，即支付宝扫码支付

        'wx_app',// 微信 APP 支付
        'wx_pub',// 微信公众号支付
        'wx_pub_qr',// 微信公众号扫码支付
        'wx_wap',// 微信 WAP 支付（此渠道仅针对特定客户开放）
        'wx_lite',// 微信小程序支付

        'cmb_wallet',// 招行一网通
        'applepay_upacp',// Apple Pay
    ];

    /**
     * @param string $channel
     * @param array $config
     * @param array $metadata
     *
     * @return mixed
     * @throws PayException
     */
    public static function pay($channel, $config, $metadata)
    {
        if (! in_array($channel, self::$supportChannel)) {
            throw new PayException('sdk当前不支持该支付渠道，当前仅支持：' . implode(',', self::$supportChannel));
        }

        $charge = new ChargeContext();

        try {
            $charge->initCharge($channel, $config);

            $ret = $charge->charge($metadata);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }
}