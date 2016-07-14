<?php
/**
 * @author: helei
 * @createTime: 2016-07-14 17:42
 * @description: 暴露给客户端调用的接口
 */

namespace Pyament;


use Payment\Charge\Ali\AliCharge;
use Payment\Charge\ChargeStrategy;
use Payment\Charge\Weixin\WxCharge;
use Payment\Common\PayException;

class ChargeContext
{
    /**
     * 支付的渠道
     * @var ChargeStrategy
     */
    protected $channel;

    /**
     * 设置对应的支付渠道
     * @param string $channel
     *  - @see Config
     * @throws PayException
     * @author helei
     */
    public function setPayChannel($channel)
    {
        switch ($channel) {
            case Config::ALI_CHANNEL:
                $this->channel = new AliCharge();
                break;
            case Config::WX_CHANNEL:
                $this->channel = new WxCharge();
                break;
            default :
                throw new PayException();
        }

    }

    public function cretea()
    {
        if (is_null($this->channel)) {
            return ;
        }

        $this->channel->charge();
    }
}