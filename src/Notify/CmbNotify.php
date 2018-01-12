<?php
namespace Payment\Notify;

use Payment\Common\CmbConfig;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\ArrayUtil;
use Payment\Utils\RsaEncrypt;

/**
 * 招商回调处理
 * Class CmbNotify
 * @package Payment\Notify
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 *
 */
class CmbNotify extends NotifyStrategy
{

    /**
     * CmbNotify constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        try {
            $this->config = new CmbConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    public function getNotifyData()
    {
        $data = empty($_POST) ? $_GET : $_POST;
        if (empty($data) || ! is_array($data)) {
            return false;
        }

        $retData = json_decode($data[CmbConfig::REQ_FILED_NAME], true);
        return $retData;
    }

    /**
     * 对返回信息验证签名
     * @param array $data
     * @return boolean
     */
    public function checkNotifyData(array $data)
    {
        $signType = strtoupper($data['signType']);
        $sign = $data['sign'];

        // 1. 对待签名参数数组排序
        $values = ArrayUtil::arraySort($data['noticeData']);
        // 2. 将排序后的参数与其对应值，组合成“参数=参数值”的格式,用&字符连接起来
        $preStr = ArrayUtil::createLinkstring($values);

        if ($signType === 'RSA') {// 使用rsa方式
            $rsa = new RsaEncrypt($this->config->rsaPubKey);

            return $rsa->rsaVerify($preStr, $sign);
        } else {
            return false;
        }
    }

    /**
     * 向客户端返回相关数据
     * @param array $data
     * @return array
     */
    protected function getRetData(array $data)
    {
        $noticeData = $data['noticeData'];
        $noticeType = $noticeData['noticeType'];
        if ($noticeType === CmbConfig::NOTICE_PAY) {
            $channel = Config::CMB_CHARGE;
        } elseif ($noticeType === CmbConfig::NOTICE_SIGN) {
            $channel = Config::CMB_BIND;
        } else {
            $channel = 'other';
        }

        if (!$this->config->returnRaw) {
            $data['channel'] = $channel;
            return $data;
        } elseif ($noticeType === CmbConfig::NOTICE_PAY) {
            $retData = [
                'amount'   => $noticeData['amount'],
                'channel'   => $channel,
                'date' => $noticeData['date'],
                'order_no'   => $noticeData['orderNo'],
                'trade_state'   => Config::TRADE_STATUS_SUCC,// 招商的订单只会成功
                'transaction_id'   => $noticeData['bankSerialNo'],
                'time_end'   => date('Y-m-d H:i:s', strtotime($noticeData['dateTime'])),// Y-m-d H:i:s
                'discount_fee' => $noticeData['discountAmount'],// 优惠金额,格式：xxxx.xx  无优惠时返回0.00
                'card_type' => $noticeData['cardType'],// 卡类型,02：一卡通；03：信用卡；07：他行卡
                'return_param' => $noticeData['merchantPara'],
                'discount_flag' => $noticeData['discountFlag'],
                'notice_no' => $noticeData['noticeSerialNo'],
            ];
        } elseif ($noticeType === CmbConfig::NOTICE_SIGN) {
            $retData = [
                'user_id' => $noticeData['userID'],
                'no_pwd_pay' => $noticeData['noPwdPay'],
                'notice_no' => $noticeData['noticeSerialNo'],
                'agr_no' => $noticeData['agrNo'],
                'rsp_msg' => $noticeData['rspMsg'],
                'return_param' => $noticeData['noticePara'],
                'user_pid_hash' => $noticeData['userPidHash'],
                'user_pid_type' => $noticeData['userPidType'],
                'channel'   => $channel,
            ];
        } else {
            $retData = $noticeData;
        }

        return $retData;
    }

    /**
     * 招商只需要返回状态码
     * @param bool $flag
     * @param string $msg
     * @return string
     */
    protected function replyNotify($flag, $msg = 'OK')
    {
        if ($flag) {
            header('HTTP/1.1 200 OK');
            return $msg;
        } else {
            header('HTTP/1.1 503 Service Unavailable');
            return $msg;
        }
    }
}
