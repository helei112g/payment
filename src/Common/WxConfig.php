<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 14:56
 * @description: 微信配置文件
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common;

use Payment\Common\Weixin\WechatHelper;
use Payment\Utils\ArrayUtil;
use Payment\Utils\StrUtil;

final class WxConfig extends ConfigInterface
{
    // 微信分配的公众账号ID
    public $appId;

    // 微信支付分配的商户号
    public $mchId;

    // 随机字符串，不长于32位
    public $nonceStr;

    // 符合ISO 4217标准的三位字母代码
    public $feeType = 'CNY';

    // 交易开始时间 格式为yyyyMMddHHmmss
    public $timeStart;

    // 用于加密的md5Key
    public $md5Key;

    // 安全证书的路径
    public $cacertPath;

    // cert证书路径或者内容
    public $appCertPem;

    // key文件路径或者内容
    public $appKeyPem;

    // 	支付类型
    public $tradeType;

    // 指定回调页面
    public $returnUrl;

    // 统一下单url
    const UNIFIED_URL = 'https://api.mch.weixin.qq.com/{debug}/pay/unifiedorder';

    // 提交刷卡支付url
    const MICROPAY_URL = 'https://api.mch.weixin.qq.com/{debug}/pay/micropay';


    // 支付查询url
    const CHARGE_QUERY_URL = 'https://api.mch.weixin.qq.com/{debug}/pay/orderquery';

    // 查询退款url
    const REFUDN_QUERY_URL = 'https://api.mch.weixin.qq.com/{debug}/pay/refundquery';

    // 企业付款的查询
    const TRANS_QUERY_URL = 'https://api.mch.weixin.qq.com/{debug}/mmpaymkttransfers/gettransferinfo';


    // 申请退款url
    const REFUND_URL = 'https://api.mch.weixin.qq.com/{debug}/secapi/pay/refund';

    // 企业付款
    const TRANSFERS_URL = 'https://api.mch.weixin.qq.com/{debug}/mmpaymkttransfers/promotion/transfers';


    // 关闭订单url  尚未接入
    const CLOSE_URL = 'https://api.mch.weixin.qq.com/{debug}/pay/closeorder';

    // 短连接转化url  尚未接入
    const SHORT_URL = 'https://api.mch.weixin.qq.com/{debug}/tools/shorturl';

    // 退款账户
    const REFUND_UNSETTLED = 'REFUND_SOURCE_UNSETTLED_FUNDS';// 未结算资金退款（默认使用未结算资金退款）
    const REFUND_RECHARGE = 'REFUND_SOURCE_RECHARGE_FUNDS';// 可用余额退款(限非当日交易订单的退款）

    // 沙箱测试相关
    const SANDBOX_PRE = 'sandboxnew';
    const SANDBOX_URL = 'https://api.mch.weixin.qq.com/sandboxnew/pay/getsignkey';

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

        $basePath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'CacertFile' . DIRECTORY_SEPARATOR;
        $this->cacertPath = "{$basePath}wx_cacert.pem";
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

        // 检查 异步通知的url
        if (key_exists('notify_url', $config) && !empty($config['notify_url'])) {
            $this->notifyUrl = trim($config['notify_url']);
        } else {
            throw new PayException('异步通知的url必须提供.');
        }

        // 设置交易开始时间 格式为yyyyMMddHHmmss   .再次之前一定要设置时区
        $startTime = time();
        $this->timeStart = date('YmdHis', $startTime);

        // 初始 MD5 key
        if (key_exists('md5_key', $config) && !empty($config['md5_key'])) {
            $this->md5Key = $config['md5_key'];
        } else {
            throw new PayException('MD5 Key 不能为空，再微信商户后台可查看');
        }

        // 设置支付的货币类型
        if (key_exists('fee_type', $config) && in_array($config['fee_type'], ['CNY'])) {
            $this->feeType = $config['fee_type'];
        }

        // 设置禁止使用的支付方式
        if (key_exists('limit_pay', $config) && !empty($config['limit_pay']) && $config['limit_pay'][0] === 'no_credit') {
            $this->limitPay = $config['limit_pay'][0];
        }

        if (key_exists('return_raw', $config)) {
            $this->returnRaw = filter_var($config['return_raw'], FILTER_VALIDATE_BOOLEAN);
        }

        if (key_exists('redirect_url', $config)) {
            $this->returnUrl = $config['redirect_url'];
        }

        // 以下两个文件，如果是调用资金流向接口，必须提供
        if (! empty($config['app_cert_pem'])) {
            $this->appCertPem = $config['app_cert_pem'];
        }
        if (! empty($config['app_key_pem'])) {
            $this->appKeyPem = $config['app_key_pem'];
        }

        if (key_exists('sign_type', $config) && in_array($config['sign_type'], ['MD5', 'HMAC-SHA256'])) {
            $this->signType = $config['sign_type'];
        } else {
            $this->signType = 'MD5';
        }

        // 生成随机字符串
        $this->nonceStr = StrUtil::getNonceStr();

        if (isset($config['use_sandbox']) && $config['use_sandbox'] === true) {
            $this->useSandbox = true;// 是沙箱模式  重新获取key

            $helper = new WechatHelper($this, []);
            $this->md5Key = $helper->getSandboxSignKey();
        } else {
            $this->useSandbox = false;// 不是沙箱模式
        }
    }
}
