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
 * @property string $trans_no  商户转账唯一订单号
 * @property string $openid 商户appid下，某用户的openid
 * @property string $check_name
 *  - NO_CHECK：不校验真实姓名
 *  - FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账）
 *  - OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
 *
 * @property string $payer_real_name  收款用户真实姓名。
 * @property string $amount  转账金额，单位：元。 只支持2位小数，小数点前最大支持13位，金额必须大于0。
 * @property string $desc 企业付款操作说明信息。必填。
 * @property string $client_ip 调用接口的机器Ip地址
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
            'device_info' => $this->terminal_id,
            'nonce_str' => $this->nonceStr,
            'partner_trade_no'    => $this->trans_no,
            'openid'    => $this->openid,

            'check_name'    => $this->check_name,
            're_user_name'  => $this->payer_real_name,
            'amount'    => $this->amount,// 此处需要处理单位为分
            'desc'  => $this->desc,

            // $_SERVER["REMOTE_ADDR"]  获取客户端接口。此处获取php所在机器的ip  如果无法获取，则使用该ip
            'spbill_create_ip'  => $this->client_ip,
        ];

        $this->retData = ArrayUtil::paraFilter($this->retData);
    }

    /**
     * 检查相关参数是否设置
     * @author helei
     */
    protected function checkDataParam()
    {
        $openId = $this->openid;
        $transNo = $this->trans_no;
        $checkName = $this->check_name;
        $realName = $this->payer_real_name;
        $amount = $this->amount;
        $clientIp = $this->client_ip;

        if (empty($openId)) {
            throw new PayException('商户appid下，某用户的openid 必须传入');
        }

        if (empty($transNo)) {
            throw new PayException('商户订单号，需保持唯一性');
        }

        if ($checkName !== 'NO_CHECK' && empty($realName)) {
            throw new PayException('请传入收款人真实姓名');
        }

        // 微信使用的单位位分.此处进行转化
        $this->amount = bcmul($amount, 100, 0);
        if (empty($amount) || $amount < 0) {
            throw new PayException('转账金额错误');
        }

        if (empty($clientIp)) {
            $this->client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        }
    }
}
