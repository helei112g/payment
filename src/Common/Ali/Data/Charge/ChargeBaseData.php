<?php
/**
 * @author: helei
 * @createTime: 2016-07-20 14:33
 * @description:
 */

namespace Payment\Common\Ali\Data\Charge;


use Payment\Common\Ali\Data\AliBaseData;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\ArrayUtil;

/**
 * Class ChargeBaseData
 *
 * @inheritdoc
 *
 * @property string $order_no
 * @property string $amount
 * @property string $client_ip
 * @property string $subject
 * @property string $body
 * @property string $extra_param
 * @property string $show_url
 *
 * @package Payment\Common\Ali\Data\Charge
 * anthor helei
 */
abstract class ChargeBaseData extends AliBaseData
{

    /**
     * 构建 APP支付 加密数据
     * @author helei
     */
    protected function buildData()
    {
        $timeExpire = $this->timeExpire;
        $version = $this->version;

        if ($version) {
            $signData = $this->alipay2_0Data($timeExpire);
        } else {
            $signData = $this->alipay1_0Data($timeExpire);
        }

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    /**
     * 支付宝老版本构建数据
     * @param string $timeExpire
     * @return mixed
     */
    abstract protected function alipay1_0Data($timeExpire = '');

    /**
     * 支付宝新版本构建数据
     * @param string $timeExpire
     * @return mixed
     */
    abstract protected function alipay2_0Data($timeExpire = '');

    /**
     * 检查传入的支付参数是否正确
     *
     * 如果输入参数不符合规范，直接抛出异常
     *
     * @author helei
     */
    protected function checkDataParam()
    {
        $orderNo = $this->order_no;
        $amount = $this->amount;
        $clientIp = $this->client_ip;
        $subject = $this->subject;
        $body = $this->body;

        // 检查订单号是否合法
        if (empty($orderNo) || mb_strlen($orderNo) > 64) {
            throw new PayException('订单号不能为空，并且长度不能超过64位');
        }

        // 检查金额不能低于0.01，不能大于 100000.00
        if (bccomp($amount, Config::PAY_MIN_FEE, 2) === -1) {
            throw new PayException('支付金额不能低于 ' . Config::PAY_MIN_FEE . ' 元');
        }
        if (bccomp($amount, Config::PAY_MAX_FEE, 2) === 1) {
            throw new PayException('支付金额不能大于 ' . Config::PAY_MAX_FEE . ' 元');
        }

        $version = $this->version;
        // 检查ip地址  老版本才需要检查
        if (
            empty($version) &&
            (empty($clientIp) || ! preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $clientIp))
        ) {
            throw new PayException('IP 地址必须上传，并且以IPV4的格式');
        }

        // 检查 商品名称 与 商品描述
        if (empty($subject) || empty($body)) {
            throw new PayException('必须提供商品名称与商品描述');
        }
    }
}