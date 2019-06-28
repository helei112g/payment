<?php
/**
 * Created by PhpStorm.
 * User: biker
 * Date: 2019/6/28
 * Time: 17:18
 */

namespace Payment\Close;

use Payment\Common\Ali\AliBaseStrategy;
use Payment\Common\Ali\Data\CloseData;
use Payment\Common\PayException;
use Payment\Config;

class AliClose extends AliBaseStrategy{

    /**
     * 交易关闭接口
     * @var string
     */
    protected $method = 'alipay.trade.close';


    public function getBuildDataClass(){
        $this->config->method = $this->method;
        return CloseData::class;
    }


    /**
     * 处理返回
     * BY biker
     * @param array $data
     * @return array|mixed|string
     * @throws PayException
     */
    protected function retData(array $data){
        $reqData = parent::retData($data);
        try {
            $ret = $this->sendReq($reqData);
        } catch (PayException $e) {
            throw $e;
        }
        if ($this->config->returnRaw) {
            $ret['channel'] = Config::ALI_CLOSE;
            return $ret;
        }

        if ($ret['code'] !== '10000') {
            return [
                'is_success'    => 'F',
                'error' => $ret['sub_msg'],
            ];
        }
        $retData = [
            'is_success'    => 'T',
            'response'  => [
                'transaction_id'   => $ret['trade_no'],
                'order_no'  => $ret['out_trade_no'],
                'channel'   => Config::ALI_CLOSE,
            ],
        ];
        return $retData;
    }
}