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
 * @property string $body
 * @property string $subject
 * @property string $order_no
 * @property integer $timeout_express
 * @property string $amount
 * @property string $goods_type
 * @property string $return_param
 * @property string $store_id  	商户门店编号
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
        $signData = [
            // 公共参数
            'app_id'        => $this->appId,
            'method'        => $this->method,
            'format'        => $this->format,
            'return_url'    => $this->returnUrl,
            'charset'       => $this->charset,
            'sign_type'     => $this->signType,
            'timestamp'     => $this->timestamp,
            'version'       => $this->version,
            'notify_url'    => $this->notifyUrl,

            // 业务参数
            'biz_content'   => $this->getBizContent(),
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    /**
     * 支付宝构建请求支付的数据
     * @return mixed
     */
    abstract protected function getBizContent();


    /**
     * 检查传入的支付业务参数是否正确
     *
     * 如果输入参数不符合规范，直接抛出异常
     *
     * @author helei
     */
    protected function checkDataParam()
    {
        $subject = $this->subject;
        $orderNo = $this->order_no;
        $amount = $this->amount;
        $goodsType = $this->goods_type;
        $passBack = $this->return_param;

        // 检查订单号是否合法
        if (empty($orderNo) || mb_strlen($orderNo) > 64) {
            throw new PayException('订单号不能为空，并且长度不能超过64位');
        }

        // 检查金额不能低于0.01，不能大于 100000000.00
        if (bccomp($amount, Config::PAY_MIN_FEE, 2) === -1) {
            throw new PayException('支付金额不能低于 ' . Config::PAY_MIN_FEE . ' 元');
        }
        if (bccomp($amount, Config::PAY_MAX_FEE, 2) === 1) {
            throw new PayException('支付金额不能大于 ' . Config::PAY_MAX_FEE . ' 元');
        }

        // 检查 商品名称 与 商品描述
        if (empty($subject)) {
            throw new PayException('必须提供 商品的标题/交易标题/订单标题/订单关键字 等');
        }

        // 检查商品类型
        if (empty($goodsType)) {// 默认为实物类商品
            $this->goods_type = 1;
        } elseif (! in_array($goodsType, [0 ,1])) {
            throw new PayException('商品类型可取值为：0-虚拟类商品  1-实物类商品');
        }

        // 返回参数进行urlencode编码
        if (! empty($passBack) && ! is_string($passBack)) {
            throw new PayException('回传参数必须是字符串');
        }
        $this->return_param = urlencode($passBack);
    }
}
