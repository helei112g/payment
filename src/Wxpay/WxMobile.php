<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 11:28
 * @description: 微信 APP 支付接口
 */

namespace Payment\Wxpay;



use Payment\Contracts\ChargeInterface;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;
use Payment\Utils\Curl;
use Payment\Utils\DataParser;
use Payment\Utils\StrUtil;
use Payment\Wxpay\Data\PayResultData;
use Payment\Wxpay\Data\UnifiedOrderData;

class WxMobile implements ChargeInterface
{
    // 交易类型
    protected $tradeType;

    protected $config;

    public function __construct($tradeType)
    {
        $this->tradeType = $tradeType;

        $this->config = new WxConfig();
    }

    /**
     * @param array $data
     * @return mixed
     * @throws PayException
     * @author helei
     */
    public function charges(array $data)
    {
        try {
            $data = $this->buildMobileData($data);
        } catch (PayException $e) {
            throw $e;
        }

        // 设置签名
        $data->setSign();

        // 微信统一下单接口
        $url = $this->config->getGetewayUrl() . 'pay/unifiedorder';

        // 获取用于请求的xml数据
        $xml = DataParser::toXml($data->getValues());

        // 进行curl请求
        $curl = new Curl();
        $ret = $curl->set([
            'CURLOPT_HEADER'    => 0
        ])->post($xml)->submit($url);

        // 格式化为数据
        $retArr = DataParser::toArray($ret['body']);
        if (! is_array($retArr)) {
            throw new PayException('微信支付数据解析错误');
        }

        // 返回结果
        return $this->handleRetData($retArr);
    }

    /**
     * @param array $data
     * @return array
     * @author helei
     */
    protected function handleRetData(array $data)
    {
        if ($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
            // 下单成功
            $payResult = new PayResultData();
            // 验证返回的结果签名
            if (! $payResult->signVerify($data)) {
                return [// 验证签名错误，说明是其他伪造
                    'type'  => 'err',
                    'data'  => [],
                ];
            }
            // 对返回结果签名，生成后，返回客户端
            $payResult->setPrepayId($data['prepay_id']);
            $payResult->setNonceStr(StrUtil::getNonceStr());
            $payResult->setTimestamp((string)time());
            $payResult->setSign();

            return [
                'type'  => 'succ',
                'data'  => $payResult->getValues(),
            ];
        }

        return [
            'type'  => 'err',
            'data'  => []
        ];
    }

    /**
     * 设置数据
     * @param array $data
     * @return UnifiedOrderData
     * @throws PayException
     * @author helei
     */
    protected function buildMobileData(array $data)
    {
        $mobile = new UnifiedOrderData();

        // 过滤值为空的数据
        $data = ArrayUtil::paraFilter($data);

        // 增加随机字符串
        $mobile->setNonceStr(StrUtil::getNonceStr());

        // 商品的描述信息
        if (array_key_exists('body', $data)) {
            $mobile->setBody($data['body']);
        } else {
            throw new PayException('body 参数必须设置');
        }

        // 设置商品的标题
        if (array_key_exists('subject', $data)) {
            $mobile->setDetail($data['subject']);
        } else {
            throw new PayException('subject 商品名称 参数必须设置');
        }

        // 设置微信回调时的回传参数
        if (array_key_exists('description', $data)) {
            if (mb_strlen($data['description'], 'utf-8') > 100) {
                throw new PayException('公共回传数据，不能超过100个字符');
            }

            $mobile->setAttach($data['description']);
        }

        // 设置商户的订单号
        if (array_key_exists('order_no', $data)) {
            if (strlen($data['order_no']) > 64) {
                throw new PayException('商户订单号，最多支持64位');
            }

            $mobile->setOutTradeNo($data['order_no']);
        } else {
            throw new PayException('order_no 商户订单号 参数必须设置');
        }

        // 订单总金额
        if (array_key_exists('amount', $data)) {
            // 此处向微信请求时，需要设置单位为分
            $amount = bcmul($data['amount'], 100, 0);
            if ($amount < 1) {
                throw new PayException('支付金额不能低于0.01 元');
            }

            $mobile->setTotalFee($amount);
        } else {
            throw new PayException('amount 订单总金额, 单位为对应币种的最小货币单位');
        }

        // 设置终端ip
        if (array_key_exists('client_ip', $data)) {
            $mobile->setSpbillCreateIp($data['client_ip']);
        }

        // 是否设置订单超时 。单位是分钟 .最短失效时间间隔必须大于5分钟
        if (array_key_exists('time_expire', $data)) {
            $time_expire = intval($data['time_expire']) * 60;// 单位转为秒
            if ($time_expire < 300) {
                // 小于5分钟，抛出异常
                throw new PayException('time_expire 对于微信支付，必须大于5分钟');
            }

            $time = time() + $time_expire - 60;// 为了降低误差，确保安全，人为减少1分钟

            $mobile->setTimeExpire(date('YmdHis', $time));
        }

        // 设置异步通知参数
        if (array_key_exists('success_url', $data)) {
            $mobile->setNotifyUrl($data['success_url']);
        } else {
            throw new PayException('success_url 服务器异步通知页面路径 参数必须设置');
        }

        // 交易类型
        $mobile->setTradeType($this->tradeType);

        // 指定支付方式
        if (array_key_exists('limit_pay', $data)) {
            $mobile->setLimitPay($data['limit_pay']);
        }

        return $mobile;
    }
}