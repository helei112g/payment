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
     * AliCharge constructor.
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

    /**
     * 获取支付对应的数据完成类
     * @return BaseData
     * @author helei
     */
    abstract protected function getBuildDataClass();

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

    protected function sendReq($url, $responseKey)
    {
        // 发起网络请求
        $curl = new Curl();
        $responseTxt = $curl->set([
            'CURLOPT_SSL_VERIFYPEER'    => true,
            'CURLOPT_SSL_VERIFYHOST'    => 2,
            'CURLOPT_HEADER'    => 0,// 为了便于解析，将头信息过滤掉
        ])->get($url);

        if ($responseTxt['error']) {
            throw new PayException('网络发生错误，请稍后再试');
        }

        // TODO  验证签名，检查支付宝返回的数据

        $body = $responseTxt['body'];

        $data = json_decode($body, true)[$responseKey];
        if ($data['code'] != 10000) {
            throw new PayException($data['sub_msg']);
        }

        return $data;
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
}
