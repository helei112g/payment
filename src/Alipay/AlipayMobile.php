<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 09:27
 * @description: 支付宝  移动支付接口  集成
 */

namespace Payment\Alipay;



use Payment\Alipay\Data\MobileData;
use Payment\Contracts\ChargeInterface;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

class AlipayMobile implements ChargeInterface
{
    // 配置信息类
    protected $config;
    public function __construct()
    {
        $this->config = new AlipayConfig();
    }

    /**
     * 进行支付请求
     * @param array $data
     * @throws PayException
     * @return mixed
     * @author helei
     */
    public function charges(array $data)
    {
        try {
            $data = $this->buildMobileData($data);
        } catch (PayException $e) {
            throw $e;
        }

        $data->handleData($data->getValues());
        // 设置签名
        $data->setSign();
        // 设置签名的引号
        $queryArr = ArrayUtil::arraySort($data->getValues());
        $queryArr['sign'] = '"' . $queryArr['sign'] . '"';

        // 生成查询字符串
        //$queryStr = ArrayUtil::createLinkstring(ArrayUtil::arraySort($queryArr));
        //$queryStr = htmlspecialchars($queryStr);

        return [
            'type'  => 'succ',
            'data'  => $queryArr
        ];
    }

    /**
     * 构建用户手机支付的数据
     * @param array $data
     * @note 移动支付的数据，进去请求时，需要使用：key="value"的参数。一定要对value加双引号
     *
     * @return MobileData
     * @throws PayException
     * @author helei
     */
    protected function buildMobileData(array $data)
    {
        $mobile = new MobileData();

        // 过滤值为空的数据
        $data = ArrayUtil::paraFilter($data);

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
            // 此处向支付请求时，需要设置单位为元
            /*$amount = bcdiv($data['amount'], 100, 2);*/
            if (bccomp($data['amount'], 0.01, 2) === -1) {
                throw new PayException('支付金额不能低于0.01 元');
            }

            $mobile->setTotalFee($data['amount']);
        } else {
            throw new PayException('amount 订单总金额, 单位为对应币种的最小货币单位');
        }

        // 设置商品的标题
        if (array_key_exists('subject', $data)) {
            $mobile->setSubject($data['subject']);
        } else {
            throw new PayException('subject 商品名称 参数必须设置');
        }

        // 商品的描述信息
        if (array_key_exists('body', $data)) {
            $mobile->setBody($data['body']);
        } else {
            throw new PayException('body 参数必须设置');
        }

        // 设置异步通知参数
        if (array_key_exists('success_url', $data)) {
            $mobile->setNotifyUrl($data['success_url']);
        } else {
            throw new PayException('success_url 服务器异步通知页面路径 参数必须设置');
        }

        // 是否设置订单超时 。单位是分钟 .最多不超过15天。最小不小于1分钟
        if (array_key_exists('time_expire', $data)) {
            $time_expire = intval($data['time_expire']);
            if ($time_expire < 1 || $time_expire > 21600) {
                throw new PayException('未付款交易的超时时间 不能低于1分钟，不能大于15天');
            }

            $mobile->setItBPay($time_expire);
        }

        // 设置支付宝回调时的回传参数
        if (array_key_exists('description', $data)) {
            if (mb_strlen($data['description'], $this->config->getInputCharset()) > 100) {
                throw new PayException('公共回传数据，不能超过100个字符');
            }

            $mobile->setExtraCommonParam($data['description']);
        }

        return $mobile;
    }
}