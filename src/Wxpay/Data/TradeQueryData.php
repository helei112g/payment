<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 15:26
 * @description:
 */

namespace Payment\Wxpay\Data;


class TradeQueryData extends WxBaseData
{
    public function __construct()
    {
        parent::__construct();
        $this->values = [
            'appid' => $this->config->getAppId(),
            'mch_id'    => $this->config->getMchId(),
        ];
    }

    /**
     * 设置支付宝的订单号，优先使用
     * @param string $value
     **/
    public function setTransactionId($value)
    {
        $this->values['transaction_id'] = $value;
    }
    /**
     * 获取支付宝的订单号，优先使用的值
     * @return string 值
     **/
    public function getTransactionId()
    {
        if (array_key_exists('transaction_id', $this->values)) {
            return $this->values['transaction_id'];
        }

        return null;
    }

    /**
     * 设置商户系统内部的订单号，当没提供transaction_id时需要传这个。
     * @param string $value
     **/
    public function setOutTradeNo($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    /**
     * 获取商户系统内部的订单号，当没提供transaction_id时需要传这个。的值
     * @return string 值
     **/
    public function getOutTradeNo()
    {
        if (array_key_exists('out_trade_no', $this->values)) {
            return $this->values['out_trade_no'];
        }

        return null;
    }
}