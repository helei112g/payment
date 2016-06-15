<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 10:01
 * @description: 支付接口的基础数据
 */

namespace Payment\Alipay\Data;


abstract class PayBaseData extends AliBaseData
{
    /**
     * 商户订单号  需要1-64位
     * @param string $out_trade_no
     * @author helei
     */
    public function setOutTradeNo($out_trade_no)
    {
        $this->values['out_trade_no'] = $out_trade_no;
    }

    /**
     * 获取商户的订单号
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

    /**
     * 设置 商品名称
     * @param $subject
     * @author helei
     */
    public function setSubject($subject)
    {
        $this->values['subject'] = $subject;
    }

    /**
     * 获取商品名称
     * @return null
     * @author helei
     */
    public function getSubject()
    {
        if (array_key_exists('subject', $this->values)) {
            return $this->values['subject'];
        }

        return null;
    }

    /**
     * 设置 支付类型
     * @param $payment_type
     * @author helei
     */
    public function setPaymentType($payment_type)
    {
        $this->values['payment_type'] = $payment_type;
    }

    /**
     * 获取支付类型
     * @return null
     * @author helei
     */
    public function getPaymentType()
    {
        if (array_key_exists('payment_type', $this->values)) {
            return $this->values['payment_type'];
        }

        return null;
    }

    /**
     * 设置 卖家支付宝用户号 是以2088开头的纯16位数字
     * @param $seller_id
     * @author helei
     */
    public function setSellerId($seller_id)
    {
        $this->values['seller_id'] = $seller_id;
    }

    /**
     * seller_email是支付宝登录账号，格式一般是邮箱或手机号。
     * @return null
     * @author helei
     */
    public function getSellerId()
    {
        if (array_key_exists('seller_id', $this->values)) {
            return $this->values['seller_id'];
        }

        return null;
    }

    /**
     * 设置 交易金额
     * @param $total_fee
     * @author helei
     */
    public function setTotalFee($total_fee)
    {
        $this->values['total_fee'] = $total_fee;
    }

    /**
     * 获取 交易金额
     * @return null
     * @author helei
     */
    public function getTotalFee()
    {
        if (array_key_exists('total_fee', $this->values)) {
            return $this->values['total_fee'];
        }

        return null;
    }

    /**
     * 商品描述
     * @param string $body
     * @author helei
     */
    public function setBody($body)
    {
        $this->values['body'] = $body;
    }

    /**
     * 获取商品的描述
     * @return null
     * @author helei
     */
    public function getBody()
    {
        if (array_key_exists('body', $this->values)) {
            return $this->values['body'];
        }

        return null;
    }

    /**
     * 设置 超时时间
     *
     * @param string $it_b_pay
     * @note 设置未付款交易的超时时间，一旦超时，该笔交易就会自动被关闭。
     *  取值范围：1m～15d。  m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。
     * @author helei
     */
    public function setItBPay($it_b_pay)
    {
        $this->values['it_b_pay'] = $it_b_pay . 'm';
    }

    /**
     * 获取超时时间
     * @author helei
     */
    public function getItBPay()
    {
        if (array_key_exists('it_b_pay', $this->values)) {
            return $this->values['it_b_pay'];
        }

        return null;
    }

    /**
     * 设置 商品类型
     * @param  string $goods_type
     *  - 1表示实物类商品
     *  - 0表示虚拟类商品
     * @author helei
     */
    public function setGoodsType($goods_type)
    {
        $this->values['goods_type'] = $goods_type;
    }

    /**
     * 获取 商品类型
     * @author helei
     */
    public function getGoodsType()
    {
        if (array_key_exists('goods_type', $this->values)) {
            return $this->values['goods_type'];
        }

        return null;
    }
}