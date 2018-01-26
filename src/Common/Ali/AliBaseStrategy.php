<?php
namespace Payment\Common\Ali;

use GuzzleHttp\Client;
use InvalidArgumentException;
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
    // 支付接口名称
    protected $method;

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
     * @param string $method 网络请求的方法， get post 等
     * @return mixed
     * @throws PayException
     */
    protected function sendReq(array $data, $method = 'GET')
    {
        $client = new Client([
            'base_uri' => $this->config->getewayUrl,
            'timeout' => '10.0'
        ]);
        $method = strtoupper($method);
        $options = [];
        if ($method === 'GET') {
            $options = [
                'query' => $data,
                'http_errors' => false
            ];
        } elseif ($method === 'POST') {
            $options = [
                'form_params' => $data,
                'http_errors' => false
            ];
        }
        // 发起网络请求
        $response = $client->request($method, '', $options);

        if ($response->getStatusCode() != '200') {
            throw new PayException('网络发生错误，请稍后再试curl返回码：' . $response->getReasonPhrase());
        }

        $body = $response->getBody()->getContents();
        try {
            $body = \GuzzleHttp\json_decode($body, true);
        } catch (InvalidArgumentException $e) {
            throw new PayException('返回数据 json 解析失败');
        }

        $responseKey = str_ireplace('.', '_', $this->config->method) . '_response';
        if (! isset($body[$responseKey])) {
            throw new PayException('支付宝系统故障或非法请求');
        }

        // 验证签名，检查支付宝返回的数据
        $flag = $this->verifySign($body[$responseKey], $body['sign']);
        if (! $flag) {
            throw new PayException('支付宝返回数据被篡改。请检查网络是否安全！');
        }

        // 这里可能带来不兼容问题。原先会检查code ，不正确时会抛出异常，而不是直接返回
        return $body[$responseKey];
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
        $preStr = \GuzzleHttp\json_encode($data, JSON_UNESCAPED_UNICODE);// 主要是为了解决中文问题

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

    /**
     * 返回统一的交易状态  做一些转化，方便处理
     *
     * 交易状态：
     *  - WAIT_BUYER_PAY（交易创建，等待买家付款）
     *  - TRADE_CLOSED（未付款交易超时关闭，或支付完成后全额退款）
     *  - TRADE_SUCCESS（交易支付成功）
     *  - TRADE_FINISHED（交易结束，不可退款）
     *
     * @param $status
     * @return string
     * @author helei
     */
    protected function getTradeStatus($status)
    {
        switch ($status) {
            case 'TRADE_SUCCESS':// 交易支付成功
            case 'TRADE_FINISHED':// 交易结束，不可退款
                return Config::TRADE_STATUS_SUCC;

            case 'WAIT_BUYER_PAY':// 交易创建，等待买家付款
            case 'TRADE_CLOSED':// 未付款交易超时关闭，或支付完成后全额退款
            default:
                return Config::TRADE_STATUS_FAILD;
        }
    }
}
