<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 14:56
 * @description: 微信配置文件
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common;


use Payment\Utils\ArrayUtil;
use Payment\Utils\StrUtil;

final class WxConfig extends ConfigInterface
{
    // 微信支付的网关
    public $getewayUrl = 'https://api.mch.weixin.qq.com/';

    // 微信分配的公众账号ID
    public $appId;

    // 微信支付分配的商户号
    public $mchId;

    // 随机字符串，不长于32位
    public $nonceStr;

    // 符合ISO 4217标准的三位字母代码
    public $feeType = 'CNY';

    // 用于异步通知的地址
    public $notifyUrl;

    // 交易开始时间 格式为yyyyMMddHHmmss
    public $timeStart;

    // 订单在微信服务器过期的时间，过期后无法支付
    public $timeExpire;

    // 用于加密的md5Key
    public $md5Key;

    // 安全证书的路径
    public $cacertPath;

    // cert证书路径
    public $certPath;

    // key文件路径
    public $keyPath;

    // 加密方式 默认使用MD5  微信当前仅支持该方式
    public $signType = 'MD5';

    // 统一下单url
    const UNIFIED_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    // 查询url
    const ORDER_QUERY_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';

    // 申请退款url
    const REFUND_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    // 查询退款url
    const REFUDN_QUERY_URL = 'https://api.mch.weixin.qq.com/pay/refundquery';

    // 企业付款
    const TRANSFERS_URL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

    // 企业付款的查询
    const TRANS_QUERY_URL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo';

    // 关闭订单url  尚未接入
    const CLOSE_URL = 'https://api.mch.weixin.qq.com/pay/closeorder';

    // 短连接转化url  尚未接入
    const SHORT_URL = 'https://api.mch.weixin.qq.com/tools/shorturl';

    /**
     * 初始化微信配置文件
     * WxConfig constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        try {
            $this->initConfig($config);
        } catch (PayException $e) {
            throw $e;
        }

        $basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Weixin' . DIRECTORY_SEPARATOR;
        $this->cacertPath = "{$basePath}/rootca.pem";
    }

    /**
     * 初始化配置文件参数
     * @param array $config
     * @throws PayException
     */
    private function initConfig(array $config)
    {
        $config = ArrayUtil::paraFilter($config);

        // 检查 微信分配的公众账号ID
        if (key_exists('app_id', $config) && !empty($config['app_id'])) {
            $this->appId = $config['app_id'];
        } else {
            throw new PayException('必须提供微信分配的公众账号ID');
        }

        // 检查 微信支付分配的商户号
        if (key_exists('mch_id', $config) && !empty($config['mch_id'])) {
            $this->mchId = $config['mch_id'];
        } else {
            throw new PayException('必须提供微信支付分配的商户号');
        }

        // 生成随机字符串
        $this->nonceStr = StrUtil::getNonceStr();

        // 检查 异步通知的url
        if (key_exists('notify_url', $config) && !empty($config['notify_url'])) {
            $this->notifyUrl = trim($config['notify_url']);
        } else {
            throw new PayException('异步通知的url必须提供.');
        }

        // 设置交易开始时间 格式为yyyyMMddHHmmss   .再次之前一定要设置时区
        $startTime = time();
        $this->timeStart = date('YmdHis', $startTime);

        // 初始 微信订单过期时间，最短失效时间间隔必须大于5分钟
        if (key_exists('time_expire', $config) && !empty($config['time_expire']) && $config['time_expire'] >= 5) {
            $this->timeExpire = date('YmdHis', $startTime + ($config['time_expire'] * 60));
        } else {
            throw new PayException('必须设置订单过期时间,且需要大于5分钟.如果不正确请检查是否正确设置时区');
        }

        // 初始 支付宝网关地址
        if (key_exists('geteway_url', $config) && !empty($config['geteway_url'])) {
            $this->getewayUrl = $config['geteway_url'];
        }

        // 初始 MD5 key
        if (key_exists('md5_key', $config) && !empty($config['md5_key'])) {
            $this->md5Key = $config['md5_key'];
        } else {
            throw new PayException('MD5 Key 不能为空，再微信商户后台可查看');
        }

        // 以下两个文件，如果是调用资金流向接口，必须提供
        if (! empty($config['cert_path'])) {
            $this->certPath = $config['cert_path'];
        }

        if (! empty($config['key_path'])) {
            $this->keyPath = $config['key_path'];
        }
    }


}
