<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 11:45
 * @description:
 */

namespace Payment\Wxpay\Data;


class UnifiedOrderData extends WxBaseData
{

    public function __construct()
    {
        parent::__construct();
        $this->values = [
            'appid' => $this->config->getAppId(),
            'mch_id'    => $this->config->getMchId(),
            'fee_type'  => 'CNY'
        ];
    }

    /**
     * 设置微信支付分配的终端设备号，商户自定义
     * @param string $value
     **/
    public function setDeviceInfo($value)
    {
        $this->values['device_info'] = $value;
    }
    /**
     * 获取微信支付分配的终端设备号，商户自定义的值
     * @return string 值
     **/
    public function getDeviceInfo()
    {
        if (array_key_exists('device_info', $this->values)) {
            return $this->values['device_info'];
        }

        return null;
    }

    /**
     * 设置商品或支付单简要描述
     * @param string $value
     **/
    public function setBody($value)
    {
        $this->values['body'] = $value;
    }
    /**
     * 获取商品或支付单简要描述的值
     * @return string 值
     **/
    public function getBody()
    {
        if (array_key_exists('body', $this->values)) {
            return $this->values['body'];
        }

        return null;
    }

    /**
     * 设置商品名称明细列表
     * @param string $value
     **/
    public function setDetail($value)
    {
        $this->values['detail'] = $value;
    }
    /**
     * 获取商品名称明细列表的值
     * @return string 值
     **/
    public function getDetail()
    {
        if (array_key_exists('detail', $this->values)) {
            return $this->values['detail'];
        }

        return null;
    }

    /**
     * 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
     * @param string $value
     **/
    public function setAttach($value)
    {
        $this->values['attach'] = $value;
    }
    /**
     * 获取附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据的值
     * @return string 值
     **/
    public function getAttach()
    {
        if (array_key_exists('attach', $this->values)) {
            return $this->values['attach'];
        }

        return null;
    }

    /**
     * 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
     * @param string $value
     **/
    public function setOutTradeNo($value)
    {
        $this->values['out_trade_no'] = $value;
    }
    /**
     * 获取商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号的值
     * @return string 值
     **/
    public function getOutTradeNo()
    {
        if (array_key_exists('out_trade_no', $this->values)) {
            return $this->values['out_trade_no'];
        }

        return null;
    }


    /**
     * 设置符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
     * @param string $value
     **/
    public function setFeeType($value)
    {
        $this->values['fee_type'] = $value;
    }
    /**
     * 获取符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型的值
     * @return string 值
     **/
    public function getFeeType()
    {
        if (array_key_exists('fee_type', $this->values)) {
            return $this->values['fee_type'];
        }

        return null;
    }

    /**
     * 设置订单总金额，只能为整数，详见支付金额
     * @param string $value
     **/
    public function setTotalFee($value)
    {
        $this->values['total_fee'] = $value;
    }
    /**
     * 获取订单总金额，只能为整数，详见支付金额的值
     * @return string 值
     **/
    public function getTotalFee()
    {
        if (array_key_exists('total_fee', $this->values)) {
            return $this->values['total_fee'];
        }

        return null;
    }

    /**
     * 设置APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
     * @param string $value
     **/
    public function setSpbillCreateIp($value)
    {
        $this->values['spbill_create_ip'] = $value;
    }
    /**
     * 获取APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。的值
     * @return string 值
     **/
    public function getSpbillCreateIp()
    {
        if (array_key_exists('spbill_create_ip', $this->values)) {
            return $this->values['spbill_create_ip'];
        }

        return null;
    }

    /**
     * 设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
     * @param string $value
     **/
    public function setTimeStart($value)
    {
        $this->values['time_start'] = $value;
    }
    /**
     * 获取订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则的值
     * @return string 值
     **/
    public function GetTime_start()
    {
        if (array_key_exists('time_start', $this->values)) {
            return $this->values['time_start'];
        }

        return null;
    }

    /**
     * 设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
     *  - 最短失效时间间隔必须大于5分钟
     * @param string $value
     **/
    public function setTimeExpire($value)
    {
        $this->values['time_expire'] = $value;
    }

    /**
     * 获取订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则的值
     * @return string 值
     **/
    public function getTimeExpire()
    {
        if (array_key_exists('time_expire', $this->values)) {
            return $this->values['time_expire'];
        }

        return null;
    }


    /**
     * 设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
     * @param string $value
     **/
    public function setGoodsTag($value)
    {
        $this->values['goods_tag'] = $value;
    }
    /**
     * 获取商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠的值
     * @return string 值
     **/
    public function getGoodsTag()
    {
        if (array_key_exists('goods_tag', $this->values)) {
            return $this->values['goods_tag'];
        }

        return null;
    }

    /**
     * 设置接收微信支付异步通知回调地址
     * @param string $value
     **/
    public function setNotifyUrl($value)
    {
        $this->values['notify_url'] = $value;
    }
    /**
     * 获取接收微信支付异步通知回调地址的值
     * @return string 值
     **/
    public function getNotifyUrl()
    {
        if (array_key_exists('notify_url', $this->values)) {
            return $this->values['notify_url'];
        }

        return null;
    }

    /**
     * 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
     * @param string $value
     **/
    public function setTradeType($value)
    {
        $this->values['trade_type'] = $value;
    }
    /**
     * 获取取值如下：JSAPI，NATIVE，APP，详细说明见参数规定的值
     * @return string 值
     **/
    public function GetTradeType()
    {
        if (array_key_exists('trade_type', $this->values)) {
            return $this->values['trade_type'];
        }

        return null;
    }

    /**
     * 设置trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
     * @param string $value
     **/
    public function setProductId($value)
    {
        $this->values['product_id'] = $value;
    }
    /**
     * 获取trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。的值
     * @return string 值
     **/
    public function getProductId()
    {
        if (array_key_exists('product_id', $this->values)) {
            return $this->values['product_id'];
        }

        return null;
    }

    /**
     * 设置trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。
     * @param string $value
     **/
    public function setOpenId($value)
    {
        $this->values['openid'] = $value;
    }
    /**
     * 获取trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。 的值
     * @return string 值
     **/
    public function getOpenId()
    {
        if (array_key_exists('product_id', $this->values)) {
            return $this->values['product_id'];
        }

        return null;
    }

    /**
     * 指定支付方式
     * @param $limit_pay
     * @author helei
     */
    public function setLimitPay($limit_pay)
    {
        $this->values['limit_pay'] = $limit_pay;
    }

    /**
     * 指定支付方式
     * @return null
     * @author helei
     */
    public function getLimitPay()
    {
        if (array_key_exists('limit_pay', $this->values)) {
            return $this->values['limit_pay'];
        }

        return null;
    }
}