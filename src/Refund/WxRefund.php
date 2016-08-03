<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 10:51
 * @description:
 */

namespace Payment\Refund;


use Payment\Common\PayException;
use Payment\Common\Weixin\Data\RefundData;
use Payment\Common\Weixin\WxBaseStrategy;
use Payment\Common\WxConfig;
use Payment\Utils\Curl;
use Payment\Utils\DataParser;

/**
 * Class WxRefund
 * 微信退款操作
 * @package Payment\Refund
 * anthor helei
 */
class WxRefund extends WxBaseStrategy
{

    protected function getBuildDataClass()
    {
        return RefundData::class;
    }

    /**
     * 微信退款接口，需要用到相关加密文件及证书，需要重新进行curl的设置
     * @param string $xml
     * @param string $url
     * @return array
     * @author helei
     */
    protected function curlPost($xml, $url)
    {
        $curl = new Curl();
        $responseTxt = $curl->set([
            'CURLOPT_HEADER'    => 0,
            'CURLOPT_SSL_VERIFYHOST'    => false,
            'CURLOPT_SSLCERTTYPE'   => 'PEM', //默认支持的证书的类型，可以注释
            'CURLOPT_SSLCERT'   => $this->config->certPath,
            'CURLOPT_SSLKEY'    => $this->config->keyPath,
            'CURLOPT_CAINFO'    => $this->config->cacertPath,
        ])->post($xml)->submit($url);

        return $responseTxt;
    }

    /**
     * 处理退款的返回数据
     * @param array $ret
     * @return mixed
     * @author helei
     */
    protected function retData(array $ret)
    {
        // 请求失败，可能是网络
        if ($ret['return_code'] != 'SUCCESS') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $ret['return_msg']
            ];
        }

        // 业务失败
        if ($ret['result_code'] != 'SUCCESS') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $ret['err_code_des']
            ];
        }

        return $this->createBackData($ret);
    }

    /**
     * 处理返回的数据
     * @param array $data
     * @return array
     * @author helei
     */
    protected function createBackData(array $data)
    {
        // 将订单总金额金额处理为元
        $total_fee = bcdiv($data['total_fee'], 100, 2);
        // 将订单退款金额处理为元
        $refund_fee = bcdiv($data['refund_fee'], 100, 2);

        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'transaction_id'   => $data['transaction_id'],
                'order_no'  => $data['out_trade_no'],
                'refund_no' => $data['out_refund_no'],
                'refund_id' => $data['refund_id'],
                'refund_fee'    => $refund_fee,
                'amount'   => $total_fee,
            ],
        ];

        return $retData;
    }

    /**
     * 返回退款的url
     * @return null|string
     * @author helei
     */
    protected function getReqUrl()
    {
        return WxConfig::REFUND_URL;
    }
}