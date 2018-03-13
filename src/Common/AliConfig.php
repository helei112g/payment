<?php
namespace Payment\Common;

use Payment\Utils\ArrayUtil;
use Payment\Utils\StrUtil;

/**
 * @author: helei
 * @createTime: 2016-07-15 14:56
 * @description: 支付宝配置文件  所有支付的配置文件，均需要继承 ConfigInterface 这个接口
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
final class AliConfig extends ConfigInterface
{
    // 支付宝的网关
    public $getewayUrl;

    // 支付宝分配给开发者的应用ID
    public $appId;

    // 	接口名称
    public $method;// 参考 定义的常量

    // 仅支持JSON
    public $format = 'JSON';

    // 用于同步通知的地址
    public $returnUrl;

    // 采用的编码
    public $charset = 'UTF-8';

    // 发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"
    public $timestamp;

    // 调用的接口版本，固定为：1.0
    public $version = '1.0';

    // 用于rsa加密的私钥文件路径
    public $rsaPrivateKey;

    // 用于rsa解密的支付宝公钥文件路径
    public $rsaAliPubKey;

    /**
     * 初始化配置文件
     * @param array $config
     * @throws PayException
     */
    protected function initConfig(array $config)
    {
        $config = ArrayUtil::paraFilter($config);// 过滤掉空值，下面不用在检查是否为空

        // 初始 支付宝网关地址
        $this->getewayUrl = 'https://openapi.alipay.com/gateway.do';
        if (isset($config['use_sandbox']) && $config['use_sandbox'] === true) {
            $this->getewayUrl = 'https://openapi.alipaydev.com/gateway.do';
        } else {
            $this->useSandbox = false;// 不是沙箱模式
        }

        // 支付宝分配给开发者的应用ID
        if (key_exists('app_id', $config) && is_numeric($config['app_id'])) {
            $this->appId = $config['app_id'];
        } else {
            throw new PayException('支付宝应用ID错误，请检查');
        }

        // 初始 支付宝异步通知地址，可为空
        if (key_exists('notify_url', $config)) {
            $this->notifyUrl = $config['notify_url'];
        }

        // 初始 支付宝 同步通知地址，可为空
        if (key_exists('return_url', $config)) {
            $this->returnUrl = $config['return_url'];
        }

        // 初始 支付宝 签名方式，默认为：RSA
        if (key_exists('sign_type', $config) && in_array($config['sign_type'], ['RSA', 'RSA2'])) {
            $this->signType = $config['sign_type'];
        } else {
            throw new PayException('目前支付宝仅支持RSA2和RSA，推荐使用RSA2');
        }

        // 新版本，需要提供独立的支付宝公钥信息。每一个应用，公钥都不相同
        if (key_exists('ali_public_key', $config) && ! empty($config['ali_public_key'])) {
            $this->rsaAliPubKey = StrUtil::getRsaKeyValue($config['ali_public_key'], 'public');
        } else {
            throw new PayException('请提供支付宝对应的rsa公钥');
        }
        if (empty($this->rsaAliPubKey)) {
            throw new PayException('支付宝公钥生成错误，如果你使用的是文件方式配置，建议修改为字符串配置');
        }

        // 初始 RSA私钥文件 需要检查该文件是否存在
        if (key_exists('rsa_private_key', $config) && ! empty($config['rsa_private_key'])) {
            $this->rsaPrivateKey = StrUtil::getRsaKeyValue($config['rsa_private_key'], 'private');
        } else {
            throw new PayException('请提供商户的rsa私钥文件');
        }
        if (empty($this->rsaPrivateKey)) {
            throw new PayException('支付宝rsa私钥生成错误，如果你使用的是文件方式配置，建议修改为字符串配置');
        }

        // 	发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"  需要正确设置时区
        $this->timestamp = date('Y-m-d H:i:s', time());

        // 设置禁止使用的支付方式
        if (key_exists('limit_pay', $config) && is_array($config['limit_pay'])) {
            $this->limitPay = implode(',', $config['limit_pay']);
        }

        // 是否要求返回支付返回的原始数据
        if (key_exists('return_raw', $config)) {
            $this->returnRaw = filter_var($config['return_raw'], FILTER_VALIDATE_BOOLEAN);
        }
    }
}
