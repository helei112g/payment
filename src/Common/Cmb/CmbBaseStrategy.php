<?php

namespace Payment\Common\Cmb;

use GuzzleHttp\Client;
use Payment\Common\BaseData;
use Payment\Common\BaseStrategy;
use Payment\Common\CmbConfig;
use Payment\Common\PayException;
use Payment\Config;

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
        $client = new Client([
            'timeout' => '10.0'
        ]);
        // @note: 微信部分接口并不需要证书支持。这里为了统一，全部携带证书进行请求
        $options = [
            'body' => $json,
            'http_errors' => false
        ];
        $response = $client->request('POST', $this->config->getewayUrl, $options);
        if ($response->getStatusCode() != '200') {
            throw new PayException('网络发生错误，请稍后再试curl返回码：' . $response->getReasonPhrase());
        }

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        // TODO 检查返回的数据是否被篡改
        $flag = $this->verifySign($data);
        if (!$flag) {
            throw new PayException('微信返回数据被篡改。请检查网络是否安全！');
        }

        $rspData = $data['rspData'];
        if ($rspData['rspCode'] !== CmbConfig::SUCC_TAG) {
            throw new PayException('招商返回错误提示：' . $rspData['rspMsg']);
        }

        return $rspData;
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

    /**
     * 检查返回的数据是否正确
     * @param array $retData
     * @return bool
     */
    protected function verifySign(array $retData)
    {
        // todo
        return true;
    }
}
