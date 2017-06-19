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
use Payment\Utils\StrUtil;

final class AliConfig extends ConfigInterface
{
    // 支付宝的网关
    public $getewayUrl;

    // 	支付宝分配给开发者的应用ID
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

    // 合作者身份ID
    public $partner;

    // 用于rsa加密的私钥文件路径
    public $rsaPrivateKey;

    // 用于rsa解密的支付宝公钥文件路径
    public $rsaAliPubKey;

    // 支付宝各类method名称
    // wap 支付
    const WAP_PAY_METHOD = 'alipay.trade.wap.pay';

    // app 支付
    const APP_PAY_METHOD = 'alipay.trade.app.pay';

    // 即时到账 web支付 新版接口
    const PC_PAY_METHOD = 'alipay.trade.page.pay';

    // 扫码支付   用户扫商户的二维码
    const QR_PAY_METHOD = 'alipay.trade.precreate';

    // 条码支付   商户扫用户的二维码
    const BAR_PAY_METHOD = 'alipay.trade.pay';


    // 统一收单线下交易查询
    const TRADE_QUERY_METHOD = 'alipay.trade.query';

    // 统一收单交易退款查询  未完成
    const REFUND_QUERY_METHOD = 'alipay.trade.fastpay.refund.query';

    // 转账情况查询
    const TRANS_QUERY_METHOD = 'alipay.fund.trans.order.query';


    // 统一收单交易退款接口
    const TRADE_REFUND_METHOD = 'alipay.trade.refund';

    // 单笔转账到支付宝账户接口
    const TRANS_TOACCOUNT_METHOD = 'alipay.fund.trans.toaccount.transfer';

    public function __construct(array $config)
    {
        // 初始化配置信息
        try {
            $this->initConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 检查传入的配置文件信息是否正确
     * @param array $config
     * @throws PayException
     * @author helei
     */
    private function initConfig(array $config)
    {
        $config = ArrayUtil::paraFilter($config);// 过滤掉空值，下面不用在检查是否为空

        // 初始 支付宝网关地址
        $this->getewayUrl = 'https://openapi.alipay.com/gateway.do?';
        if (isset($config['use_sandbox']) && $config['use_sandbox'] === true) {
            $this->getewayUrl = 'https://openapi.alipaydev.com/gateway.do?';
        } else {
            $this->useSandbox = false;// 不是沙箱模式
        }

        // 支付宝分配给开发者的应用ID
        if (key_exists('app_id', $config) && is_numeric($config['app_id'])) {
            $this->appId = $config['app_id'];
        } else {
            throw new PayException('缺少支付宝分配给开发者的应用ID，请在开发者中心查看');
        }

        // 初始 支付宝异步通知地址，可为空
        if (key_exists('notify_url', $config)) {
            $this->notifyUrl = $config['notify_url'];
        }

        // 初始 支付宝 同步通知地址，可为空
        if (key_exists('return_url', $config)) {
            $this->returnUrl = $config['return_url'];
        }

        // 初始 支付宝 签名方式，可为空
        if (key_exists('sign_type', $config) && in_array($config['sign_type'], ['RSA', 'RSA2'])) {
            $this->signType = $config['sign_type'];
        } else {
            throw new PayException('目前支付宝仅支持RSA2和RSA，推荐使用RSA2');
        }


        // 新版本，需要提供独立的公钥信息。每一个应用，公钥都不相同
        if (key_exists('ali_public_key', $config) && (file_exists($config['ali_public_key']) || ! empty($config['ali_public_key']))) {
            $this->rsaAliPubKey = StrUtil::getRsaKeyValue($config['ali_public_key'], 'public');
        } else {
            throw new PayException('请提供支付宝对应的rsa公钥');
        }

        // 初始 RSA私钥文件 需要检查该文件是否存在
        if (key_exists('rsa_private_key', $config) && (file_exists($config['rsa_private_key']) || ! empty($config['ali_public_key']))) {
            $this->rsaPrivateKey = StrUtil::getRsaKeyValue($config['rsa_private_key'], 'private');
        } else {
            throw new PayException('请提供商户的rsa私钥文件');
        }

        // 	发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"  需要正确设置时区
        $this->timestamp = date('Y-m-d H:i:s', time());

        // 初始 合作者身份ID    如果该值为空，则默认为商户签约账号对应的支付宝用户ID
        if (key_exists('partner', $config)) {
            $this->partner = $config['partner'];
        }

        // 设置禁止使用的支付方式
        if (key_exists('limit_pay', $config) && is_array($config['limit_pay'])) {
            $this->limitPay = implode(',', $config['limit_pay']);
        }

        if (key_exists('return_raw', $config)) {
            $this->returnRaw = filter_var($config['return_raw'], FILTER_VALIDATE_BOOLEAN);
        }
    }
}
