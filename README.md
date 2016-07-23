# 重大更新

payment v1.x系列目前仅修复重要bug问题,不再增加新的功能.现阶段主要开发与维护 v2.x.

具体代码,请查看分支 paymentv2

paymentv2的[相关文档](https://helei112g.github.io/2016/07/18/%E6%94%AF%E4%BB%98%E5%AE%9D%E3%80%81%E5%BE%AE%E4%BF%A1%E6%94%AF%E4%BB%98%E6%8E%A5%E5%85%A5%E9%9B%86%E6%88%90/)以放在博客中维护,方便更新修改.

# 更新说明
* 201-6-22 增加支付退款接口 调用方法，请看： `examples/refund.php`
* 201-6-21 增加微信网站扫码支付接口 调用方法，请看： `examples/wxcharge.php`

# 项目介绍
1. 集成了支付宝的即时到帐、移动支付、订单查询接口
2. 集成了微信的APP支付、订单查询接口
3. 运行时，需要 `php 5.5` 以上的版本。目前5.4的php版本也可运行。但后期可能会放弃该版本。
4. 项目中所有的金额传输单位全部为元。lib会自动在向微信支付时，处理为分。支付宝支付时保持不变。
5. 强烈建议，请查看 `examples` 文件中的示例代码。以下文档内容仅供参考，后期可能不能及时更新。

# 安装

建议安装方式：

```php
    composer require riverslei/payment
```

一般安装方式：

直接下载项目，然后通过 `payment/autoload.php` 来引入项目。

对于还在使用不支持命名空间的同学，你们可以自己改造。不打算发布一个不支持命名空间的项目

# 调用方式
    本来说想周末再补充文档。不过昨天发的项目，今天一看都差不多40个start了。我想不能等了。得早点把文档给出来。
    
## 支付宝网站支付
    首先使用支付宝之前，需要在做以下几个配置

1. 修改 `src/Alipay/AlipayConfig.php` 这个配置文件。其中涉及到的信息在登陆商户支付版后，均可找到。需要注意的是
rsa秘钥的生成与上传。切记上传时一定检查是否有空格，有空格肯定报错，报错别问我。
2. 生成的rsa秘钥，一定要放在 `src/Alipay/safekey` 这个文件夹下面。一把来说我不要求你的文件命令，但是别用中文命令，最好直接默认使用生成出来的命令。

现在来说说调用的事。
首选使用支付前，需要获得一个支付对象。由于支付对象有多个（微信APP、支付宝网站、支付宝移动支付等），所以这里通过依赖接口，静态工厂方法，来方便大家使用。

下面示例代码是：支付宝网站支付的演示代码。这里如果不够清晰，可以看 `examples` 文件夹中的示例代码。
```php
    $alipayDirect = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_ALIPAY_DIRECT);
    
    $payData = [
        "order_no"	=> 'F616699445072025',// 必须， 商户订单号，适配每个渠道对此参数的要求，必须在商户系统内唯一
        "amount"	=> '0.01',// 必须， 订单总金额, 人民币为元
        "client_ip"	=> '127.0.0.1',//  可选， 发起支付请求客户端的 IP 地址，格式为 IPV4
        "subject"	=> 'Older Driver',// 必须， 商品的标题，该参数最长为 32 个 Unicode 字符
        "body"	=> '购买Older Driver',// 必须， 商品的描述信息
        "success_url"	=> 'http://mall.devtiyushe.com/order/default/ali-pay-notify.html',// 必须， 支付成功的回调地址  统一使用异步通知  该url后，不能带任何参数。
        "return_url"	=> 'http://mall.devtiyushe.com/order/default/pay-return-url.html',
        "time_expire"	=> '14',// 可选， 订单失效时间，单位是 分钟
        "description"	=> '',//  可选，如果用户请求时传递了该参数，则返回给商户时会回传该参数
    ];
    
    try {
        $url = $alipayDirect->charges($payData);
    } catch (PayException $e) {
        echo $e->errorMessage();
    }
```

## 支付宝移动支付
    移动支付的配置与上方相同。所有的支付宝支付，配置都只需要设置以上配置即可。以后对于配置就不在单独描述了。
    
直接上代码，说怎么调用

```php
    $alipayMobile = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_ALIPAY);
    
    $payData = [
        "order_no"	=> 'F616699445072025',// 必须， 商户订单号，适配每个渠道对此参数的要求，必须在商户系统内唯一
        "amount"	=> '0.01',// 必须， 订单总金额, 人民币为元
        "client_ip"	=> '127.0.0.1',//  可选， 发起支付请求客户端的 IP 地址，格式为 IPV4
        "subject"	=> 'Older Driver',// 必须， 商品的标题，该参数最长为 32 个 Unicode 字符
        "body"	=> '购买Older Driver',// 必须， 商品的描述信息
        "success_url"	=> 'http://mall.devtiyushe.com/order/default/ali-pay-notify.html',// 必须， 支付成功的回调地址  统一使用异步通知  该url后，不能带任何参数。
        "time_expire"	=> '14',// 可选， 订单失效时间，单位是 分钟
        "description"	=> '',//  可选，如果用户请求时传递了该参数，则返回给商户时会回传该参数
    ];
    
     try {
         $reqArr = $alipayMobile->charges($data);// 调用该函数，会抛出  PayException 异常
         var_dump($reqArr);
     } catch (PayException $e) {
         echo $e->errorMessage();
     }
```

## 微信APP支付
    当前微信支付仅支持APP支付。后期会陆续更新公众号支付。H5支付等功能。
    
进行微信支付的使用，首要的还是先进行配置。结构与支付宝基本差不多。
1. 打开 `src/Wxpay/WxConfig.php` 文件，可以根据自己微信的商户平台，找到对应的信息。
2. 把微信平台上相关的key下载后放入 `src/Wxpay/safekey` 文件夹中。当然这一步也可不做，当前微信没有几个接口在用他们提供的安全文件

通过两步配置完成，可以开始使用了。
```php
    $appCharge = ChargeFactory::getInstance(ChargeChannel::CHANNEL_IS_WX);
    
    $payData = [
        "order_no"	=> 'F2016dd6dd1a23',// 必须， 商户订单号，适配每个渠道对此参数的要求，必须在商户系统内唯一
        "amount"	=> '1',// 订单总金额, 人民币为元
        "subject"	=> '测试即时到帐接口',// 必须， 商品的标题，该参数最长为 32 个 Unicode 字符
        "body"	=> '即时到帐接口，就是爱支付',// 必须， 商品的描述信息
        "client_ip"	=> '127.0.0.1',//  可选， 发起支付请求客户端的 IP 地址，格式为 IPV4
        "success_url"	=> 'http://mall.tys.tiyushe.net/pay-test/notify.html',// 必须， 支付成功的回调地址  统一使用异步通知  该url后，不能带任何参数。
        "time_expire"	=> '15',// 可选， 订单失效时间，单位是 分钟
        "description"	=> '这是附带的业务数据',//  可选，如果用户请求时传递了该参数，则返回给商户时会回传该参数
    ];
    
    try {
        $reqArr = $appCharge->charges($data);// 调用该函数，会抛出  PayException 异常
        var_dump($reqArr);
    } catch (PayException $e) {
        echo $e->errorMessage();
    }
```


## 订单查询
    详细代码还是可以看 `examples/query.php` 文件哈
    
```php
    // 支付宝的回调
    $payway = ChargeChannel::CHANNEL_IS_ALIPAY;
    
    // 微信的回调
    //$payway = ChargeChannel::CHANNEL_IS_WX;
    
    $value = '1007570439201601142692427764';// 第三方交易号
    $key = 'trade_no';// 可取值：out_trade_no：商户网站唯一订单号   trade_no： 第三方交易号
    
    $api = TradeFactory::getInstance($payway);
    
    try {
        $data = $api->tradeQuery($value, $key);
    
        /**
         * 'subject' => '美团美食购买'// 商品标题
         * 'body' => '购买蓉和小厨美食'// 商品描述
         * 'amount' => '133400'// 支付的总金额，单位为分
         * 'channel' => 'ali'// 支付通道 .此处可能值仅为： ali  wx
         * 'order_no' => '2016060504005139'// 商户唯一订单号
         * 'buyer_id' => '2088122159801601'// 购买者识别码。支付宝为：购买者邮箱，或者手机号码。weiixn为唯一识别码
         * 'trade_state' => 'SUCCESS'// 交易状态。SUCCESS—支付成功     REFUND—转入退款    NOTPAY—未支付
         * 'transaction_id' => '2016060521001004600254528027'// 第三方的流水号
         * 'time_end' => 2016-06-05 16:01:00'// 交易完成时间
         */
        var_dump($data);
    } catch (PayException $e) {
        echo $e->errorMessage();
    }
```

## 回调通知
    这里说的通知，主要是说异步通知，当然同步通知处理方式一样。你唯一需要关注的是，解决好你自己的业务，不要一定订单被更新多次就糟糕了。

这里回调通知，

```php
    /**
     * 首先该lib已经处理了相关的验证，并且可以进行自动的回调。
     * 现在我假设你自己的业务处理类叫做：NotifyOrder
     */
    
    class NotifyOrder implements PayNotifyInterface
    {
    
        /**
         * 必须要实现的方法。我会对该商户实现的方法进行回调
         * @param array $data
         * @return bool
         * @author helei
         */
        public function notifyProcess($data)
        {
            // 1. 检查订单是否已经被更新过了。
            // 2. 检查金额是否正确
            // 3. 其他逻辑
            // 4. 进行更新订单状态
            return true;
        }
    }
    
    // 支付宝的回调
    $payway = ChargeChannel::CHANNEL_IS_ALIPAY;
    
    // 微信的回调
    //$payway = ChargeChannel::CHANNEL_IS_WX;
    
    $api = TradeFactory::getInstance($payway);
    
    $notify = new NotifyOrder();
    
    try {
        $ret = $api->notify($notify);
    
        echo $ret;exit;// 执行成功时，需要输出结果
    } catch (PayException $e) {
        echo $e->errorMessage();
    }
```
 
# 说明

后续接口，将会一点点增加。写这个的初衷也是，自己基本每份工作都要写支付。每次都是重新开始。麻烦、累
干脆整理一下，放出来，大家共用、共享。同时也希望有好的建议、改进方法的可以向我说明。我们一起完善维护。让工作更轻松。

* 邮箱：dayugog@gmail.com
* 博客：http://blog.csdn.net/hel12he
* 微信：helei543345

另外本项目，首发在github。主要原因是：github上提交composer包非常方便。但是每次更新后，会自动提交到oschina的。

github地址： https://github.com/helei112g/payment

当然如果你愿意打赏我，我也是愿意接受的。