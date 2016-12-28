<?php
/**
 * @author: helei
 * @createTime: 2016-08-04 09:42
 * @description:
 */

namespace Payment\Common\Weixin\Data;
use Payment\Common\PayException;
use Payment\Utils\ArrayUtil;


/**
 * Class TransferData
 *
 * 微信当前也仅支持单笔付款，不支持批量
 *
 * @property string $trans_no  转账单号
 * @property string $trans_fee  等于   $trans_data['trans_fee'] 的值
 * @property string $openid 等于   $trans_data['user_account'] 的值
 * @property string $user_name 等于 $trans_data['user_name'] 的值
 * @property string $desc 等于 $trans_data['desc'] 的值
 * @property array $trans_data  付款详细数据
 *  $trans_data[] = [
 *      'user_account'   => '收款账号',
 *      'user_name'     => '收款人姓名',
 *      'trans_fee'       => '付款金额',  // 传入时单位为元
 *      'desc'      => '付款备注说明',
 *  ];
 *
 * @package Payment\Common\Weixin\Data
 * anthor helei
 */
class TransferData extends WxBaseData
{

    protected function buildData()
    {
        $this->retData = [
            'mch_appid' => $this->appId,
            'mchid'    => $this->mchId,
            'nonce_str' => $this->nonceStr,
            'partner_trade_no'    => $this->trans_no,
            'openid'    => $this->openid,

            // NO_CHECK：不校验真实姓名
            // FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账)
            // OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
            'check_name'    => 'FORCE_CHECK',
            're_user_name'  => $this->user_name,
            'amount'    => $this->trans_fee,// 此处需要处理单位为分
            'desc'  => $this->desc,

            // $_SERVER["REMOTE_ADDR"]  获取客户端接口。此处获取php所在机器的ip  如果无法获取，则使用该ip
            'spbill_create_ip'  => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',
        ];

        $this->retData = ArrayUtil::paraFilter($this->retData);
    }

    /**
     * 检查相关参数是否设置
     * @author helei
     */
    protected function checkDataParam()
    {
        $data = $this->trans_data;
        $transNo = $this->trans_no;// 付款交易号，商户系统内唯一

        // 当前微信转款仅支持单笔
        if (sizeof($data) != 1) {
            throw new PayException('当前版本，不支持微信批量退款。目前仅支持1笔');
        }

        // 检查付款单号是否设置
        if (empty($transNo) || mb_strlen($transNo) < 11 || mb_strlen($transNo) > 32) {
            throw new PayException('转账单号，不能为空，长度在11~32位之间');
        }

        foreach ($data as $key => $item) {

            if (empty($item['user_account'])) {
                throw new PayException('该值必须设置，为关注者的openid');
            }
            $this->openid = $item['user_account'];

            // 检查付款金额  微信转款，最小金额为1元
            if (bccomp($item['trans_fee'], '1', 2) === -1) {
                throw new PayException("交易金额不能小于 1 元");
            }
            $this->trans_fee = bcmul($item['trans_fee'], 100, 0);// 微信以分为单位

            // 检查收款方姓名
            if (empty($item['user_name'])) {
                throw new PayException("收款方姓名不能为空，且需要真实姓名");
            }
            $this->user_name = trim($item['user_name']);

            if (empty($item['desc']) || mb_strlen($item['desc']) > 50) {
                throw new PayException("备注说明不能为空，并且不能超过50个字符");
            }
            $this->desc = $item['desc'];
        }
    }
}