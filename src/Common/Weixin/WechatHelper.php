<?php
namespace Payment\Common\Weixin;

use GuzzleHttp\Client;
use Payment\Common\PayException;
use Payment\Common\Weixin\Data\WxBaseData;
use Payment\Common\WxConfig;
use Payment\Utils\Curl;
use Payment\Utils\DataParser;
use Payment\Utils\StrUtil;

/**
 * 微信支付沙盒测试
 * Class WechatHelper
 * @package Payment\Common\Weixin
 */
class WechatHelper extends WxBaseData
{
    // 沙盒测试url
    const SANDBOX_URL = 'https://api.mch.weixin.qq.com/sandboxnew/pay/getsignkey';

    /**
     * 获取沙盒的签名信息
     * @return mixed
     * @throws PayException
     */
    public function getSandboxSignKey()
    {
        $this->setSign();

        $xml = DataParser::toXml($this->getData());

        $url = self::SANDBOX_URL;

        $client = new Client([
            'timeout' => '10.0'
        ]);
        $options = [
            'body' => $xml,
            'http_errors' => false
        ];

        $response = $client->request('POST', self::SANDBOX_URL, $options);

        if ($response->getStatusCode() != '200') {
            throw new PayException('网络发生错误，请稍后再试curl返回码：' . $response->getReasonPhrase());
        }
        // 格式化为数组
        $retData = DataParser::toArray($response->getBody()->getContents());
        if (strtoupper($retData['return_code']) != 'SUCCESS') {
            throw new PayException('微信返回错误提示:' . $retData['return_msg']);
        }

        return $retData['sandbox_signkey'];
    }

    /**
     * 构建请求的数据
     */
    protected function buildData()
    {
        $this->retData = [
            'mch_id'    => $this->mchId,
            'nonce_str' => StrUtil::getNonceStr(),
        ];
    }

    protected function checkDataParam()
    {
        // TODO: Implement checkDataParam() method.
    }
}
