<?php
/**
 * @author: helei
 * @createTime: 2016-06-08 16:19
 * @description: 支付宝通知的参数
 */

namespace Payment\Alipay\Data;


use Payment\Utils\ArrayUtil;
use Payment\Utils\RsaEncrypt;

class NotifyData extends AliBaseData
{

    /**
     * 进行签名
     * @param string $prestr
     * @return string
     * @author helei
     */
    protected function makeSign($prestr)
    {
        return '';
    }

    /**
     * 验证签名
     * @param array $data
     * @return bool
     * @author helei
     */
    public function signVerify(array $data)
    {
        // 本处由于回调的签名方式，可能存在多种，因此需要进行区分
        $sign_type = strtoupper($data['sign_type']);
        $sign = $data['sign'];

        // 1. 剔除sign与sign_type参数
        $values = ArrayUtil::removeKeys($data, ['sign', 'sign_type']);

        //  2. 移除数组中的空值
        $values = ArrayUtil::paraFilter($values);

        // 3. 对待签名参数数组排序
        $values = ArrayUtil::arraySort($values);

        // 4. 将排序后的参数与其对应值，组合成“参数=参数值”的格式,用&字符连接起来
        $prestr = ArrayUtil::createLinkstring($values);

        if ($sign_type == 'MD5') {
            // md5 的签名方式
            return md5($prestr . $this->config->getMd5Key()) == $sign;
        } elseif ($sign_type == 'RSA') {
            // rsa 签名  .由于通知类的签名，都是验证，因此固定使用公钥
            $public_key = file_get_contents($this->config->getRsaAliPublicKey());
            $rsa = new RsaEncrypt($public_key);

            return $rsa->rsaVerify($prestr, $sign);
        } else {
            return false;
        }
    }
}