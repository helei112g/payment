<?php
/**
 * @author: helei
 * @createTime: 2016-06-08 10:58
 * @description:
 */

namespace Payment\Alipay\Data;


class TradeQueryData extends AliBaseData
{
    // 单笔交易查询接口
    public function __construct()
    {
        parent::__construct();
        $this->values = [
            'service'   => 'single_trade_query',
            'partner'   => $this->config->getPartner(),
            '_input_charset'   => $this->config->getInputCharset(),
            'sign_type' => strtoupper('md5'),
        ];
    }

    /**
     * 进行签名算法   使用md5
     * @param string $prestr
     * @return string
     * @author helei
     */
    protected function makeSign($prestr)
    {
        $prestr .= $this->config->getMd5Key();

        return md5($prestr);
    }

    /**
     * 支付宝交易号  最短 16 位，最长 64 位
     * @param string $out_trade_no
     * @author helei
     */
    public function setTradeNo($out_trade_no)
    {
        $this->values['trade_no'] = $out_trade_no;
    }

    /**
     * 支付宝交易号  最短 16 位，最长 64 位
     * @return null
     * @author helei
     */
    public function getTradeNo()
    {
        if (array_key_exists('trade_no', $this->values)) {
            return $this->values['trade_no'];
        }

        return null;
    }

    /**
     * 商户网站唯一订单号
     * @param string $out_trade_no
     * @author helei
     */
    public function setOutTradeNo($out_trade_no)
    {
        $this->values['out_trade_no'] = $out_trade_no;
    }

    /**
     * 商户网站唯一订单号
     * @return null
     * @author helei
     */
    public function getOutTradeNo()
    {
        if (array_key_exists('out_trade_no', $this->values)) {
            return $this->values['out_trade_no'];
        }

        return null;
    }
}