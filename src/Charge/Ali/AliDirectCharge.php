<?php
/**
 * Created by ChaXian.
 * User: Bing
 * Date: 2018/5/24
 * Time: 11:32
 */

namespace Payment\Charge\Ali;


use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\Charge\DirectChargeData;
use Payment\Common\AliDirectConfig;
use Payment\Common\BaseStrategy;
use Payment\Utils\ArrayUtil;

class AliDirectCharge extends AliBaseStrategy
{

    public function __construct(array $config)
    {
        try {
            $this->config = new AliDirectConfig($config);
        } catch (PayException $e) {
            throw $e;
        }
    }

    public function getBuildDataClass()
    {
        return DirectChargeData::class;
    }

    public function retData(array $data)
    {
        $signType = $data['sign_type'];
        $sign = $data['sign'];

        $data = ArrayUtil::removeKeys($data, ['sign_type', 'sign']);

        $data = ArrayUtil::arraySort($data);

        $data['sign'] = $sign;
        $data['sign_type'] = $signType;

        return $this->config->getewayUrl . '?' . http_build_query($data);
    }

}