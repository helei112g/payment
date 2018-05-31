<?php
/**
 * Created by ChaXian.
 * User: Bing
 * Date: 2018/5/24
 * Time: 11:39
 * Description:检查配置信息
 */

namespace Payment\Common;


use Payment\Utils\ArrayUtil;

class AliDirectConfig extends ConfigInterface
{
    protected $returnRaw = false;

    public $signType = 'MD5';

    public $useSandbox = false;

    public $getewayUrl = 'https://mapi.alipay.com/gateway.do';

    public $service = 'create_direct_pay_by_user';

    public $charset = 'utf-8';

    public $extend_param = '0tv^eh8a';

    public $key;

    public $partner;

    public $seller_email;

    public $returnUrl;

    protected function initConfig(array $config)
    {
        $config = ArrayUtil::paraFilter($config);// 过滤掉空值，下面不用在检查是否为空
        // TODO: Implement initConfig() method.

        // 初始 支付宝异步通知地址，可为空
        if (key_exists('notify_url', $config)) {
            $this->notifyUrl = $config['notify_url'];
        }

        // 初始 支付宝 同步通知地址，可为空
        if (key_exists('return_url', $config)) {
            $this->returnUrl = $config['return_url'];
        }

        // 初始 商户email, 可为空
        if (key_exists('alipay_account', $config)) {
            $this->seller_email = $config['alipay_account'];
        }

        // 支付宝 签名密钥
        if (key_exists('alipay_key', $config)) {
            $this->key = $config['alipay_key'];
        } else {
            throw new PayException('请提供加密密钥');
        }

        // 支付宝商户ID
        if (key_exists('alipay_partner', $config) && is_numeric($config['alipay_partner'])) {
            $this->partner = $config['alipay_partner'];
        } else {
            throw new PayException('商户ID错误，请检查');
        }
    }

}