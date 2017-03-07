<?php
/**
 * @author: helei
 * @createTime: 2016-08-04 10:30
 * @description:
 */

namespace Payment\Query\Wx;

use Payment\Common\Weixin\Data\Query\TransferQueryData;
use Payment\Common\Weixin\WxBaseStrategy;
use Payment\Common\WxConfig;
use Payment\Utils\Curl;

/**
 * Class WxTransferQuery
 * @package Payment\Query
 * anthor helei
 */
class WxTransferQuery extends WxBaseStrategy
{
    protected function getBuildDataClass()
    {
        return TransferQueryData::class;
    }

    /**
     * 使用证书方式进行查询
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
            'CURLOPT_SSLCERT'   => $this->config->appCertPem,
            'CURLOPT_SSLKEY'    => $this->config->appKeyPem,
            'CURLOPT_CAINFO'    => $this->config->cacertPath,
        ])->post($xml)->submit($url);

        return $responseTxt;
    }

    /**
     * 返回付款查询url
     * @return string
     * @author helei
     */
    protected function getReqUrl()
    {
        return WxConfig::TRANS_QUERY_URL;
    }

    /**
     * 处理通知的返回数据
     * @param array $data
     * @return mixed
     * @author helei
     */
    protected function retData(array $data)
    {
        if ($this->config->returnRaw) {
            return $data;
        }

        // 请求失败，可能是网络
        if ($data['return_code'] != 'SUCCESS') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $data['return_msg']
            ];
        }

        // 业务失败
        if ($data['result_code'] != 'SUCCESS') {
            return $retData = [
                'is_success'    => 'F',
                'error' => $data['err_code_des']
            ];
        }

        // 正确
        return $this->createBackData($data);
    }

    /**
     * 返回数据给客户端
     * @param array $data
     * @return array
     * @author helei
     */
    protected function createBackData(array $data)
    {
        // 将金额处理为元
        $amount = bcdiv($data['payment_amount'], 100, 2);

        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'trans_no'   => $data['partner_trade_no'],// 商户单号
                'transaction_id'  => $data['detail_id'],// 付款单号
                'status'  => strtolower($data['status']),// 转账状态
                'reason'    => $data['reason'],// 失败原因
                'openid'   => $data['openid'],
                'payee_name'   => $data['transfer_name'],// 收款用户姓名
                'amount'   => $amount,
                'pay_date'   => $data['transfer_time'],
                'desc'   => $data['desc'],// 付款描述
            ],
        ];

        return $retData;
    }
}
