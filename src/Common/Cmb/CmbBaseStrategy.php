<?php

namespace Payment\Common\Cmb;

use Payment\Common\BaseData;
use Payment\Common\BaseStrategy;
use Payment\Common\CmbConfig;
use Payment\Common\PayException;
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
        return $ret;
    }

    /**
     * 父类仅提供基础的post请求，子类可根据需要进行重写
     * @param string $xml
     * @param string $url
     * @return array
     * @author helei
     */
    protected function curlPost($xml, $url)
    {
        $curl = new Curl();
        return $curl->set([
            'CURLOPT_HEADER'    => 0
        ])->post($xml)->submit($url);
    }


}