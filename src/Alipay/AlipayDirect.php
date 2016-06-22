<?php
/**
 * @author: helei
 * @createTime: 2016-06-07 19:50
 * @description: 使用 支付宝 即时到账接口，完成支付操作
 */

namespace Payment\Alipay;



use Payment\Alipay\Data\DirectData;
use Payment\Contracts\ChargeInterface;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

class AlipayDirect implements ChargeInterface
{
    // 配置信息类
    protected $config;
    public function __construct()
    {
        $this->config = new AlipayConfig();
    }

    /**
     * 即时到账接口，返回的是一个url，可直接 通过 get请求，进行支付宝的访问
     * @param array $data 支付需要的参数
     * @return mixed
     * @throws PayException
     * @author helei
     */
    public function charges(array $data)
    {
        try {
            $data = $this->buildDirectData($data);
        } catch (PayException $e) {
            throw $e;
        }

        // 进行签名设置
        $data->setSign();

        // 生成查询字符串
        $queryStr = ArrayUtil::createLinkstring($data->getValues());
        //$queryStr = htmlspecialchars($queryStr);

        // 生成可进行get请求的url
        $url = $this->config->getGetewayUrl() .$queryStr;
        return $url;
    }

    /**
     * 构造需要的数据请求数据
     * @param array $data
     * @return DirectData
     * @throws PayException
     * @author helei
     */
    protected function buildDirectData(array $data)
    {
        $direct = new DirectData();

        // 过滤值为空的数据
        $data = ArrayUtil::paraFilter($data);

        // 设置商户的订单号
        if (array_key_exists('order_no', $data)) {
            if (strlen($data['order_no']) > 64) {
                throw new PayException('商户订单号，最多支持64位');
            }

            $direct->setOutTradeNo($data['order_no']);
        } else {
            throw new PayException('order_no 商户订单号 参数必须设置');
        }

        // 订单总金额
        if (array_key_exists('amount', $data)) {
            // 此处向支付请求时，需要设置单位为元
            /*$amount = bcdiv($data['amount'], 100, 2);*/
            if (bccomp($data['amount'], 0.01, 2) === -1) {
                throw new PayException('支付金额不能低于0.01 元');
            }

            $direct->setTotalFee($data['amount']);
        } else {
            throw new PayException('amount 订单总金额, 单位为对应币种的最小货币单位');
        }

        // 设置客户端ip
        if (array_key_exists('client_ip', $data)) {
            $direct->setExterInvokeIp($data['client_ip']);
        }

        // 设置商品的标题
        if (array_key_exists('subject', $data)) {
            $direct->setSubject($data['subject']);
        } else {
            throw new PayException('subject 商品名称 参数必须设置');
        }

        // 商品的描述信息
        if (array_key_exists('body', $data)) {
            $direct->setBody($data['body']);
        } else {
            throw new PayException('body 参数必须设置');
        }

        // 设置异步通知参数
        if (array_key_exists('success_url', $data)) {
            $direct->setNotifyUrl($data['success_url']);
        } else {
            throw new PayException('success_url 服务器异步通知页面路径 参数必须设置');
        }

        // 是否设置订单超时 。单位是分钟 .最多不超过15天。最小不小于1分钟
        if (array_key_exists('time_expire', $data)) {
            $time_expire = intval($data['time_expire']);
            if ($time_expire < 1 || $time_expire > 21600) {
                throw new PayException('未付款交易的超时时间 不能低于1分钟，不能大于15天');
            }

            $direct->setItBPay($time_expire);
        }

        // 设置支付宝回调时的回传参数
        if (array_key_exists('description', $data)) {
            if (mb_strlen($data['description'], $this->config->getInputCharset()) > 100) {
                throw new PayException('公共回传数据，不能超过100个字符');
            }

            $direct->setExtraCommonParam($data['description']);
        }

        // 设置支付成功后的同步地址
        if (array_key_exists('return_url', $data)) {
            $direct->setReturnUrl($data['return_url']);
        }

        // 设置商品展示网址
        if (array_key_exists('show_url', $data)) {
            $direct->setShowUrl($data['total_fee']);
        }

        return $direct;
    }
}