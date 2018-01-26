<?php
namespace Payment\Common;

use Payment\Utils\ArrayUtil;
use Payment\Utils\StrUtil;

/**
 * Class CmbConfig
 * @package Payment\Common
 * @desc: 招商一网通支付的配置文件
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
class CmbConfig extends ConfigInterface
{
    // 调用的接口版本，固定为：1.0
    public $version = '1.0';

    // 采用的编码
    public $charset = 'UTF-8';

    // 用于加密的 merKey
    public $merKey;

    // 发送请求的时间，格式"yyyyMMddHHmmss"
    public $dateTime;

    // 商户分行号，4位数字
    public $branchNo;

    // 商户号，6位数字
    public $merchantNo;

    // 用于同步通知的地址
    public $returnUrl;

    // 成功签约结果通知地址:首次签约，必填. 商户接收成功签约结果通知的地址。
    public $signNoticeUrl;

    // 操作员登录密码。
    public $opPwd;

    // 招商请求的网关
    public $getewayUrl;

    // 招商的公钥
    public $rsaPubKey;

    const MAX_EXPIRE_TIME = 30;// 过期时间最大 30分钟

    const REQ_FILED_NAME = 'jsonRequestData';// 报文的参数名：jsonRequestData

    const SUCC_TAG = 'SUC0000';// SUC0000表示成功，其他表示错误，具体错误码见详细API定义。

    const TRADE_CODE = 'FBPK';// 交易码,固定为“FBPK”

    const NOTICE_PAY = 'BKPAYRTN';// 支付成功回调

    const NOTICE_SIGN = 'BKQY';// 签约成功回调

    /**
     * 初始化配置文件
     * @param array $config
     * @throws PayException
     */
    protected function initConfig(array $config)
    {
        $config = ArrayUtil::paraFilter($config);

        // 初始 mer key
        if (key_exists('mer_key', $config) && !empty($config['mer_key'])) {
            $this->merKey = $config['mer_key'];
        } else {
            throw new PayException('Mer Key 不能为空，请前往招商一网通进行设置');
        }

        // 设置操作员登陆密码
        if (key_exists('op_pwd', $config) && !empty($config['op_pwd'])) {
            $this->opPwd = $config['op_pwd'];
        } else {
            throw new PayException('请设置操作员登陆密码');
        }

        // 检查 异步通知的url
        if (key_exists('notify_url', $config) && !empty($config['notify_url'])) {
            $this->notifyUrl = trim($config['notify_url']);
        } else {
            throw new PayException('异步通知的url必须提供.');
        }

        // 检查 签约异步通知的url
        if (key_exists('sign_notify_url', $config) && !empty($config['sign_notify_url'])) {
            $this->signNoticeUrl = trim($config['sign_notify_url']);
        } else {
            throw new PayException('签约 异步通知的url必须提供.');
        }

        // 商户分行号，4位数字
        if (key_exists('branch_no', $config) && !empty($config['branch_no'])) {
            $this->branchNo = trim($config['branch_no']);
        } else {
            throw new PayException('商户分行号必须提供，4位数字.');
        }

        // 商户号，6位数字
        if (key_exists('merchant_no', $config) && !empty($config['merchant_no'])) {
            $this->merchantNo = trim($config['merchant_no']);
        } else {
            throw new PayException('商户号必须提供，6位数字.');
        }

        $this->limitPay = '';
        // 设置禁止使用的支付方式
        if (
            key_exists('limit_pay', $config) &&
            !empty($config['limit_pay']) &&
            strtoupper($config['limit_pay'][0]) === 'A'
        ) {
            $this->limitPay = 'A';
        }

        if (key_exists('return_raw', $config)) {
            $this->returnRaw = filter_var($config['return_raw'], FILTER_VALIDATE_BOOLEAN);
        }

        // 签名算法,固定为“SHA-256”
        if (key_exists('sign_type', $config) && in_array($config['sign_type'], ['SHA-256'])) {
            $this->signType = $config['sign_type'];
        } else {
            $this->signType = 'SHA-256';
        }

        if (isset($config['use_sandbox']) && $config['use_sandbox'] === true) {
            $this->useSandbox = true;
        } else {
            $this->useSandbox = false;// 不是沙箱模式
        }

        if (key_exists('cmb_pub_key', $config) && (file_exists($config['cmb_pub_key']) || ! empty($config['cmb_pub_key']))) {
            $this->rsaPubKey = StrUtil::getRsaKeyValue($config['cmb_pub_key'], 'public');
        } else {
            throw new PayException('请提供招商对应的rsa公钥，可通过Helper接口获取');
        }

        // 初始 招商一网通 同步通知地址，可为空
        if (key_exists('return_url', $config)) {
            $this->returnUrl = $config['return_url'];
        }
        // 设置交易开始时间 格式为yyyyMMddHHmmss   .再此之前一定要设置时区
        $this->dateTime = date('YmdHis', time());
    }
}
