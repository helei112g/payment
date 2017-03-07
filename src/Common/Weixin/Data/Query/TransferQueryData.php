<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/7
 * Time: 下午4:55
 */

namespace Payment\Common\Weixin\Data\Query;


use Payment\Common\PayException;
use Payment\Common\Weixin\Data\WxBaseData;
use Payment\Utils\ArrayUtil;

/**
 * 微信转账查询   当前微信仅支持  商户订单号 来进行查询
 *
 * @property string $trans_no  商户转账唯一订单号
 *
 * Class TransferQueryData
 * @package Payment\Common\Weixin\Data\Query
 */
class TransferQueryData extends WxBaseData
{

    protected function buildData()
    {
        $this->retData = [
            'appid' => $this->appId,
            'mch_id'    => $this->mchId,
            'nonce_str' => $this->nonceStr,
            'sign_type' => $this->signType,

            'partner_trade_no'    => $this->trans_no,
        ];

        $this->retData = ArrayUtil::paraFilter($this->retData);
    }

    protected function checkDataParam()
    {
        $transNo = $this->trans_no;
        if (empty($transNo)) {
            throw new PayException('请提供商户调用企业付款API时使用的商户订单号');
        }
    }
}