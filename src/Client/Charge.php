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
use Payment\Config;

class Charge
{
    private static $supportChannel = [
        Config::ALI_CHANNEL_APP,// 支付宝 APP 支付
        Config::ALI_CHANNEL_WAP, // 支付宝手机网页支付
        Config::ALI_CHANNEL_WEB, // 支付宝电脑网站支付
        Config::ALI_CHANNEL_QR, // 支付宝当面付-扫码支付
        Config::ALI_CHANNEL_BAR,// 支付宝当面付-条码支付

        Config::WX_CHANNEL_APP,// 微信 APP 支付
        Config::WX_CHANNEL_PUB,// 微信公众号支付
        Config::WX_CHANNEL_QR,// 微信公众号扫码支付
        Config::WX_CHANNEL_BAR,// 微信刷卡支付
        Config::WX_CHANNEL_WAP,// 微信 WAP 支付（此渠道仅针对特定客户开放）
        Config::WX_CHANNEL_LITE,// 微信小程序支付

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
    public static function run($channel, $config, $metadata)
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