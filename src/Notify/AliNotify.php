<?php
/**
 * @author: helei
 * @createTime: 2016-07-20 16:21
 * @description: 支付宝回调通知
 *
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Notify;

use Payment\Common\AliConfig;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\ArrayUtil;
use Payment\Utils\Rsa2Encrypt;
use Payment\Utils\RsaEncrypt;

class AliNotify extends NotifyStrategy
{
    /**
     * AliNotify constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        try {
            $this->config = new AliConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    protected function getOldAliPublicKey()
    {
        $filePath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'CacertFile/old_alipay_public_key.pem';

        return @file_get_contents($filePath);
    }

    /**
     * 获取移除通知的数据  并进行简单处理（如：格式化为数组）
     *
     * 如果获取数据失败，返回false
     *
     * @return array|boolean
     * @author helei
     */
    public function getNotifyData()
    {
        $data = empty($_POST) ? $_GET : $_POST;
        if (empty($data) || ! is_array($data)) {
            return false;
        }

        return $data;
    }

    /**
     * 检查异步通知的数据是否合法
     *
     * 如果检查失败，返回false
     *
     * @param array $data  由 $this->getNotifyData() 返回的数据
     * @return boolean
     * @author helei
     */
    public function checkNotifyData(array $data)
    {
        $status = $this->getTradeStatus($data['trade_status']);
        if ($status !== Config::TRADE_STATUS_SUCC) {
            // 如果不是交易成功状态，直接返回错误，
            return false;
        }

        // 主要是为了即时到账的签名
        if (! isset($data['version'])) {
            $this->config->rsaAliPubKey = $this->getOldAliPublicKey();
        }

        // 检查签名
        $flag = $this->verifySign($data);

        return $flag;
    }

    /**
     * 向客户端返回必要的数据
     * @param array $data 回调机构返回的回调通知数据
     * @return array|false
     * @author helei
     */
    protected function getRetData(array $data)
    {
        if ($this->config->returnRaw) {
            $data['channel'] = Config::ALI_CHARGE;
            return $data;
        }

        if (! isset($data['version'])) {// 即时到账
            $retData = [
                'order_no'   => $data['out_trade_no'],
                'subject'   => $data['subject'],
                'transaction_id'   => $data['trade_no'],
                'trade_state'   => $this->getTradeStatus($data['trade_status']),
                'trade_create_time' => $data['gmt_create'],// 交易创建时间
                'pay_time'   => $data['gmt_payment'],// 交易付款时间
                'seller_id' => $data['seller_id'],
                'seller_email' => $data['seller_email'],
                'buyer_id'   => $data['buyer_id'],
                'amount'   => $data['total_fee'],
                'channel'   => Config::ALI_CHARGE,
                'body'   => $data['body'],
                'discount' => $data['discount'],
                'return_param' => $data['extra_common_param'],
                'notify_time'   => $data['notify_time'],
                'notify_type' => $data['notify_type'],
            ];
        } else {
            $retData = [
                'amount'   => $data['total_amount'],
                'buyer_id'   => $data['buyer_id'],
                'transaction_id'   => $data['trade_no'],
                'body'   => $data['body'],
                'notify_time'   => $data['notify_time'],
                'subject'   => $data['subject'],
                'buyer_account' => $data['buyer_logon_id'],
                'auth_app_id' => $data['auth_app_id'],
                'notify_type' => $data['notify_type'],
                'invoice_amount' => $data['invoice_amount'],
                'order_no'   => $data['out_trade_no'],
                'trade_state'   => $this->getTradeStatus($data['trade_status']),
                'pay_time'   => $data['gmt_payment'],// 交易付款时间
                'point_amount' => $data['point_amount'],// 使用集分宝支付的金额
                'trade_create_time' => $data['gmt_create'],// 交易创建时间
                'pay_amount' => $data['buyer_pay_amount'],// 用户在交易中支付的金额
                'receipt_amount' => $data['receipt_amount'],// 商家在交易中实际收到的款项，单位为元
                'fund_bill_list' => $data['fund_bill_list'],// 支付成功的各个渠道金额信息
                'app_id' => $data['app_id'],
                'seller_id' => $data['seller_id'],
                'seller_email' => $data['seller_email'],
                'channel'   => Config::ALI_CHARGE,
            ];
        }

        // 检查是否存在用户自定义参数
        if (isset($data['passback_params']) && ! empty($data['passback_params'])) {
            $retData['return_param'] = $data['passback_params'];
        }

        return $retData;
    }

    /**
     * 支付宝，成功返回 ‘success’   失败，返回 ‘fail’
     * @param boolean $flag 每次返回的bool值
     * @param string $msg 错误原因  后期考虑记录日志
     * @return string
     * @author helei
     */
    protected function replyNotify($flag, $msg = '')
    {
        if ($flag) {
            return 'success';
        } else {
            return 'fail';
        }
    }

    /**
     * 返回统一的交易状态
     * @param $status
     * @return string
     * @author helei
     */
    protected function getTradeStatus($status)
    {
        if (in_array($status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return Config::TRADE_STATUS_SUCC;
        } else {
            return Config::TRADE_STATUS_FAILD;
        }
    }

    /**
     * 检查支付宝数据 签名是否被篡改
     * @param array $data
     * @return boolean
     * @author helei
     */
    protected function verifySign(array $data)
    {
        $signType = strtoupper($data['sign_type']);
        $sign = $data['sign'];

        // 1. 剔除sign与sign_type参数
        $values = ArrayUtil::removeKeys($data, ['sign', 'sign_type']);
        //  2. 移除数组中的空值
        $values = ArrayUtil::paraFilter($values);
        // 3. 对待签名参数数组排序
        $values = ArrayUtil::arraySort($values);
        // 4. 将排序后的参数与其对应值，组合成“参数=参数值”的格式,用&字符连接起来
        $preStr = ArrayUtil::createLinkstring($values);

        if ($signType === 'RSA') {// 使用rsa方式
            $rsa = new RsaEncrypt($this->config->rsaAliPubKey);

            return $rsa->rsaVerify($preStr, $sign);
        } elseif ($signType === 'RSA2') {
            $rsa = new Rsa2Encrypt($this->config->rsaAliPubKey);

            return $rsa->rsaVerify($preStr, $sign);
        } else {
            return false;
        }
    }
}
