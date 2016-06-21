<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 11:10
 * @description:
 */

namespace Payment\Wxpay;


use Payment\Wxpay\Helper\WxTradeType;

class WxConfig
{
    protected $config = [];

    public function __construct($tradeType = 'APP')
    {
        $this->config = [
            'mchid'   => 'xxxxxxxxxx',// 商户号
            'md5_key'   => 'xxxxxxxxxx',// 是在微信支付中心设置的秘钥key
            'geteway_url'   => 'https://api.mch.weixin.qq.com/',// 微信的网关
            'appsecret' => 'xxxxxxxxxxxxxxxxxx',//app秘钥  是微信公众号中心的秘钥
            'oauth_redirect_uri'  => '',//网页授权回调域名

            // 证书路径
            'sslcert_path'  => __DIR__ . '/safekey/apiclient_cert.pem',
            'sslkey_path'   => __DIR__ . '/safekey/apiclient_key.pem',
        ];

        if ($tradeType == WxTradeType::TYPE_IS_APP) {
            // 使用的APP支付交易方式，此时值为：微信开放平台审核通过的应用APPID
            // https://open.weixin.qq.com  在应用中心获得
            $this->config['appid'] = 'xxxxxxxxxxxxxxxxx';
        } else {
            // 支付方式是 NATIVE ,需要设置的appid是 微信分配的公众账号ID
            $this->config['appid'] = 'xxxxxxxxxxxxx';
        }
    }

    public function getAppId()
    {
        return $this->config['appid'];
    }

    public function getMchId()
    {
        return $this->config['mchid'];
    }
    public function getMd5Key()
    {
        return $this->config['md5_key'];
    }
    public function getGetewayUrl()
    {
        return $this->config['geteway_url'];
    }
    public function getAppSecret()
    {
        return $this->config['geteway_url'];
    }
    public function getOauthRedirectUri()
    {
        return urlencode($this->config['oauth_redirect_uri']);
    }

    public function getSslcertPath()
    {
        return $this->config['sslcert_path'];
    }
    public function getSslkeyPath()
    {
        return $this->config['sslkey_path'];
    }
}