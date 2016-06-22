<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 15:22
 * @description:
 */

namespace Payment\Wxpay;



use Payment\Contracts\PayNotifyInterface;
use Payment\Contracts\TradeApiInterface;
use Payment\Common\PayException;
use Payment\Common\TradeInfoData;
use Payment\Common\TradeRefundData;
use Payment\Utils\Curl;
use Payment\Utils\DataParser;
use Payment\Utils\StrUtil;
use Payment\Wxpay\Data\PayResultData;
use Payment\Wxpay\Data\TradeQueryData;

class WxTradeApi implements TradeApiInterface
{

    // 配置信息类
    protected $config;
    public function __construct($tradeType = 'APP')
    {
        $this->config = new WxConfig($tradeType);
    }

    /**
     * 查询微信订单状态
     * @param string $value
     * @param string $key
     * @return array
     * @throws PayException
     * @author helei
     */
    public function tradeQuery($value, $key)
    {
        try {
            $data = $this->buildQueryData($value, $key);
        } catch (PayException $e) {
            throw $e;
        }

        $url = $this->config->getGetewayUrl() . 'pay/orderquery';

        // 获取用于请求的xml数据
        $xml = DataParser::toXml($data->getValues());

        // 进行curl请求
        $curl = new Curl();
        $ret = $curl->post($xml)->submit($url);

        // 格式化为数据
        $retArr = DataParser::toArray($ret['body']);

        // 检查数据签名
        $payResult = new PayResultData();
        // 验证返回的结果签名
        if (! $payResult->signVerify($retArr)) {
            throw new PayException('签名错误');
        }
        return $this->createTradeInfo($retArr);
    }

    /**
     * 生成订单的查询信息
     * @param array $data
     * @return array
     * @author helei
     */
    private function createTradeInfo(array $data)
    {
        if ($data['return_code'] !== 'SUCCESS' || $data['result_code'] !== 'SUCCESS') {
            return null;
        }

        if ($data['trade_state'] == 'SUCCESS') {
            $state = 'SUCCESS';
        } elseif ($data['trade_state'] == 'REFUND') {
            $state = 'REFUND';// 有退款信息
        } else {
            $state = 'NOTPAY';
        }

        $info = new TradeInfoData([
            'subject'   => '',
            'body'   => '',
            'amount'   => isset($data['total_fee']) ? bcdiv($data['total_fee'], 100, 2) : 0,// 单位设置为元
            'channel'   => 'wx',
            'order_no'   => $data['out_trade_no'],
            'buyer_id'   => isset($data['openid']) ? $data['openid'] : '',
            'trade_state'   => $state,
            'transaction_id'   => isset($data['transaction_id']) ? $data['transaction_id'] : '',
            'time_end'  => isset($data['time_end']) ? date('Y-m-d H:i:s', strtotime($data['time_end'])) : '',// 交易完成时间
            'description'   => '',
        ]);

        return $info->toArray();
    }

    /**
     * @param $value
     * @param $key
     * @return TradeQueryData
     * @author helei
     * @throws PayException
     */
    protected function buildQueryData($value, $key)
    {
        $data = new TradeQueryData();

        // 检查参数
        if (empty($value)) {
            throw new PayException('必须指定查询的编号值');
        }

        if ($key == 'trade_no') {
            $data->setTransactionId($value);
        } elseif ($key == 'out_trade_no') {
            $data->setOutTradeNo($value);
        } else {
            throw new PayException('订单查询，仅支持trade_no  out_trade_no 两种方式');
        }

        // 设置字符串
        $data->setNonceStr(StrUtil::getNonceStr());

        // 进行签名
        $data->setSign();

        return $data;
    }

    /**
     * 回调通知
     * @param PayNotifyInterface $notify
     * @return string
     * @author helei
     */
    public function notify(PayNotifyInterface $notify)
    {
        $response = [];// 响应微信的数据结构
        // 采用以下方式替换weiixn的方式
        $xml = file_get_contents("php://input");
        if (empty($xml)) {
            $response = [
                'return_code'   => 'FAIL',
                'return_msg'    => '未获取到微信的数据',
            ];
            return DataParser::toXml($response);
        }

        // 格式化数据为数组
        $data = DataParser::toArray($xml);
        if ($data === false) {
            $response = [
                'return_code'   => 'FAIL',
                'return_msg'    => '解析数据错误',
            ];
            return DataParser::toXml($response);
        }

        // 检查是否完成支付
        if ($data['result_code'] !== 'SUCCESS' || $data['return_code'] !== 'SUCCESS') {
            $response = [
                'return_code'   => 'FAIL',
                'return_msg'    => '尚未支付',
            ];
            return DataParser::toXml($response);
        }

        // 签名验证
        $payResult = new PayResultData();
        // 验证返回的结果签名
        if (! $payResult->signVerify($data)) {
            $response = [
                'return_code'   => 'FAIL',
                'return_msg'    => '签名错误',
            ];
            return DataParser::toXml($response);
        }

        // 将数据进行统一处理
        $info = new TradeInfoData([
            'subject'   => '',
            'body'   => '',
            'amount'   => bcdiv($data['total_fee'], 100, 2),// 单位设置为元
            'channel'   => 'ali',
            'order_no'   => $data['out_trade_no'],
            'buyer_id'   => $data['openid'],
            'trade_state'   => 'SUCCESS',
            'transaction_id'   => $data['transaction_id'],
            'time_end'  => date('Y-m-d H:i:s', strtotime($data['time_end'])),// 交易完成时间
            'description'   => isset($data['attach']) ? $data['attach'] : '',// 附带的额外数据
        ]);

        $flag = $notify->notifyProcess($info->toArray());
        if ($flag) {
            $response = [
                'return_code'   => 'SUCCESS',
                'return_msg'    => 'OK',
            ];
            return DataParser::toXml($response);
        }

        $response = [
            'return_code'   => 'FAIL',
            'return_msg'    => '处理业务逻辑失败',
        ];

        return DataParser::toXml($response);
    }

    /**
     * 微信退款api
     * @param array $data
     * @return mixed
     * @author helei
     */
    public function refund(array $data)
    {
        return '开发中....';
    }
}