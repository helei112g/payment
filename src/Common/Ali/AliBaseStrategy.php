<?php
namespace Payment\Common\Ali;

use GuzzleHttp\Client;
use Payment\Common\AliConfig;
use Payment\Common\BaseData;
use Payment\Common\BaseStrategy;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\ArrayUtil;
use Payment\Utils\Rsa2Encrypt;
use Payment\Utils\RsaEncrypt;
use Payment\Utils\StrUtil;

/**
 * @author: helei
 * @createTime: 2016-07-15 17:10
 * @description: 支付宝支付接口的基类。
 * @link      https://www.gitbook.com/book/helei112g1/payment-sdk/details
 * @link      https://helei112g.github.io/
 */
abstract class AliBaseStrategy implements BaseStrategy
{
    /**
     * 支付宝的配置文件
     * @var AliConfig $config
     */
    protected $config;

    /**
     * 用于支付数据
     * @var BaseData $reqData
     */
    protected $reqData;

    /**
     * 网络请求类
     * @var Client $client
     */
    protected $client;

    /**
     * AliBaseStrategy constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        try {
            $this->config = new AliConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 实际执行操作，策略总控
     * @param array $data
     * @return array|string
     * @throws PayException
     */
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

        return $data;
    }

    /**
     * 支付宝业务发送网络请求，并验证签名
     * @param array $data
     * @return mixed
     * @throws PayException
     */
    protected function sendReq(array $data)
    {
        // 发起网络请求
        $response = $this->config->httpClient->request('GET', '', $data);
        var_dump($response);exit;

        /*$curl = new Curl();
        $responseTxt = $curl->set([
            'CURLOPT_SSL_VERIFYPEER'    => true,
            'CURLOPT_SSL_VERIFYHOST'    => 2,
            'CURLOPT_HEADER'    => 0,// 为了便于解析，将头信息过滤掉
            //'CURLOPT_CAINFO'    => $this->config->cacertPath,
        ])->get($url);*/

        if ($response['error']) {
            throw new PayException('网络发生错误，请稍后再试curl返回码：' . $response['message']);
        }

        $body = $response['body'];

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
