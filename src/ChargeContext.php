<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:42
 * @description: 暴露给客户端调用的接口
 */

namespace Pyament;


use Payment\Charge\Ali\AliAppCharge;
use Payment\Charge\Ali\AliWapCharge;
use Payment\Charge\Ali\AliWebCharge;
use Payment\Charge\ChargeStrategy;
use Payment\Charge\Weixin\WxAppCharge;
use Payment\Charge\Weixin\WxPubCharge;
use Payment\Charge\Weixin\WxWebCharge;
use Payment\Common\PayException;

class ChargeContext
{
    /**
     * 支付的渠道
     * @var ChargeStrategy
     */
    protected $payWay;


    /**
     * 设置对应的支付渠道
     * @param string $channel 支付渠道
     *  - @see Config
     * @param array $config 配置文件
     * @throws PayException
     * @author helei
     */
    public function initCharge($channel, array $config)
    {
        switch ($channel) {
            case Config::ALI_CHANNEL_WEB:
                $this->payWay = new AliWebCharge($config);
                break;
            case Config::ALI_CHANNEL_WAP:
                $this->payWay = new AliWapCharge($config);
                break;
            case Config::ALI_CHANNEL_APP:
                $this->payWay = new AliAppCharge($config);
                break;
            case Config::WX_CHANNEL_WEB:
                $this->payWay = new WxWebCharge($config);
                break;
            case Config::WX_CHANNEL_PUB:
                $this->payWay = new WxPubCharge($config);
                break;
            case Config::WX_CHANNEL_APP:
                $this->payWay = new WxAppCharge($config);
                break;
            default :
                throw new PayException('当前仅支持：支付宝 与 微信');
        }
    }

    /**
     * 通过环境类调用支付
     * @param array $data
     * @author helei
     */
    public function charge(array $data)
    {
        if (is_null($this->payWay)) {
            return ;
        }

        $this->payWay->charge($data);
    }
}