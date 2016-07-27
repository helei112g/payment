<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 13:07
 * @description:
 */

namespace Payment\Trans;


interface TransferStrategy
{
    /**
     * 处理转款业务
     * @param array $data
     *
     * $data['trans_no']    = '';// 转款单号
     * $data['trans_data'][] = [
     *      'serial_no' => '流水号',
     *      'user_account'   => '收款账号',
     *      'user_name'     => '收款人姓名',
     *      'trans_fee'       => '付款金额',
     *      'desc'      => '付款备注说明',
     *  ];
     *
     * @return mixed
     * @author helei
     */
    public function handle(array $data);
}