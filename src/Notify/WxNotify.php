<?php
/**
 * @author: helei
 * @createTime: 2016-07-20 16:46
 * @description:
 */

namespace Payment\Notify;


use Payment\Common\PayException;
use Payment\Common\WxConfig;
use Payment\Config;
use Payment\Utils\ArrayUtil;
use Payment\Utils\DataParser;

/**
 * Class WxNotify
 * 微信回调处理
 * @package Payment\Notify
 * anthor helei
 */
class WxNotify extends NotifyStrategy
{

    /**
     * WxNotify constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        try {
            $this->config = new WxConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 获取微信返回的异步通知数据
     * @return array|bool
     * @author helei
     */
    protected function getNotifyData()
    {
        // php://input 带来的内存压力更小
        $data = @file_get_contents('php://input');// 等同于微信提供的：$GLOBALS['HTTP_RAW_POST_DATA']
        // 将xml数据格式化为数组
        $arrData = DataParser::toArray($data);
        if (empty($arrData)) {
            return false;
        }

        // 移除值中的空格  xml转化为数组时，CDATA 数据会被带入额外的空格。
        $arrData = ArrayUtil::paraFilter($arrData);

        return $arrData;
    }

    /**
     * 检查微信异步通知的数据是否正确
     * @param array $data
     *
     * @author helei
     * @return boolean
     */
    protected function checkNotifyData(array $data)
    {
        if ($data['return_code'] != 'SUCCESS' || $data['result_code'] != 'SUCCESS') {
            // $arrData['return_msg']  返回信息，如非空，为错误原因
            // $data['result_code'] != 'SUCCESS'  表示业务失败
            return false;
        }

        // 检查返回数据签名是否正确
        return $this->verifySign($data);
    }

    /**
     * 检查微信返回的数据是否被篡改过
     * @param array $retData
     * @return boolean
     * @author helei
     */
    protected function verifySign(array $retData)
    {
        $retSign = $retData['sign'];
        $values = ArrayUtil::removeKeys($retData, ['sign', 'sign_type']);

        $values = ArrayUtil::paraFilter($values);

        $values = ArrayUtil::arraySort($values);

        $signStr = ArrayUtil::createLinkstring($values);

        $signStr .= "&key=" . $this->config->md5Key;

        $sign = md5($signStr);

        return strtoupper($sign) === $retSign;
    }

    /**
     * 获取向客户端返回的数据
     * @param array $data
     *
     * ```php
     *  $data = [
     *      'appid' => '', // 公众账号ID
     *      'bank_type' => 'CFT',// 付款银行
     *      'cash_fee' => '1',// 现金支付金额
     *      'fee_type' => 'CNY',// 货币种类
     *      'is_subscribe' => 'N', // 是否关注公众账号  Y-关注，N-未关注
     *      'mch_id' => '',// 商户号
     *      'nonce_str' => '', // 随机字符串
     *      'openid' => '', // 用户标识
     *      'out_trade_no' => '',// 商户订单号
     *      'result_code' => 'SUCCESS',// 业务结果
     *      'return_code' => 'SUCCESS',// 返回状态码
     *      'sign' => '', // 签名
     *      'time_end' => '20160802104424',// 支付完成时间 格式为yyyyMMddHHmmss
     *      'total_fee' => '1',// 订单金额
     *      'trade_type' => 'APP', // 交易类型  JSAPI、NATIVE、APP
     *      'transaction_id' => '',// 微信支付订单号
     *      'attach'    => '',//商家数据包  商户自定义数据，原样返回
     *  ];
     * ```
     *
     * @return array
     * @author helei
     */
    protected function getRetData(array $data)
    {
        // 将金额处理为元
        $data['total_fee'] = bcdiv($data['total_fee'], 100, 2);

        $retData = [
            'amount'   => $data['total_fee'],
            'channel'   => Config::WEIXIN,
            'order_no'   => $data['out_trade_no'],
            'buyer_id'   => $data['openid'],
            'trade_state'   => Config::TRADE_STATUS_SUCC,
            'transaction_id'   => $data['transaction_id'],
            'time_end'   => date('Y-m-d H:i:s', strtotime($data['time_end'])),
            'notify_time'   => date('Y-m-d H:i:s', time()),
            'notify_type'   => Config::TRADE_NOTIFY,// 通知类型为 支付行为
        ];

        // 检查是否存在用户自定义参数
        if (isset($data['attach']) && ! empty($data['attach'])) {
            $retData['extra_param'] = $data['attach'];
        }

        return $retData;
    }

    /**
     * 处理完后返回的数据格式
     * @param bool $flag
     * @param string $msg 通知信息，错误原因
     * @author helei
     * @return string
     */
    protected function replyNotify($flag, $msg = 'OK')
    {
        // 默认为成功
        $result = [
            'return_code'   => 'SUCCESS',
            'return_msg'    => 'OK',
        ];
        if (! $flag) {
            // 失败
            $result = [
                'return_code'   => 'FAIL',
                'return_msg'    => $msg,
            ];
        }

        return DataParser::toXml($result);
    }
}