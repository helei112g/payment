<?php
/**
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/3/7
 * Time: 下午3:59
 */

namespace Payment\Common\Ali\Data\Query;


use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

/**
 * 构建查询转账情况的数据
 *
 * @property string $trans_no  商户转账唯一订单号
 * @property string $transaction_id   支付宝转账单据号：和商户转账唯一订单号不能同时为空
 *
 * Class TransferQueryData
 * @package Payment\Common\Ali\Data\Query
 */
class TransferQueryData extends QueryBaseData
{

    protected function getBizContent()
    {
        $content = [
            'out_biz_no'    => $this->trans_no,
            'order_id'        => $this->transaction_id,
        ];

        $content = ArrayUtil::paraFilter($content);// 过滤掉空值，下面不用在检查是否为空
        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }

    protected function checkDataParam()
    {
        $transNo = $this->trans_no;
        $transactionId = $this->transaction_id;

        // 二者不能同时为空
        if (empty($transactionId) && empty($transNo)) {
            throw new PayException('必须提供支付宝转账单据号或者商户转账单号');
        }
    }
}