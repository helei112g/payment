<?php
namespace charge;


use Codeception\Specify;
use Payment\Charge\Ali\AliAppCharge;
use Payment\Charge\Ali\AliWapCharge;
use Payment\Charge\Ali\AliWebCharge;
use Payment\Common\PayException;
use Payment\Utils\StrUtil;

class AliChargeTest extends \Codeception\Test\Unit
{
    use Specify;
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var array
     */
    protected $aliConfig = [];


    protected function _before()
    {
        // 支付宝配置文件
        $this->aliConfig = require __DIR__ . '/../../../examples/aliconfig.php';
    }

    protected function _after()
    {
    }

    // tests
    public function testWapCharge()
    {
        $payData = [
            "order_no"	=> StrUtil::getNonceStr(32),
            "amount"	=> '1',// 单位为元 ,最小为0.01
            "client_ip"	=> '127.0.0.1',
            "subject"	=> '测试支付宝wap支付',
            "body"	=> '测试支付宝wap支付',
            "show_url"  => 'http://mall.xxxx.com/goods/23.html',// 支付宝手机网站支付接口 该参数必须上传 。其他接口忽略
            "extra_param"	=> '',
        ];
        $wapCharge = new AliWapCharge($this->aliConfig);
        $this->specify('输入正确数据，返回一个调用支付宝的url', function () use ($payData, $wapCharge) {
            $ret = $wapCharge->handle($payData);

            $this->assertStringStartsWith('https://mapi.alipay.com/gateway.do?service=alipay.wap.create.direct.pay.by.user&', $ret, '以支付宝网关开始');
        });

        // 如果是检测异常，需要将该方法放置在最后
        $this->specify('输入不正确的支付数据，抛出 PayException 异常', function () use ($payData, $wapCharge) {
            $payData['amount'] = '0.0000001';

            $this->expectException(PayException::class);// 异常的断言，需要写在发出请求前
            $ret = $wapCharge->handle($payData);
        });
    }

    /**
     * 测试返回的数据类型
     */
    public function testAppCharge()
    {
        $payData = [
            "order_no"	=> StrUtil::getNonceStr(32),
            "amount"	=> '0.01',// 单位为元 ,最小为0.01
            "client_ip"	=> '127.0.0.1',
            "subject"	=> '测试支付宝app支付',
            "body"	=> '测试支付宝app支付',
            "show_url"  => 'http://mall.xxxx.com/goods/23.html',// 支付宝手机网站支付接口 该参数必须上传 。其他接口忽略
            "extra_param"	=> '',
        ];

        $appCharge = new AliAppCharge($this->aliConfig);
        $this->specify('测试 支付宝APP支付 返回的数据构造类名', function () use ($payData, $appCharge) {
            $ret = $appCharge->handle($payData);

            //codecept_debug($ret);
            $this->assertStringEndsWith('&subject="测试支付宝app支付"&total_fee="0.01"', $ret, '结尾数据必定是金额在最后');
        });

        $this->specify('给出不正确的支付请求数据，抛出 PayException 异常', function () use ($payData, $appCharge) {
            $payData['amount'] = '0.000001';

            $this->expectException(PayException::class);// 异常的断言，需要写在发出请求前
            $ret = $appCharge->handle($payData);
        });
    }

    // tests
    public function testWebCharge()
    {
        $payData = [
            "order_no"	=> StrUtil::getNonceStr(32),
            "amount"	=> '0.01',// 单位为元 ,最小为0.01
            "client_ip"	=> '127.0.0.1',
            "subject"	=> '测试支付宝app支付',
            "body"	=> '测试支付宝app支付',
            "show_url"  => 'http://mall.xxxxx.com/goods/23.html',// 支付宝手机网站支付接口 该参数必须上传 。其他接口忽略
            "extra_param"	=> '',
        ];

        $webCharge = new AliWebCharge($this->aliConfig);
        $this->specify('测试 支付宝web支付 返回的数据构造类名', function () use ($webCharge, $payData) {
            $ret = $webCharge->handle($payData);

            $this->assertStringStartsWith('https://mapi.alipay.com/gateway.do?service=create_direct_pay_by_user&', $ret, '结尾数据必定是金额在最后');
        });

        $this->specify('给出不正确的支付请求数据，抛出 PayException 异常', function () use ($webCharge, $payData) {
            $payData['amount'] = '0.000001';

            $this->expectException(PayException::class);// 异常的断言，需要写在发出请求前
            $ret = $webCharge->handle($payData);
        });
    }
}