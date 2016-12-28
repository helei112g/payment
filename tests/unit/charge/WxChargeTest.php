<?php
namespace charge;


use Codeception\Specify;
use Codeception\Util\Stub;
use Payment\Charge\Weixin\WxAppCharge;
use Payment\Charge\Weixin\WxPubCharge;
use Payment\Charge\Weixin\WxQrCharge;
use Payment\Common\PayException;
use Payment\Utils\StrUtil;

class WxChargeTest extends \Codeception\Test\Unit
{
    use Specify;
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var array
     */
    protected $wxConfig = [];

    protected function _before()
    {
        // 微信必须设置时区，否则出错
        date_default_timezone_set('Asia/Shanghai');
        // 微信配置文件
        $this->wxConfig = require __DIR__ . '/../../../examples/wxconfig.php';
    }

    protected function _after()
    {
    }

    /**
     * 此处可能会由于网络原因，导致curl出错
     */
    public function testAppCharge()
    {
        $appCharge = new WxAppCharge($this->wxConfig);

        $payData = [
            "order_no"	=> StrUtil::getNonceStr(32),
            "amount"	=> '0.01',// 单位为元 ,最小为0.01
            "client_ip"	=> '127.0.0.1',
            "subject"	=> '测试支付',
            "body"	=> '支付接口测试',
            "extra_param"	=> '',
        ];

        $this->specify('正确数据发起 微信APP 支付请求', function () use ($appCharge, $payData) {
            $ret = $appCharge->handle($payData);

            $this->assertArrayHasKey('appid', $ret);
            $this->assertArrayHasKey('partnerid', $ret);
            $this->assertArrayHasKey('prepayid', $ret);
            $this->assertArrayHasKey('package', $ret);
            $this->assertArrayHasKey('noncestr', $ret);
            $this->assertArrayHasKey('timestamp', $ret);
            $this->assertArrayHasKey('sign', $ret);
        });

        $this->specify('错误数据， 微信APP 支付抛出 PayException异常', function () use ($appCharge, $payData) {
            $payData['client_ip'] = '';

            $this->expectException(PayException::class);
            $appCharge->handle($payData);
        });
    }

    public function testQRCharge()
    {
        $appCharge = new WxQrCharge($this->wxConfig);

        $payData = [
            "order_no"	=> StrUtil::getNonceStr(32),
            'product_id'    => StrUtil::getNonceStr(6),// 扫码支付，必须设置
            "amount"	=> '0.01',// 单位为元 ,最小为0.01
            "client_ip"	=> '127.0.0.1',
            "subject"	=> '测试支付',
            "body"	=> '支付接口测试',
            "extra_param"	=> '',
        ];

        $this->specify('正确数据发起 微信扫码支付 支付请求', function () use ($appCharge, $payData) {
            $ret = $appCharge->handle($payData);

            $this->assertStringStartsWith('weixin://wxpay', $ret);
        });

        $this->specify('错误数据， 微信扫码支付 支付抛出 PayException异常', function () use ($appCharge, $payData) {
            $payData['client_ip'] = '';

            $this->expectException(PayException::class);
            $appCharge->handle($payData);
        });
    }

    // 使用 stub  方式进行测试
    public function testPubCharge()
    {
        $json = '{"appId":"wxaa11f6c3a3e13aac","package":"prepay_id=123123123","nonceStr":"dsfads5353fdF","timeStamp":"1479267873","signType":"MD5","paySign":"asdfsdfayegd4534gdfgdf"}';
        //$this->markTestIncomplete('该接口需要额外权限，由于测试账号无权限，暂时不实现本测试结果');
        $pubCharge = Stub::make(WxPubCharge::class, ['handle'  => $json]);

        $this->specify('使用 stub  的方式测试公众号接口', function () use ($pubCharge, $json) {

            $this->assertEquals($json, $pubCharge->handle([]));
        });
    }
}