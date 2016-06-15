<?php
/**
 * @author: helei
 * @createTime: 2016-06-08 11:25
 * @description:
 */

namespace Payment\Alipay;



use Payment\Alipay\Data\NotifyData;
use Payment\Contracts\PayNotifyInterface;
use Payment\Contracts\TradeApiInterface;
use Payment\Common\PayException;
use Payment\Common\TradeInfoData;
use Payment\Common\TradeRefundData;
use Payment\Utils\Curl;
use Payment\Utils\DataParser;
use Payment\Wxpay\Data\TradeQueryData;

class AliTradeApi implements TradeApiInterface
{

    // 配置信息类
    protected $config;
    public function __construct()
    {
        $this->config = new AlipayConfig();
    }

    /**
     * @param string $value
     * @param string $key 可取值
     *  - trade_no 支付宝交易号
     *  - out_trade_no  商户网站唯一订单号
     *
     * @return array
     * @throws PayException
     * @author helei
     */
    public function tradeQuery($value, $key = 'trade_no')
    {
        try {
            $data = $this->buildQueryData($value, $key);
        } catch (PayException $e) {
            throw $e;
        }

        // 进行签名
        $data->setSign();

        $params = $data->getValues();

        // 通过curl进行请求
        $curl = new Curl();
        // 进行post请求
        $ret = $curl->set([
            'CURLOPT_SSL_VERIFYPEER'    => true,
            'CURLOPT_SSL_VERIFYHOST'    => 2,
            'CURLOPT_CAINFO'    => $this->config->getCacertPath(),
            'CURLOPT_HEADER'    => 0,// 为了便于解析，将头信息过滤掉
        ])->post($params)->submit($this->config->getGetewayUrl() . '_input_charset=' . $this->config->getInputCharset());
        if ($ret['error'] == 1) {
            // 请求发生错误
            throw new PayException('网络错误，请稍后再试!');
        }

        // 将返回的xml数据，解析为数组
        $parserArr = DataParser::toArray($ret['body']);
        if (! is_array($parserArr)) {// 解析失败
            throw new PayException('请求订单数据解析异常!');
        }


        // 生成订单信息对象
        return $this->createTradeInfo($parserArr);
    }

    /**
     * 生成订单的查询信息
     * @param array $data
     * @return array
     * @author helei
     */
    private function createTradeInfo(array $data)
    {
        if ($data['is_success'] !== 'T') {
            return null;
        }

        $trade = $data['response']['trade'];
        if (in_array($trade['trade_status'], ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            $state = 'SUCCESS';
        } elseif (isset($trade['refund_status'])) {
            $state = 'REFUND';// 有退款信息
        } else {
            $state = 'NOTPAY';
        }

        $info = new TradeInfoData([
            'subject'   => $trade['subject'],
            'body'   => $trade['body'],
            'amount'   => $trade['total_fee'],// 单位设置为元
            'channel'   => 'ali',
            'order_no'   => $trade['out_trade_no'],
            'buyer_id'   => $trade['buyer_email'],
            'trade_state'   => $state,
            'transaction_id'   => $trade['trade_no'],
            'time_end'  => $trade['gmt_payment'],// 交易完成时间
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
            $data->setTradeNo($value);
        } elseif ($key == 'out_trade_no') {
            $data->setOutTradeNo($value);
        } else {
            throw new PayException('订单查询，仅支持trade_no  out_trade_no 两种方式');
        }

        return $data;
    }

    /**
     * @param PayNotifyInterface $notify
     *  - 子类必须实现该接口的 notifyProcess 方法
     * @return string
     * @throws PayException
     * @author helei
     */
    public function notify(PayNotifyInterface $notify)
    {
        // 支付宝的异步通知数据，全部是post数据。获取
        $data = empty($_POST) ? $_GET : $_POST;
        if (empty($data)) {
            //throw new PayException('支付宝异步通知的参数，必须是post传参');
            return 'fail';
        }

        // 验证签名是否正确
        $notifyData = new NotifyData();
        $flag = $notifyData->signVerify($data);
        if (! $flag) {
            //throw new PayException('支付宝签名错误');
            return 'fail';
        }

        // 验证请求是否来自支付宝
        $flag = $this->checkRequestFromAli($data['notify_id']);
        if (! $flag && false) {
            //throw new PayException('该请求未从支付宝获得核实');
            return 'fail';
        }

        // 检查订单是否支付
        if (! in_array($data['trade_status'], ['TRADE_FINISHED', 'TRADE_SUCCESS'])) {
            //throw new PayException('订单尚未支付');
            return 'fail';
        }

        if (in_array($data['trade_status'], ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            $state = 'SUCCESS';
        } elseif (isset($trade['refund_status'])) {
            $state = 'REFUND';// 有退款信息
        } else {
            $state = 'NOTPAY';
        }

        // 处理数据，将所有字段设置为一致
        $info = new TradeInfoData([
            'subject'   => $data['subject'],
            'body'   => $data['body'],
            'amount'   => $data['total_fee'],// 单位设置为元
            'channel'   => 'ali',
            'order_no'   => $data['out_trade_no'],
            'buyer_id'   => $data['buyer_email'],
            'trade_state'   => $state,
            'transaction_id'   => $data['trade_no'],
            'time_end'  => $data['gmt_payment'],// 交易完成时间
            'description'   => isset($data['extra_common_param']) ? $data['extra_common_param'] : '',// 附带的额外数据
        ]);
        
        // 回调处理业务逻辑
        $flag = $notify->notifyProcess($info->toArray());
        if ($flag) {
            return 'success';
        }

        return 'fail';
    }

    /**
     * 检查请求是否来自支付宝
     * @param $notify_id
     * @return bool
     * @author helei
     */
    protected function checkRequestFromAli($notify_id)
    {
        if (empty($notify_id)) {
            return false;
        }

        $url = $this->config->getGetewayUrl() . 'service=notify_verify&partner='
            . $this->config->getPartner() . '&notify_id=' . $notify_id . '&_input_charset=' . $this->config->getInputCharset();

        $veryfy_url = htmlspecialchars($url);

        $curl = new Curl();
        $responseTxt = $curl->set([
            'CURLOPT_SSL_VERIFYPEER'    => true,
            'CURLOPT_SSL_VERIFYHOST'    => 2,
            'CURLOPT_CAINFO'    => $this->config->getCacertPath(),
            'CURLOPT_HEADER'    => 0,// 为了便于解析，将头信息过滤掉
        ])->get($veryfy_url);

        if (preg_match("/true$/i",$responseTxt['body'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 支付宝退款api
     * @param TradeRefundData $data
     * @return mixed
     * @author helei
     */
    public function refund(TradeRefundData $data)
    {
        return '开发中....';
    }
}