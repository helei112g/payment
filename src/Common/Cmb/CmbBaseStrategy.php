<?php

namespace Payment\Common\Cmb;

use Payment\Common\BaseData;
use Payment\Common\BaseStrategy;
use Payment\Common\CmbConfig;
use Payment\Common\PayException;
use Payment\Config;
use Payment\Utils\Curl;

/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/27
 * Time: 下午12:36
 */
abstract class CmbBaseStrategy implements BaseStrategy
{
    /**
     * 招商的配置文件
     * @var CmbConfig $config
     */
    protected $config;

    /**
     * 请求数据
     * @var BaseData $reqData
     */
    protected $reqData;

    /**
     * CmbBaseStrategy constructor.
     * @param array $config
     * @throws PayException
     */
    public function __construct(array $config)
    {
        /* 设置内部字符编码为 UTF-8 */
        mb_internal_encoding("UTF-8");

        try {
            $this->config = new CmbConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    /**
     * 所有支付能力的入口
     * @param array $data
     * @return mixed
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
     * 处理微信的返回值并返回给客户端
     * @param array $ret
     * @return mixed
     * @author helei
     */
    protected function retData(array $ret)
    {
        $json = json_encode($ret, JSON_UNESCAPED_UNICODE);

        $reqData = [
            'url' => $this->config->getewayUrl,
            'name' => CmbConfig::REQ_FILED_NAME,
            'value' => $json,
        ];
        return $reqData;
    }

    /**
     * 发送完了请求
     * @param string $json
     * @return mixed
     * @throws PayException
     * @author helei
     */
    protected function sendReq($json)
    {
        $responseTxt = $this->curlPost($json, $this->config->getewayUrl);
        if ($responseTxt['error']) {
            throw new PayException('网络发生错误，请稍后再试curl返回码：' . $responseTxt['message']);
        }

        $body = json_decode($responseTxt['body'], true);
        $rspData = $body['rspData'];

        if ($rspData['rspCode'] !== CmbConfig::SUCC_TAG) {
            throw new PayException('招商返回错误提示：' . $rspData['rspMsg']);
        }

        return $rspData;
    }

    /**
     * 父类仅提供基础的post请求，子类可根据需要进行重写
     * @param string $json
     * @param string $url
     * @return array
     * @author helei
     */
    protected function curlPost($json, $url)
    {
        $curl = new Curl();
        return $curl->set([
            'CURLOPT_HEADER'    => 0,
        ])->post($json)->submit($url);
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
            case '0':// 0:已结帐
                return Config::TRADE_STATUS_SUCC;
            case '1':// 1:已撤销
            case '2':// 2:部分结帐
            case '4':// 4:未结帐
            case '7':// 7:冻结交易-冻结金额已经全部结账
            case '8':// 8:冻结交易，冻结金额只结帐了一部分
            default:
                return Config::TRADE_STATUS_FAILD;// 以上状态全部设置为失败
        }
    }
}