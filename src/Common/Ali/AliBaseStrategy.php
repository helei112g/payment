<?php
/**
 * @author: helei
 * @createTime: 2016-07-15 17:10
 * @description: 支付宝支付接口的基类。
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Common\Ali;

use Payment\Common\AliConfig;
use Payment\Common\BaseData;
use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\ArrayUtil;
use Payment\Utils\Curl;
use Payment\Utils\Rsa2Encrypt;
use Payment\Utils\RsaEncrypt;
use Payment\Utils\StrUtil;

abstract class AliBaseStrategy implements BaseStrategy
{
    /**
     * 支付宝的配置文件
     * @var AliConfig $config
     */
    protected $config;

    /**
     * 支付数据
     * @var BaseData $reqData
     */
    protected $reqData;

    /**
     * AliBaseStrategy constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        try {
            $this->config = new AliConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    public function handle(array $data)
    {
        $buildClass = $this->getBuildDataClass();

        try {
            $this->reqData = new $buildClass($this->config, $data);
        } catch (PayException $e) {
            throw $e;
        }

        $this->reqData->setSign();

        $data = $this->reqData->getData();

        return $this->retData($data);
    }

    /**
     * 支付宝业务发送网络请求，并验证签名
     * @param $url
     * @return mixed
     * @throws PayException
     */
    protected function sendReq($url)
    {
        // 发起网络请求
        $curl = new Curl();
        $responseTxt = $curl->set([
            'CURLOPT_SSL_VERIFYPEER'    => true,
            'CURLOPT_SSL_VERIFYHOST'    => 2,
            'CURLOPT_HEADER'    => 0,// 为了便于解析，将头信息过滤掉
            //'CURLOPT_CAINFO'    => $this->config->cacertPath,
        ])->get($url);

        if ($responseTxt['error']) {
            throw new PayException('网络发生错误，请稍后再试curl返回码：' . $responseTxt['message']);
        }

        $body = $responseTxt['body'];

        $responseKey = str_ireplace('.', '_', $this->config->method . '.response');

        $body = json_decode($body, true);
        if ($body[$responseKey]['code'] != 10000) {
            throw new PayException($body[$responseKey]['sub_msg']);
        }

        // 验证签名，检查支付宝返回的数据
        $flag = $this->verifySign($body[$responseKey], $body['sign']);
        if (! $flag) {
            throw new PayException('支付宝返回数据被篡改。请检查网络是否安全！');
        }

        return $body[$responseKey];
    }

    /**
     * 处理支付宝的返回值并返回给客户端
     * @param array $data
     * @return string|array
     * @author helei
     */
    protected function retData(array $data)
    {
        $sign = $data['sign'];
        $data = ArrayUtil::removeKeys($data, ['sign']);

        $data = ArrayUtil::arraySort($data);

        // 支付宝新版本  需要转码
        foreach ($data as &$value) {
            $value = StrUtil::characet($value, $this->config->charset);
        }

        $data['sign'] = $sign;// sign  需要放在末尾

        return $this->config->getewayUrl . http_build_query($data);
    }

    /**
     * 返回统一的交易状态  做一些转化，方便处理
     * @param $status
     * @return string
     * @author helei
     */
    protected function getTradeStatus($status)
    {
        switch ($status) {
            case 'TRADE_SUCCESS':
                //no break
            case 'TRADE_FINISHED':
                return Config::TRADE_STATUS_SUCC;

            case 'WAIT_BUYER_PAY':
            case 'TRADE_CLOSED':
            default:
                return Config::TRADE_STATUS_FAILD;
        }
    }

    /**
     * 检查支付宝数据 签名是否被篡改
     * @param array $data
     * @param string $sign  支付宝返回的签名结果
     * @return bool
     * @author helei
     */
    protected function verifySign(array $data, $sign)
    {
        $preStr = json_encode($data);

        if ($this->config->signType === 'RSA') {// 使用RSA
            $rsa = new RsaEncrypt($this->config->rsaAliPubKey);

            return $rsa->rsaVerify($preStr, $sign);
        } elseif ($this->config->signType === 'RSA2') {// 使用rsa2方式
            $rsa = new Rsa2Encrypt($this->config->rsaAliPubKey);

            return $rsa->rsaVerify($preStr, $sign);
        } else {
            return false;
        }
    }
}
