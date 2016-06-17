<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 14:53
 * @description:
 */

namespace Payment\Wxpay\Data;



use Payment\Utils\ArrayUtil;

class JSPayResultData extends PayResultData
{
    public function __construct()
    {
        parent::__construct();
        $this->values = [
            'appId' => $this->config->getAppId(),
            'signType'  => strtoupper('MD5'),
        ];
    }

    /**
     * 设置时间戳
     * @param $timestamp
     * @author helei
     */
    public function setTimestamp($timestamp)
    {
        $this->values['timeStamp'] = $timestamp;
    }

    public function getTimestamp()
    {
        if (array_key_exists('timeStamp', $this->values)) {
            return $this->values['timeStamp'];
        }

        return null;
    }

    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $value
     **/
    public function setNonceStr($value)
    {
        $this->values['nonceStr'] = $value;
    }
    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     * @return string 值
     **/
    public function getNonceStr()
    {
        if (array_key_exists('nonceStr', $this->values)) {
            return $this->values['nonceStr'];
        }

        return null;
    }

    /**
     * 填写固定值Sign=WXPay
     * @param string $prepay_id  统一下单接口返回的prepay_id参数值
     * @author helei
     */
    public function setPackage($prepay_id)
    {
        $this->values['package'] = 'prepay_id=' . $prepay_id;
    }

    public function getPackage()
    {
        if (array_key_exists('package', $this->values)) {
            return $this->values['package'];
        }

        return null;
    }

    /**
     * 设置签名，详见签名生成算法
     * @return string
     **/
    public function setSign()
    {
        // 1. 剔除sign与sign_type参数
        $values = ArrayUtil::removeKeys($this->values, 'sign');

        //2. 移除数组中的空值
        $values = ArrayUtil::paraFilter($values);

        //3. 按字典序排序参数
        $values = ArrayUtil::arraySort($values);

        // 4. 将排序后的参数与其对应值，组合成“参数=参数值”的格式,用&字符连接起来
        $prestr = ArrayUtil::createLinkstring($values);

        $this->values['paySign'] = $this->makeSign($prestr);
        return $this->values['paySign'];
    }

    /**
     * 获取签名，详见签名生成算法的值
     * @return string
     **/
    public function getSign()
    {
        return $this->values['paySign'];
    }

    /**
     * 检查签名是否正确
     * @param array $data
     * @return boolean
     * @author helei
     */
    public function signVerify(array $data)
    {
        $wxSign = $data['sign'];
        // 1. 剔除sign与sign_type参数
        $values = ArrayUtil::removeKeys($data, ['sign']);

        //  2. 移除数组中的空值
        $values = ArrayUtil::paraFilter($values);

        // 3. 对待签名参数数组排序
        $values = ArrayUtil::arraySort($values);

        // 4. 将排序后的参数与其对应值，组合成“参数=参数值”的格式,用&字符连接起来
        $prestr = ArrayUtil::createLinkstring($values);

        // 进行签名
        $string = $prestr . "&key=" . $this->config->getMd5Key();

        $string = md5($string);

        $sign = strtoupper($string);
        return $sign == $wxSign;
    }
}