<?php
/**
 * @author: helei
 * @createTime: 2016-07-27 13:05
 * @description:
 */

namespace Payment\Common\Ali\Data;


use Payment\Common\AliConfig;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;

/**
 * Class TransData
 *
 * @property string $trans_no  转账单号
 * @property string $amount  付款的总金额，最小0.01.单位元
 * @property string $batch_num  本次退款的总笔数
 * @property array $trans_data  付款详细数据
 *  $trans_data[] = [
 *      'serial_no' => '流水号',
 *      'user_account'   => '收款账号',
 *      'user_name'     => '收款人姓名',
 *      'trans_fee'       => '付款金额',
 *      'desc'      => '付款备注说明',
 *  ];
 *
 * @package Payment\Common\Ali\Data
 * anthor helei
 */
class TransData extends AliBaseData
{
    // 付款描述中不能包含以下字符。直接进行过滤
    protected $danger = ['^', '|', '$', '#'];

    // 替换为安全的字符串
    protected $safe = ['', '', '', ''];

    protected function buildData()
    {
        // 设置加密的方式
        $signData = [
            // 基本参数
            'service'   => 'batch_trans_notify',
            'partner'   => trim($this->partner),
            '_input_charset'   => trim($this->inputCharset),
            'sign_type'   => trim($this->signType),
            'notify_url'    => trim($this->notifyUrl),

            // 业务参数
            'account_name' => trim($this->account_name),
            'detail_data'   => $this->trans_data,
            'batch_no'  => trim($this->trans_no),
            'batch_num' => $this->batch_num,
            'batch_fee' => $this->amount,
            'email' => $this->account,
            'pay_date'  => date('Ymd', time()),
        ];

        // 移除数组中的空值
        $this->retData = ArrayUtil::paraFilter($signData);
    }

    /**
     * 检查参数是否合法
     * @author helei
     */
    protected function checkDataParam()
    {
        $transNo = $this->trans_no;// 付款交易号，商户系统内唯一
        $data = $this->trans_data;// 批量转款数据
        $account = $this->account; // 付款账号
        $accountName = $this->account_name;// 付款账号名称

        // 检查付款单号是否设置
        if (empty($transNo) || mb_strlen($transNo) < 11 || mb_strlen($transNo) > 32) {
            throw new PayException('转账单号，不能为空，长度在11~32位之间');
        }

        // 此接口，配置文件必须设置付款账号名与付款账号
        if (empty($account) || empty($accountName)) {
            throw new PayException('该接口必须提供 account 与 account_name 两个配置信息');
        }

        $amount = $batchNum = 0;
        $transData = '';
        // 统计总金额，及付款笔数
        foreach ($data as $key => $item) {
            // 检查备注
            $desc = str_replace($this->danger, $this->safe, $item['desc']);
            if (empty($desc) || mb_strlen($desc) > 50) {
                throw new PayException("trans_data 索引为{$key}的数据，备注说明不能为空，并且不能超过50个字符");
            }

            // 检查流水号
            if (empty($item['serial_no']) || mb_strlen($item['serial_no']) > 22) {
                throw new PayException("trans_data 索引为{$key}的数据，流水号不能为空，并且长度不能超过22个字符");
            }

            // 检查收款方账号
            if (empty($item['user_account']) || mb_strlen($item['user_account']) > 50) {
                throw new PayException("trans_data 索引为{$key}的数据，收款方账号不能为空，并且长度不能超过50个字符");
            }

            // 检查付款金额
            if (bccomp($item['trans_fee'], '0.01', 2) === -1) {
                throw new PayException("trans_data 索引为{$key}的数据，交易金额小于0.01");
            }

            // 检查收款方姓名
            if (empty($item['user_name'])) {
                throw new PayException("trans_data 索引为{$key}的数据，收款方姓名不能为空");
            }

            $batchNum++;// 付款总笔数
            $amount = bcadd($amount, $item['trans_fee'], 2);// 总金额
            $transData .= "{$item['serial_no']}^{$item['user_account']}^{$item['user_name']}^{$item['trans_fee']}^{$desc}|";
        }

        // 移除最后一个 |号
        $transData = trim($transData, '|');

        if (empty($batchNum) || empty($amount) || empty($transData) || $batchNum > 1000) {
            throw new PayException('提供的付款数据异常，可能数据为空，或交易笔数单次大于1000');
        }

        $this->batch_num = $batchNum;
        $this->amount = $amount;
        $this->trans_data = $transData;
    }
}