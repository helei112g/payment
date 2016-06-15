<?php
/**
 * @author: helei
 * @createTime: 2016-06-07 20:07
 * @description:
 */

namespace Payment\Alipay\Data;


class DirectData extends PayBaseData
{
    public function __construct()
    {
        parent::__construct();
        // 即时到账接口，默认设置的参数
        $this->values = [
            'service'   => 'create_direct_pay_by_user',
            'partner'   => $this->config->getPartner(),
            '_input_charset'   => $this->config->getInputCharset(),
            'sign_type'   => strtoupper('md5'),
            'payment_type'  => 1,
            'seller_id' => $this->config->getPartner(),
            'paymethod' => 'directPay',
            'goods_type'    => 1
        ];
    }

    /**
     * 设置 卖家支付宝账号
     * @param $seller_email
     * @author helei
     */
    public function setSellerEmail($seller_email)
    {
        $this->values['seller_email'] = $seller_email;
    }

    /**
     * 获取 卖家支付宝账号
     * @author helei
     */
    public function getSellerEmail()
    {
        if (array_key_exists('seller_email', $this->values)) {
            return $this->values['seller_email'];
        }

        return null;
    }

    /**
     * 设置 商品展示网址
     * @param $show_url
     * @author helei
     */
    public function setShowUrl($show_url)
    {
        $this->values['show_url'] = $show_url;
    }

    /**
     * 获取 商品展示的url
     * @return null
     * @author helei
     */
    public function getShowUrl()
    {
        if (array_key_exists('show_url', $this->values)) {
            return $this->values['show_url'];
        }

        return null;
    }

    /**
     * 设置 默认的支付方式
     * @param string $pay_method
     *  - creditPay（信用支付）
     *  - directPay（余额支付）
     * @author helei
     */
    public function setPayMethod($pay_method)
    {
        $this->values['paymethod'] = $pay_method;
    }

    /**
     * 获取 设置的支付方式
     * @author helei
     */
    public function getPayMethod()
    {
        if (array_key_exists('paymethod', $this->values)) {
            return $this->values['paymethod'];
        }

        return null;
    }

    /**
     * 客户端ip
     * @author helei
     */
    public function setExterInvokeIp($exter_invoke_ip)
    {
        $this->values['exter_invoke_ip'] = $exter_invoke_ip;
    }

    /**
     * 获取客户端ip
     * @return null
     * @author helei
     */
    public function getExterInvokeIp()
    {
        if (array_key_exists('exter_invoke_ip', $this->values)) {
            return $this->values['exter_invoke_ip'];
        }

        return null;
    }

    /**
     * 公用回传参数
     * @note 如果用户请求时传递了该参数，则返回给商户时会回传该参数
     *
     * @param $extra_common_param
     * @author helei
     */
    public function setExtraCommonParam($extra_common_param)
    {
        $this->values['extra_common_param'] = $extra_common_param;
    }

    /**
     * 获取公用的回传参数
     * @return null
     * @author helei
     */
    public function getExtraCommonParam()
    {
        if (array_key_exists('extra_common_param', $this->values)) {
            return $this->values['extra_common_param'];
        }

        return null;
    }

    /**
     * 设置扫码方式
     * @param $qr_pay_mode
     *  - 0：订单码-简约前置模式，对应iframe宽度不能小于600px，高度不能小于300px；
     *  - 1：订单码-前置模式，对应iframe宽度不能小于300px，高度不能小于600px；
     *  - 3：订单码-迷你前置模式，对应iframe宽度不能小于75px，高度不能小于75px。
     *  - 4：订单码-可定义宽度的嵌入式二维码，商户可根据需要设定二维码的大小。
     *  - 2：订单码-跳转模式
     * @author helei
     */
    public function setQrPayMode($qr_pay_mode)
    {
        $this->values['qr_pay_mode'] = $qr_pay_mode;
    }

    /**
     * 获取 扫码支付方式
     * @return null
     * @author helei
     */
    public function getQrPayMode()
    {
        if (array_key_exists('qr_pay_mode', $this->values)) {
            return $this->values['qr_pay_mode'];
        }

        return null;
    }

    /**
     * 生成签名结果  即时接口，使用md5进行签名
     * @param string $prestr
     * @return string
     * @author helei
     */
    protected function makeSign($prestr)
    {
        $prestr .= $this->config->getMd5Key();

        return md5($prestr);
    }
}