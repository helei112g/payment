<?php
/**
 * @author: helei
 * @createTime: 2016-08-04 10:30
 * @description:
 */

namespace Payment\Query;


use Payment\Common\Weixin\Data\QueryData;
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
        return QueryData::class;
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
            'CURLOPT_SSLCERT'   => $this->config->certPath,
            'CURLOPT_SSLKEY'    => $this->config->keyPath,
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
}