<?php
/**
 * @author: helei
 * @createTime: 2016-06-17 13:13
 * @description:
 */

namespace Payment\Wxpay\Helper;


use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;
use Payment\Utils\StrUtil;
use Payment\Wxpay\Data\UnifiedOrderData;

class WxUnifiedOrder
{
    /**
     * 创建微信支付进行支付的数据
     *
     * @param array $data
     * @param $tradeType
     *  - @see WxTradeType
     *
     * @return UnifiedOrderData
     * @throws PayException
     * @author helei
     */
    public static function createUnifiedData(array $data, $tradeType)
    {
        $tradeType = strtoupper($tradeType);
        // 过滤值为空的数据
        $data = ArrayUtil::paraFilter($data);

        static::checkParam($data, $tradeType);

        $unified = new UnifiedOrderData();

        // 增加随机字符串
        $unified->setNonceStr(StrUtil::getNonceStr());

        // 商品的描述信息
        if (array_key_exists('body', $data)) {
            $unified->setBody($data['body']);
        } else {
            throw new PayException('body 参数必须设置');
        }

        // 设置商品详情
        if (array_key_exists('subject', $data)) {
            $unified->setDetail($data['subject']);
        }

        // 设置微信回调时的回传参数
        if (array_key_exists('description', $data)) {
            if (mb_strlen($data['description'], 'utf-8') > 100) {
                throw new PayException('公共回传数据，不能超过100个字符');
            }

            $unified->setAttach($data['description']);
        }

        // 设置商户的订单号
        if (array_key_exists('order_no', $data)) {
            if (strlen($data['order_no']) > 64) {
                throw new PayException('商户订单号，最多支持64位');
            }

            $unified->setOutTradeNo($data['order_no']);
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

            $unified->setTotalFee($amount);
        } else {
            throw new PayException('amount 订单总金额, 单位为对应币种的最小货币单位');
        }

        // 设置终端ip
        if (array_key_exists('client_ip', $data)) {
            $unified->setSpbillCreateIp($data['client_ip']);
        } else {
            throw new PayException('微信支付，必须提供客户端ip');
        }

        // 是否设置订单超时 。单位是分钟 .最短失效时间间隔必须大于5分钟
        if (array_key_exists('time_expire', $data)) {
            $time_expire = intval($data['time_expire']) * 60;// 单位转为秒
            if ($time_expire < 300) {
                // 小于5分钟，抛出异常
                throw new PayException('time_expire 对于微信支付，必须大于5分钟');
            }

            $time = time() + $time_expire - 60;// 为了降低误差，确保安全，人为减少1分钟

            $unified->setTimeExpire(date('YmdHis', $time));
        }

        // 设置异步通知参数
        if (array_key_exists('success_url', $data)) {
            $unified->setNotifyUrl($data['success_url']);
        } else {
            throw new PayException('success_url 服务器异步通知页面路径 参数必须设置');
        }

        // 交易类型
        $unified->setTradeType($tradeType);

        // 设置不同类型时的特殊参数
        static::setSpecialParam($unified, $data, $tradeType);

        // 指定支付方式
        if (array_key_exists('limit_pay', $data)) {
            $unified->setLimitPay($data['limit_pay']);
        }

        return $unified;
    }

    /**
     * 检查数据
     * @param array $data
     * @param $tradeType
     * @author helei
     * @throws PayException
     */
    protected static function checkParam(array $data, $tradeType)
    {
        $tradeTypeList = WxTradeType::getValuesList();
        if (!in_array($tradeType, $tradeTypeList)) {
            $tradeTypeStr = implode(',', $tradeTypeList);
            throw new PayException("当前交易方式，系统尚不支持，目前仅支持：{$tradeTypeStr}");
        }
        
        // 支付方式是 jsapi时 ， 公众号支付
        if ($tradeType === WxTradeType::TYPE_IS_JSAPI) {
            if (!isset($data['openid']) || empty($data['openid'])) {
                throw new PayException('支付方式为：JSAPI时，必须提供用户的 openid');
            }
        }

        // 支付方式是 native 时，扫码支付
        if ($tradeType === WxTradeType::TYPE_IS_NATIVE) {
            if (!isset($data['product_id']) || empty($data['product_id'])) {
                throw new PayException('支付方式为：NATIVE时，必须提供用户的 product_id');
            }
        }
    }

    /**
     * @param UnifiedOrderData $unified
     * @param array $data
     * @param $tradeType
     * @throws PayException
     * @author helei
     */
    protected static function setSpecialParam(UnifiedOrderData $unified, array $data, $tradeType)
    {
        // NATIVE，必须设置商品ID 长度为32位
        if ($tradeType == WxTradeType::TYPE_IS_NATIVE) {
            if (key_exists('product_id', $data)) {
                if (strlen($data['product_id']) > 32) {
                    throw new PayException('二维码中包含的商品ID，最多支持32位');
                }

                $unified->setProductId($data['product_id']);
            } else {
                throw new PayException('支付类型是 NATIVE 时，必须提供商品ID');
            }
        }

        // JSAPI，必须设置用户标识
        if ($tradeType == WxTradeType::TYPE_IS_JSAPI) {
            if (key_exists('openid', $data)) {
                if (strlen($data['openid']) > 128) {
                    throw new PayException('用户在商户appid下的唯一标识，最多128位，该id是微信返回信息，不可自定义');
                }

                $unified->setOpenId($data['openid']);
            } else {
                throw new PayException('支付类型是 JSAPI 时，必须提供用户标识openid');
            }
        }
    }
}