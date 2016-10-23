<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 14:56
 * @description: 支付宝配置文件  所有支付的配置文件，均需要继承 ConfigInterface 这个接口
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common;


use Payment\Utils\ArrayUtil;

final class AliConfig extends ConfigInterface
{
    // 支付宝的网关
    public $getewayUrl = 'https://mapi.alipay.com/gateway.do?';

    // 采用的编码
    public $inputCharset = 'UTF-8';

    // 合作者身份ID
    public $partner;

    // 用于加密的md5Key
    public $md5Key;

    // 用于异步通知的地址
    public $notifyUrl;

    // 用于同步通知的地址
    public $returnUrl;

    // 订单在支付宝服务器过期的时间，过期后无法支付
    public $timeExpire;

    // 用于rsa加密的私钥文件路径
    public $rsaPrivatePath;

    // 用于rsa解密的支付宝公钥文件路径
    public $rsaAliPubPath;

    // 安全证书的路径
    public $cacertPath;

    // 付款账号名
    public $account_name;

    // 付款账号 支付宝账号，邮箱或者手机
    public $account;

    // 加密方式 默认使用RSA
    public $signType;
    

    public function __construct(array $config)
    {
        // 初始化配置信息
        try {
            $this->initConfig($config);
        } catch (PayException $e) {
            throw $e;
        }

        $basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Ali' . DIRECTORY_SEPARATOR;
        $this->rsaAliPubPath = "{$basePath}alipay_public_key.pem";
        $this->cacertPath = "{$basePath}cacert.pem";
    }

    /**
     * 检查传入的配置文件信息是否正确
     * @param array $config
     * @throws PayException
     * @author helei
     */
    private function initConfig(array $config)
    {
        $config = ArrayUtil::paraFilter($config);

        // 初始 合作者身份ID
        if (key_exists('partner', $config) && strlen($config['partner']) == '16') {
            $this->partner = $config['partner'];
        } else {
            throw new PayException('合作者身份ID 以2088开头的16位纯数字组成');
        }

        // 初始 MD5 key
        if (key_exists('md5_key', $config) && !empty($config['md5_key'])) {
            $this->md5Key = $config['md5_key'];
        } else {
            throw new PayException('MD5 Key 不能为空，再支付宝后台可查看');
        }

        // 初始 RSA私钥文件 需要检查该文件是否存在
        if (key_exists('rsa_private_key', $config) && file_exists($config['rsa_private_key'])) {
            $this->rsaPrivatePath = $config['rsa_private_key'];
        } elseif ($config['sign_type'] === 'RSA') {
            throw new PayException('RSA加密时,必须提供RSA私钥文件，请确保在该路径下存在');
        }

        // 初始 支付宝异步通知地址，可为空
        if (key_exists('notify_url', $config) && !empty($config['notify_url'])) {
            $this->notifyUrl = $config['notify_url'];
        }

        // 初始 支付宝 同步通知地址，可为空
        if (key_exists('return_url', $config) && !empty($config['return_url'])) {
            $this->returnUrl = $config['return_url'];
        }

        // 初始 支付宝订单过期时间，可为空 取值范围：1m～15d
        // m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）
        if (key_exists('time_expire', $config) && !empty($config['time_expire'])) {
            $this->timeExpire = $config['time_expire'];
        }

        // 初始 支付宝网关地址
        if (key_exists('geteway_url', $config) && !empty($config['geteway_url'])) {
            $this->getewayUrl = $config['geteway_url'];
        }

        // 初始化 付款账号名，如果是企业转账接口，必须提供该值
        if (!empty($config['account_name'])) {
            $this->account_name = $config['account_name'];
        }

        // 初始化 付款账号，付款方的支付宝账号，支持邮箱和手机号2种格式。
        if (!empty($config['account'])) {
            $this->account = $config['account'];
        }

        $this->signType = 'RSA';
        // 初始化 加密方式,默认采用RSA
        if (!empty($config['sign_type'])) {
            $this->signType = strtoupper($config['sign_type']);
        }
    }
}