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
use Payment\Utils\Curl;
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

    /**
     * 获取移除通知的数据  并进行简单处理（如：格式化为数组）
     *
     * 如果获取数据失败，返回false
     *
     * @return array|boolean
     * @author helei
     */
    protected function getNotifyData()
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
    protected function checkNotifyData(array $data)
    {
        // 检查签名
        $flag = $this->verifySign($data);

        return $flag;

        // 检查请求是否来自支付宝  之后都不在进行该检查，支付宝这个功能很鸡肋
        /*$isFrom = $this->isFromAli($data['notify_id']);
        return $isFrom;*/
    }

    /**
     * 向客户端返回必要的数据
     * @param array $data 回调机构返回的回调通知数据
     * @return array|false
     * @author helei
     */
    protected function getRetData(array $data)
    {
        $notifyType = $data['notify_type'];// 通知的类型。['trade_status_sync', 'batch_refund_notify', 'batch_trans_notify']

        $retData = '';
        switch ($notifyType) {
            case 'trade_status_sync':
                $retData = $this->getTradeData($data);
                break;
            case 'batch_refund_notify':
                $retData = $this->getRefundData($data);
                break;
            case 'batch_trans_notify':
                $retData = $this->getTransferData($data);
                break;
            default :
                $retData = false;
        }

        return $retData;
    }

    /**
     * 处理 通知类型是 trade_status_sync 的数据，其结果作为返回值，返回给客户端
     * @param array $data
     *
     *      * 以下数据为支付宝返回的数据 trade_status_sync 返回的数据
     * ```php
     * $data['discount']  折扣   支付宝系统会把discount的值加到交易金额上，如果需要折扣，本参数为负数。
     * $data['payment_type']  支付类型  只支持取值为1（商品购买）
     * $data['subject']  商品名称
     * $data['trade_no']   支付宝交易号  该交易在支付宝系统中的交易流水号。最长64位。
     * $data['buyer_email']  买家支付宝账号   可以是Email或手机号码
     * $data['gmt_create']  交易创建时间  格式为yyyy-MM-dd HH:mm:ss
     * $data['notify_type']  通知类型
     * $data['quantity']   购买数量
     * $data['out_trade_no']  商户网站唯一订单号
     * $data['seller_id']  卖家支付宝账户号  以2088开头的纯16位数字
     * $data['notify_time']   通知时间  格式为yyyy-MM-dd HH:mm:ss
     * $data['body']  商品描述
     *
     * $data['trade_status']   交易状态  https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.Y2aZ5i&treeId=62&articleId=104743&docType=1#s7
     *
     * $data['is_total_fee_adjust']  是否调整总价  该交易是否调整过价格。
     * $data['total_fee']  交易金额  该笔订单的总金额。
     * $data['gmt_payment']  交易付款时间  格式为yyyy-MM-dd HH:mm:ss
     * $data['seller_email']  卖家支付宝账号  可以是email和手机号码。
     * $data['gmt_close']   交易关闭时间   格式为yyyy-MM-dd HH:mm:ss
     * $data['price']  商品单价
     * $data['buyer_id']   买家支付宝账户号   以2088开头的纯16位数字
     * $data['notify_id']   通知校验ID
     * $data['use_coupon']  是否使用红包买家
     * $data['sign_type']   签名方式
     * $data['sign']    签名
     * $data['extra_common_param']   公用回传参数
     * ```
     *
     * @return array|bool
     * @author helei
     */
    protected function getTradeData(array $data)
    {
        $status = $this->getTradeStatus($data['trade_status']);
        if ($status !== Config::TRADE_STATUS_SUCC) {
            // 如果不是交易成功状态，直接返回错误，
            return false;
        }

        $retData = [
            'subject'   => $data['subject'],
            'body'   => $data['body'],
            'channel'   => Config::ALI,
            'order_no'   => $data['out_trade_no'],
            'trade_state'   => $status,
            'transaction_id'   => $data['trade_no'],
            'time_end'   => $data['gmt_payment'],
            'notify_time'   => $data['notify_time'],
            'notify_type'   => Config::TRADE_NOTIFY,// 通知类型为 支付行为
        ];


        if ($this->config->version) {
            // 新版本
            $retData = array_merge($retData, [
                'buyer_id'   => $data['buyer_logon_id'],
                'amount'   => $data['total_amount'],
                'receipt_amount' => $data['receipt_amount'],// 商家在交易中实际收到的款项，单位为元
                'pay_amount' => $data['buyer_pay_amount'],// 用户在交易中支付的金额
                'point_amount' => $data['point_amount'],// 使用集分宝支付的金额
                'fund_bill_list' => $data['fund_bill_list'],// 支付成功的各个渠道金额信息
            ]);

            // 检查是否存在用户自定义参数
            if (isset($data['passback_params']) && ! empty($data['passback_params'])) {
                $retData['extra_param'] = $data['passback_params'];
            }
        } else {
            // 老版本
            $retData = array_merge($retData, [
                'buyer_id'   => $data['buyer_email'],
                'amount'   => $data['total_fee'],
            ]);

            // 检查是否存在用户自定义参数
            if (isset($data['extra_common_param']) && ! empty($data['extra_common_param'])) {
                $retData['extra_param'] = $data['extra_common_param'];
            }
        }

        return $retData;
    }

    /**
     * 处理退款的返回数据，返回给客户端
     * @param array $data
     *
     * ```php
     *  $data['notify_time']   通知的发送时间。格式为yyyy-MM-dd HH:mm:ss
     *  $data['notify_type']   通知类型， batch_refund_notify
     *  $data['notify_id']   通知校验ID
     *  $data['sign_type']   DSA、RSA、MD5三个值可选，必须大写
     *  $data['sign']   签名
     *  $data['batch_no']   原请求退款批次号。
     *  $data['success_num']   退款成功总数
     *  $data['result_details']   退款结果明细  为了简洁不返回客户端
     * ```
     * @return array
     * @author helei
     */
    protected function getRefundData(array $data)
    {
        $retData = [
            'channel'   => Config::ALI,
            'refund_no'   => $data['batch_no'],
            'success_num'   => $data['success_num'],
            'notify_time'   => $data['notify_time'],
            'notify_type'   => Config::REFUND_NOTIFY,// 通知类型为 退款行为
        ];

        return $retData;
    }

    /**
     * 处理批量付款的通知类型
     * @param array $data
     *
     * ```php
     *  $data['notify_time']   通知的发送时间。格式为yyyy-MM-dd HH:mm:ss
     *  $data['notify_type']   通知类型， batch_refund_notify
     *  $data['notify_id']   通知校验ID
     *  $data['sign_type']   DSA、RSA、MD5三个值可选，必须大写
     *  $data['sign']   签名
     *  $data['batch_no']   转账批次号。
     *  $data['pay_user_id']   付款账号ID   以2088开头的16位纯数字组成。
     *  $data['pay_user_name']   付款账号姓名
     *  $data['pay_account_no']   付款账号。
     *  $data['success_details']   批量付款中成功付款的信息。
     *  $data['fail_details']   批量付款中未成功付款的信息。
     * ```
     *
     * @return array
     * @author helei
     */
    protected function getTransferData(array $data)
    {
        // 转账成功的信息  单条数据格式：流水号^收款方账号^收款账号姓名^付款金额^成功标识(S)^成功原因(null)^支付宝内部流水号^完成时间。
        $successData = explode('|', $data['success_details']);
        // 转账失败的信息  单条记录数据格式：流水号^收款方账号^收款账号姓名^付款金额^失败标识(F)^失败原因^支付宝内部流水号^完成时间。
        $failData = explode('|', $data['fail_details']);

        $retData = [
            'channel'   => Config::ALI,
            'trans_no'   => $data['batch_no'],
            'pay_name'   => $data['pay_user_name'],
            'pay_account'   => $data['pay_account_no'],
            'notify_time'   => $data['notify_time'],
            'notify_type'   => Config::REFUND_NOTIFY,// 通知类型为 退款行为
            'success'   => $successData,
            'fail'  => $failData,
        ];

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
     * 检查本次请求是否来自支付宝
     *
     * @note： notify_id只能被校验一次，如果校验后，调用客户端业务逻辑失败，
     *  支付宝不会发起第二次通知。此处需慎重处理。
     *
     * @param string $notify_id
     * @return boolean
     * @author helei
     */
    protected function isFromAli($notify_id)
    {
        if (empty($notify_id)) {
            return false;
        }

        $url = $this->config->getewayUrl . 'service=notify_verify&partner='
            . $this->config->partner . '&notify_id=' . $notify_id . '&_input_charset=' . $this->config->inputCharset;

        $curl = new Curl();
        $responseTxt = $curl->set([
            'CURLOPT_SSL_VERIFYPEER'    => true,
            'CURLOPT_SSL_VERIFYHOST'    => 2,
            'CURLOPT_CAINFO'    => $this->config->cacertPath,
            'CURLOPT_HEADER'    => 0,// 为了便于解析，将头信息过滤掉
        ])->get($url);
        
        if (preg_match("/true$/i",$responseTxt['body'])) {
            return true;
        } else {
            return false;
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

        if ($signType === 'MD5') {// 使用md5方式
            return md5($preStr . $this->config->md5Key) === $sign;
        } elseif ($signType === 'RSA') {// 使用rsa方式
            $publicKeyContent = file_get_contents($this->config->rsaAliPubPath);
            $rsa = new RsaEncrypt($publicKeyContent);

            return $rsa->rsaVerify($preStr, $sign);
        } else {
            return false;
        }
    }
}