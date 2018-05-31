<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/7
 * Time: 下午1:32
 */

namespace Payment\Common\Weixin\Data\Query;

use Payment\Common\PayException;
use Payment\Common\Weixin\Data\WxBaseData;
use Payment\Utils\ArrayUtil;

/**
 * 微信红包接口查询
 *
 * @property string $out_trade_no 商户系统内部的订单号
 * @property string $bill_type  通过商户订单号获取红包信息
 *
 * Class RedQueryData
 * @package Payment\Common\Weixin\Data\Query
 * anthor IT
 */
class RedQueryData extends WxBaseData
{
    protected function buildData()
    {
        if($this->submch){
            //开启服务商
            $appid = $this->sub_appid;
            $mchid = $this->sub_mch_id;
        }else{
            $appid = $this->appId;
            $mchid = $this->mchId;
        }
        $appid = $this->appId;
        $mchid = $this->mchId;
        $this->retData = [
            'appid'     => $appid,
            'mch_id'    => $mchid,
            'nonce_str' => $this->nonceStr,
            //'sign_type' => $this->signType,
            'mch_billno'=> $this->mch_billno,
            'bill_type' => $this->bill_type,
        ];

        $this->retData = ArrayUtil::paraFilter($this->retData);
    }

    protected function checkDataParam()
    {
        $orderNo = $this->mch_billno;// 商户订单号，查询效率低，不建议使用
        $billType = $this->bill_type;// 商户的退款单号

        // 必须条件校验
        if (empty($orderNo) && empty($billType)) {
            throw new PayException('查询红包记录  必须提供商户订单号、订单类型');
        }
    }
}
