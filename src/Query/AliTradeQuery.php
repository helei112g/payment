<?php
/**
 * @author: helei
 * @createTime: 2016-07-28 17:23
 * @description: 支付宝订单查询接口
 */

namespace Payment\Query;


use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\BaseData;
use Payment\Common\Ali\Data\TradeQueryData;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;
use Payment\Utils\Curl;
use Payment\Utils\DataParser;

class AliTradeQuery extends AliBaseStrategy
{

    protected function getBuildDataClass()
    {
        return TradeQueryData::class;
    }

    /**
     * 查询后返回的数据是xml，将查询操作放入内部，并将结果处理为数组后返回
     * @param array $data
     * @return mixed|string
     * @author helei
     * @throws \Payment\Common\PayException
     */
    public function handle(array $data)
    {
        $url = parent::handle($data);

        // 发起网络请求
        $curl = new Curl();
        $responseTxt = $curl->set([
            'CURLOPT_SSL_VERIFYPEER'    => true,
            'CURLOPT_SSL_VERIFYHOST'    => 2,
            'CURLOPT_CAINFO'    => $this->config->cacertPath,
            'CURLOPT_HEADER'    => 0,// 为了便于解析，将头信息过滤掉
        ])->get($url);

        if ($responseTxt['error']) {
            throw new PayException('网络发生错误，请稍后再试');
        }

        // 格式化为数组
        $retData = DataParser::toArray($responseTxt['body']);

        // 移除不必要参数
        $retData = ArrayUtil::removeKeys($retData, ['sign', 'sign_type', 'request']);

        return $retData;
    }
}