<?php

namespace Payment\Charge\Ali;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Charge\QrChargeData;
use Payment\Common\AliConfig;
use Payment\Common\PayException;
use Payment\Utils\Curl;
use Payment\Utils\DataParser;

/**
 * 支付宝扫码支付
 *
 * Class AliQrCharge
 * @package Payment\Charge\Weixin
 *
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */
class AliQrCharge extends AliBaseStrategy
{

    protected function getBuildDataClass()
    {
        $this->config->method = AliConfig::ALI_TRADE_QR;
        return QrChargeData::class;
    }

    /**
     * 处理扫码支付的返回值
     * @param array $ret
     *
     * @throws PayException
     * @return string  可生产二维码的uri
     * @author helei
     */
    protected function retData(array $ret)
    {
        $url = parent::retData($ret);

        // 发起网络请求
        $curl = new Curl();
        $responseTxt = $curl->set([
            'CURLOPT_SSL_VERIFYPEER'    => true,
            'CURLOPT_SSL_VERIFYHOST'    => 2,
            'CURLOPT_CAINFO'    => $this->config->cacertPath,
            'CURLOPT_HEADER'    => 0,// 为了便于解析，将头信息过滤掉
        ])->get($url);

        if ($responseTxt['error']) {
            throw new PayException('网络发生错误，请稍后再试');
        }

        $body = $responseTxt['body'];

        $data = json_decode($body, true)['alipay_trade_precreate_response'];
        if ($data['code'] != 10000) {
            throw new PayException($data['sub_msg']);
        }

        return $data['qr_code'];
    }
}