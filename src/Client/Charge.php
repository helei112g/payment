<?php
namespace Payment\Client;

use Payment\ChargeContext;
use Payment\Common\PayException;
use Payment\Config;

/**
 * @author: helei
 * @createTime: 2017-09-02 18:20
 * @description: 支付的客户端类
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 * Class Charge
 * @package Payment\Client
 *
 */
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

        Config::CMB_CHANNEL_APP,// 招行一网通
        'applepay_upacp',// Apple Pay
    ];

    /**
     * 支付实例
     * @var ChargeContext
     */
    protected static $instance;

    protected static function getInstance($channel, $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");
        
        if (is_null(self::$instance)) {
            static::$instance = new ChargeContext();
        }

        try {
            static::$instance->initCharge($channel, $config);
        } catch (PayException $e) {
            throw $e;
        }

        return static::$instance;
    }

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

        try {
            $instance = self::getInstance($channel, $config);

            $ret = $instance->charge($metadata);
        } catch (PayException $e) {
            throw $e;
        }

        return $ret;
    }
}
