<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/6
 * Time: 下午5:44
 */

namespace Payment\Charge\Wx;


use Payment\Common\BaseData;
use Payment\Common\Weixin\Data\Charge\WapChargeData;
use Payment\Common\Weixin\WxBaseStrategy;

/**
 * 微信h5支付
 * Class WxWapCharge
 * @package Payment\Charge\Weixin
 */
class WxWapCharge extends WxBaseStrategy
{

    protected function getBuildDataClass()
    {
        $this->config->tradeType = 'MWEB';
        return WapChargeData::class;
    }

    /**
     * 这里由于
     * @param array $ret
     * @return mixed
     */
    protected function retData(array $ret)
    {
        if ($this->config->returnRaw) {
            return $ret;
        }

        $wabUrl = $ret['mweb_url'];
        if ($this->config->returnUrl) {
            $wabUrl .= '&redirect_url=' . urlencode($this->config->returnUrl);
        }

        return $wabUrl;
    }
}