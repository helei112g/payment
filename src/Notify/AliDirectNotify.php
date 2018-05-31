<?php
/**
 * Created by ChaXian.
 * User: Bing
 * Date: 2018/5/31
 * Time: 17:31
 */

namespace Payment\Notify;


use Payment\Common\AliDirectConfig;
use Payment\Utils\Md5Encrypt;

class AliDirectNotify extends AliNotify
{
    public function __construct(array $config)
    {
        try {
            $this->config = new AliDirectConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    protected function verifySign(array  $data)
    {
        $signType = strtoupper($data['sign_type']);

        if ($signType !== 'MD5') {
            return false;
        }

        $sign = $data['sign'];
        // 1. 剔除sign与sign_type参数
        $values = ArrayUtil::removeKeys($data, ['sign', 'sign_type']);
        //  2. 移除数组中的空值
        $values = ArrayUtil::paraFilter($values);
        // 3. 对待签名参数数组排序
        $values = ArrayUtil::arraySort($values);
        // 4. 将排序后的参数与其对应值，组合成“参数=参数值”的格式,用&字符连接起来
        $preStr = ArrayUtil::createLinkstring($values);

        $rsa = new Md5Encrypt($this->config->key);

        return $rsa->rsaVerify($preStr, $sign);
    }
}