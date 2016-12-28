<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 15:43
 * @description:
 */

namespace Payment\Trans;


use Payment\Common\Weixin\Data\TransferData;
use Payment\Common\Weixin\WxBaseStrategy;
use Payment\Common\WxConfig;
use Payment\Utils\Curl;

/**
 * 微信企业付款接口
 * Class WxTransfer
 * @package Payment\Trans
 * anthor helei
 */
class WxTransfer extends WxBaseStrategy
{

    protected function getBuildDataClass()
    {
        return TransferData::class;
    }

    /*
     * 返回转款的url
     */
    protected function getReqUrl()
    {
        return WxConfig::TRANSFERS_URL;
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
     * 转款的返回数据
     * @param array $ret
     * @return mixed
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
     * 返回数据
     * @param array $data
     * @return array
     */
    protected function createBackData(array $data)
    {
        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'trans_no'   => $data['partner_trade_no'],
                'trans_id'  => $data['payment_no'],
                'payment_time' => $data['payment_time'],// 企业付款成功时间  2015-05-19 15:26:59
            ],
        ];

        return $retData;
    }
}