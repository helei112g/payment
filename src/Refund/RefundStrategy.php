<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 10:31
 * @description: 退款的业务逻辑
 */

namespace Payment\Refund;


interface RefundStrategy
{
    /**
     * 处理退款的数据
     * @param array $data 退款的数据集
     *  ```php
     *      $data['refund_no'] = '',  退款单号，在系统内部唯一
     *      $data['refund_data'][] => [
     *          'transaction_id'    => '原付款支付宝交易号',
     *          'refund_fee' => '退款总金额', // 单位元
     *          'reason'     => '退款理由', // “退款理由”中不能有“^”、“|”、“$”、“#”
     *      ];// 如果有多笔数据， refund_data 就写入多个数据集
     *  ````
     * @return mixed
     * @author helei
     */
    public function handle(array $data);
}