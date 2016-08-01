<?php
/**
 * @author: helei
 * @createTime: 2016-08-01 18:20
 * @description:
 */

namespace Payment\Common\Weixin\Data;
use Payment\Common\PayException;


/**
 * Class ShortUrlData
 * @package Payment\Common\Weixin\Data
 *
 * @property string $code_url
 *
 * anthor helei
 */
class ShortUrlData extends WxBaseData
{

    protected function buildData()
    {
        $this->retData = [
            'appid' => trim($this->appId),
            'mch_id'    => trim($this->mchId),

            'long_url'  => $this->code_url,
            'nonce_str' => $this->nonceStr,
        ];
    }

    protected function checkDataParam()
    {

    }
}