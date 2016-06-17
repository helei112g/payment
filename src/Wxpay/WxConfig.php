<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 11:10
 * @description:
 */

namespace Payment\Wxpay;


class WxConfig
{
    protected $config = [];

    public function __construct()
    {
        $this->config = [
            'appid'   => 'wxea45855be2709e6a',//公众账号ID
            'openid'   => '8a623c01c4c178bc8f27b0c15f73c580',// 开发者id
            'mchid'   => '1283888901',// 商户号
            'md5_key'   => 'aiyuketongweiguoji13049876543210',// 秘钥key
            'geteway_url'   => 'https://api.mch.weixin.qq.com/',// 微信的网关
            'appsecret' => '',//app秘钥
            'oauth_redirect_uri'  => '',//网页授权回调域名

            // 证书路径
            'sslcert_path'  => __DIR__ . '/safekey/apiclient_cert.pem',
            'sslkey_path'   => __DIR__ . '/safekey/apiclient_key.pem',
        ];
    }

    public function getOauthRedirectUri()
    {
        return urlencode($this->config['oauth_redirect_uri']);
    }

    public function getAppId()
    {
        return $this->config['appid'];
    }

    public function getOpenId()
    {
        return $this->config['openid'];
    }

    public function getMchId()
    {
        return $this->config['mchid'];
    }
    public function getMd5Key()
    {
        return $this->config['md5_key'];
    }

    public function getCurlTimeout()
    {
        return $this->config['curl_timeout'];
    }

    public function getGetewayUrl()
    {
        return $this->config['geteway_url'];
    }
}