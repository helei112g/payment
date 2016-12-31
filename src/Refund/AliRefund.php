<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 10:36
 * @description:
 */

namespace Payment\Refund;


use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\RefundData;
use Payment\Common\AliConfig;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\Curl;

class AliRefund extends AliBaseStrategy
{

    protected function getBuildDataClass()
    {
        $this->config->method = AliConfig::ALI_TRADE_REFUDN;
        return RefundData::class;
    }

    /**
     * 返回数据
     * @param array $data
     * @return array|string
     */
    protected function retData(array $data)
    {
        $url = parent::retData($data);
        $version = $this->config->version;

        if ($version === Config::ALI_API_VERSION) {
            $data = $this->request($url);

            return $this->createBackData($data);
        } else {
            return $url;
        }
    }

    /**
     * 处理返回的数据
     * @param array $data
     * @return array
     * @author helei
     */
    protected function createBackData(array $data)
    {
        // 新版本
        if ($data['code'] !== '10000') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $data['sub_msg']
            ];
        }

        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'transaction_id'   => $data['trade_no'],
                'order_no'  => $data['out_trade_no'],
                // 'refund_no' => $data['out_refund_no'],  这里比较坑爹，支付宝没有返回商户提交的退款单号
                // 'refund_id' => $data['refund_id'],  退款单号也没有
                'refund_fee'    => $data['refund_fee'],// 返回的总金额，这里支付宝会累计
                'buyer_id'   => $data['buyer_user_id'],
                'logon_id'   => $data['buyer_logon_id'],
                'refund_time'=> $data['gmt_refund_pay'],
            ],
        ];

        return $retData;
    }

    protected function request($url)
    {
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

        // 格式化为数组
        $retData = json_decode($body, true);

        return $retData['alipay_trade_refund_response'];
    }
}