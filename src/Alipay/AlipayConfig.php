<?php
/**
 * @author: helei
 * @createTime: 2016-06-07 19:14
 * @description: 支付宝的静态配置信息
 */

namespace Payment\Alipay;


class AlipayConfig
{
    protected $config = [];

    public function __construct()
    {
        $this->config = [
            'partner'   => '2088xxxxxxx',// 合作伙伴身份（PID）
            'md5_key'   => 'xxxxxxxxxxxx',// MD5密钥
            'seller_email'   => 'xxxxxxxxxxxx',// 收款支付宝账号
            'input_charset'   => 'utf-8',// 编码方式
            'geteway_url'   => 'https://mapi.alipay.com/gateway.do?',// 支付宝的网关地址
            'rsa_private_key'   => __DIR__ . '/safekey/rsa_private_key.pem',// rsa私钥的路径
            'rsa_ali_public_key'    => __DIR__ . '/safekey/alipay_public_key.pem',// rsa 支付宝公钥的路径
            'cacert_path'   => __DIR__ . '/safekey/cacert.pem', //ca证书路径地址，用于curl中ssl校验
        ];
    }

    public function getPartner()
    {
        return $this->config['partner'];
    }

    public function getMd5Key()
    {
        return $this->config['md5_key'];
    }
    public function getSellerEmail()
    {
        return $this->config['seller_email'];
    }
    public function getInputCharset()
    {
        return $this->config['input_charset'];
    }
    public function getGetewayUrl()
    {
        return $this->config['geteway_url'];
    }
    public function getRsaPrivateKey()
    {
        return $this->config['rsa_private_key'];
    }
    public function getRsaAliPublicKey()
    {
        return $this->config['rsa_ali_public_key'];
    }
    public function getCacertPath()
    {
        return $this->config['cacert_path'];
    }
}