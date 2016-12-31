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
    public $getewayUrl;

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
    public $signType = 'RSA';

    // ================= 支付宝 2.0 新接口 支持参数 ================= //

    // 	支付宝分配给开发者的应用ID
    public $appId;

    // 仅支持JSON
    public $format = 'JSON';

    // 调用的接口版本，固定为：1.0
    public $version = '1.0';

    // 发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"
    public $timestamp;

    // 	接口名称
    public $method;// 参考 定义的常量

    // 支付宝的新版接口名称常量定义
    // app 支付
    const ALI_TRADE_APP = 'alipay.trade.app.pay';

    // wap 支付
    const ALI_TRADE_WAP = 'alipay.trade.wap.pay';

    // 扫码支付
    const ALI_TRADE_QR = 'alipay.trade.precreate';

    // 统一收单线下交易查询
    const ALI_TRADE_QUERY = 'alipay.trade.query';

    // 统一收单交易退款接口
    const ALI_TRADE_REFUDN = 'alipay.trade.refund';

    // 统一收单交易退款查询  未完成
    const ALI_REFUND_QUERY = 'alipay.trade.fastpay.refund.query';

    public function __construct(array $config)
    {
        // 初始化配置信息
        try {
            $this->initConfig($config);
        } catch (PayException $e) {
            throw $e;
        }

        $basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Ali' . DIRECTORY_SEPARATOR;

        if (!isset($config['ali_version'])) {// 兼容老版本
            $this->rsaAliPubPath = "{$basePath}alipay_public_key.pem";
        }

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

        if (isset($config['ali_version']) && $this->version === $config['ali_version']) {
            // 新版本 支付宝 支付
            $this->initConfigAli2_0($config);
        } else {
            $this->initConfigAli1_0($config);
        }

        // 初始 RSA私钥文件 需要检查该文件是否存在
        if (key_exists('rsa_private_key', $config) && file_exists($config['rsa_private_key'])) {
            $this->rsaPrivatePath = $config['rsa_private_key'];
        } elseif ($this->signType === 'RSA') {
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

    }

    /**
     * 支付宝老版接口。 支付宝可能慢慢放弃支持
     * @param array $config
     *
     * @throws PayException
     */
    private function initConfigAli1_0(array $config)
    {
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

        // 初始化 付款账号名，如果是企业转账接口，必须提供该值
        if (!empty($config['account_name'])) {
            $this->account_name = $config['account_name'];
        }

        // 初始化 付款账号，付款方的支付宝账号，支持邮箱和手机号2种格式。
        if (!empty($config['account'])) {
            $this->account = $config['account'];
        }

        // 初始化 加密方式,默认采用RSA
        if (!empty($config['sign_type'])) {
            $this->signType = strtoupper($config['sign_type']);
        }

        // 初始 支付宝网关地址
        $this->getewayUrl = 'https://mapi.alipay.com/gateway.do?';

        // 如果是老版本支付，这里需要将 version 字段设置为0
        $this->version = '0';
    }

    /**
     * 支付宝 2.0 接口 初始化配置文件
     * @param array $config
     *
     * @throws PayException
     */
    private function initConfigAli2_0(array $config)
    {
        // 支付宝分配给开发者的应用ID
        if (key_exists('app_id', $config) && is_numeric($config['app_id'])) {
            $this->appId = $config['app_id'];
        } else {
            throw new PayException('支付宝分配给开发者的应用ID 新版支付，必须提供');
        }

        // 新版本，需要提供独立的公钥信息。每一个应用，公钥都不相同
        if (key_exists('ali_public_key', $config) && file_exists($config['ali_public_key'])) {
            $this->rsaAliPubPath = $config['ali_public_key'];
        } else {
            throw new PayException('使用新版支付宝支付，必须提供开发者平台中的支付宝公钥，每一个应用都不相同');
        }

        // 初始 支付宝网关地址
        $this->getewayUrl = 'https://openapi.alipay.com/gateway.do?';
        if ($config['use_sandbox'] === true) {
            $this->getewayUrl = 'https://openapi.alipaydev.com/gateway.do?';
        }

        // 	新版只支持此种签名方式   商户生成签名字符串所使用的签名算法类型，目前支持RSA
        $this->signType = 'RSA';

        // 	发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"  需要正确设置时区
        $this->timestamp = date('Y-m-d H:i:s', time());

        // 初始 合作者身份ID
        if (key_exists('partner', $config)/* && strlen($config['partner']) == '16'*/) {
            $this->partner = $config['partner'];
        }
    }
}