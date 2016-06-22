<?php
/**
 * @author: helei
 * @createTime: 2016-06-11 14:53
 * @description:
 */

namespace Payment\Wxpay\Data;



use Payment\Utils\ArrayUtil;

class PayResultData extends WxBaseData
{
    public function __construct()
    {
        parent::__construct();
        $this->values = [
            'appid' => $this->config->getAppId(),
            'partnerid' => $this->config->getMchId(),
            'package'   => 'Sign=WXPay',
        ];
    }

    /**
     * 设置商户号
     * @param string $value
     * @author helei
     */
    public function setMchId($value)
    {
        $this->values['partnerid'] = $value;
    }

    public function getMchId()
    {
        if (array_key_exists('partnerid', $this->values)) {
            return $this->values['partnerid'];
        }

        return null;
    }

    /**
     * 预支付交易会话ID
     * @param $prepayid
     * @author helei
     */
    public function setPrepayId($prepayid)
    {
        $this->values['prepayid'] = $prepayid;
    }

    public function getPrepayId()
    {
        if (array_key_exists('prepayid', $this->values)) {
            return $this->values['prepayid'];
        }

        return null;
    }

    /**
     * 填写固定值Sign=WXPay
     * @param $package
     * @author helei
     */
    public function setPackage($package)
    {
        $this->values['package'] = $package;
    }

    public function getPackage()
    {
        if (array_key_exists('package', $this->values)) {
            return $this->values['package'];
        }

        return null;
    }

    /**
     * 设置时间戳
     * @param $timestamp
     * @author helei
     */
    public function setTimestamp($timestamp)
    {
        $this->values['timestamp'] = $timestamp;
    }

    public function getTimestamp()
    {
        if (array_key_exists('timestamp', $this->values)) {
            return $this->values['timestamp'];
        }

        return null;
    }

    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $value
     **/
    public function setNonceStr($value)
    {
        $this->values['noncestr'] = $value;
    }
    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     * @return string 值
     **/
    public function getNonceStr()
    {
        if (array_key_exists('noncestr', $this->values)) {
            return $this->values['noncestr'];
        }

        return null;
    }

    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $url
     **/
    public function setCodeUrl($url)
    {
        $this->values['code_url'] = urlencode($url);
    }
    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     * @return string 值
     **/
    public function getCodeUrl()
    {
        if (array_key_exists('code_url', $this->values)) {
            return urldecode($this->values['code_url']);
        }

        return null;
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