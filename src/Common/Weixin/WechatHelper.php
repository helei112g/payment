<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/26
 * Time: 下午5:39
 */

namespace Payment\Common\Weixin;


use Payment\Common\PayException;
use Payment\Common\Weixin\Data\WxBaseData;
use Payment\Common\WxConfig;
use Payment\Utils\Curl;
use Payment\Utils\DataParser;

class WechatHelper extends WxBaseData
{

    public function getSandboxSignKey()
    {
        $this->setSign();

        $xml = DataParser::toXml($this->getData());

        $url = WxConfig::SANDBOX_URL;

        $curl = new Curl();
        $responseTxt = $curl->set([
            'CURLOPT_HEADER'    => 0
        ])->post($xml)->submit($url);

        if ($responseTxt['error']) {
            throw new PayException('网络发生错误，请稍后再试curl返回码：' . $responseTxt['message']);
        }
        // 格式化为数组
        $retData = DataParser::toArray($responseTxt['body']);
        if ($retData['return_code'] != 'SUCCESS') {
            throw new PayException('微信返回错误提示:' . $retData['return_msg']);
        }

        return $retData['sandbox_signkey'];
    }

    protected function buildData()
    {
        $this->retData = [
            'mch_id'    => $this->mchId,
            'nonce_str' => $this->nonceStr,
        ];
    }

    protected function checkDataParam()
    {
        // TODO: Implement checkDataParam() method.
    }
}