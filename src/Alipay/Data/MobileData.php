<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 09:28
 * @description: 移动支付 数据结构
 */

namespace Payment\Alipay\Data;


use Payment\Utils\RsaEncrypt;

class MobileData extends PayBaseData
{
    public function __construct()
    {
        parent::__construct();
        $this->values = [
            'partner'   => $this->config->getPartner(),
            'seller_id' => $this->config->getSellerEmail(),
            'service'   => 'mobile.securitypay.pay',
            'payment_type'  => 1,
            '_input_charset'    => $this->config->getInputCharset(),
            'sign_type' => 'RSA',
            'goods_type'    => 1,
        ];
    }

    /**
     * 客户端号
     * @param $app_id
     * @author helei
     */
    public function setAppId($app_id)
    {
        $this->values['app_id'] = $app_id;
    }

    /**
     * 客户端号
     * @author helei
     */
    public function getAppId()
    {
        if (array_key_exists('app_id', $this->values)) {
            return $this->values['app_id'];
        }

        return null;
    }

    /**
     * 客户端来源
     * @param $appenv
     * @author helei
     */
    public function setAppEnv($appenv)
    {
        $this->values['appenv'] = $appenv;
    }

    /**
     * 客户端来源
     * @author helei
     */
    public function getAppEnv()
    {
        if (array_key_exists('appenv', $this->values)) {
            return $this->values['appenv'];
        }

        return null;
    }

    /**
     * 是否发起实名校验
     * - T F  可取值
     * @param $rn_check
     * @author helei
     */
    public function setRnCheck($rn_check)
    {
        $this->values['rn_check'] = $rn_check;
    }

    /**
     * 获取 是否发起实名校验 的状态
     * @author helei
     */
    public function getRnCheck()
    {
        if (array_key_exists('rn_check', $this->values)) {
            return $this->values['rn_check'];
        }

        return null;
    }

    /**
     * 开放平台返回的包含账户信息的token
     * @author helei
     */
    public function setExternToken($extern_token)
    {
        $this->values['extern_token'] = $extern_token;
    }

    /**
     * 授权令牌
     * @author helei
     */
    public function getExternToken()
    {
        if (array_key_exists('extern_token', $this->values)) {
            return $this->values['extern_token'];
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
     * 由于移动支付接口，需要增加value的引号。通过该函数，为每个值增加引号
     * @author helei
     */
    public function handleData($values)
    {
        foreach ($values as $key => $value) {
            // 为每一个值增加引号

            $this->values[$key] = '"' . $value . '"';
        }

        return $this->values;
    }

    /**
     * 数据签名，   RSA签名方式
     * @param $prestr
     * @return string
     * @author helei
     */
    protected function makeSign($prestr)
    {
        $private_key = file_get_contents($this->config->getRsaPrivateKey());
        $rsa = new RsaEncrypt($private_key);

        return urlencode($rsa->encrypt($prestr));
    }
}