<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/testNotify.php';

date_default_timezone_set('Asia/Shanghai');

$aliConfig = require_once __DIR__ . '/aliconfig.php';
$wxConfig  = require_once __DIR__ . '/wxconfig.php';
$cmbConfig = require_once __DIR__ . '/cmbconfig.php';

$from = $_GET['from'] ? $_GET['from'] : 'ali';

if ($from === 'ali') {
    $config = $aliConfig;
    //$tmp = 'charset=utf-8&out_trade_no=15806490592761&method=alipay.trade.page.pay.return&total_amount=0.01&sign=dp8VcyW941FYJh4znvipilmROaH%2BHi5cQ06al1Rfl8Lq7l4QZ%2FIGa6ZsQokVfDVU9hT9vK30Zzoau9UOvT2ueZ5Kbkju8GXGfclDWHbzLgxFd2C9datK%2Bavvek9Zriops3z2I%2FRt%2BXqcYqxjPzA3QQpSnXDj037kymMA%2FdMXTPD26z3JzwLnTk32SfCiSHllGHMkik9Fexh1%2Fqqht%2BMezDyArvHhM08k%2FnMxYyGGRXUtE027osMUfKXBU89k9vHUr6is8DJH3Gkdtno3nI2MORVi%2BgkL0%2Bq9NPz3MYQ01nHRbmmBGScRkv5SNVomVYKVTtfuT7SQaLLm67MTzan4BA%3D%3D&trade_no=2020020222001440351000252550&auth_app_id=2016073100130857&version=1.0&app_id=2016073100130857&sign_type=RSA2&seller_id=2088102169252684&timestamp=2020-02-02+21%3A11%3A34';

    $tmp = 'body=test+body&subject=test+subject&sign_type=RSA2&buyer_logon_id=aaq***%40sandbox.com&auth_app_id=2016073100130857&notify_type=trade_status_sync&out_trade_no=14893825198432&point_amount=0.00&version=1.0&fund_bill_list=%5B%7B%22amount%22%3A%220.01%22%2C%22fundChannel%22%3A%22ALIPAYACCOUNT%22%7D%5D&passback_params=123&buyer_id=2088102169940354&total_amount=0.01&trade_no=2017031321001004350200145287&notify_time=2017-03-13+13%3A23%3A05&charset=UTF-8&invoice_amount=0.01&gmt_payment=2017-03-13+13%3A23%3A04&trade_status=TRADE_SUCCESS&sign=SrfDm1whLHx8PeFcPbAEn7S43%2BOTMy5ZnTxv42jpCeRXz8poKS0n542Nf4eAq7%2BJfta1vMqybMFf9C4Cl%2B3WEPFbndU2WGpboyU2CPUcSoYaBE68H1%2FImNUomEi3vMjJe3H4s%2Fz%2BLOnVcH8luO0bbSB79kKupec0fdm9V9Wg2axaZD9UkRLwBvoXsDx9tFOAwhqHyY1ZPq%2F1SQj5cwhQ2luKhJaqjO4L4Z819b%2BvHZfuaKX3xt5pgCQXiSVLo%2BfA%2FY0RmDfNngZML8UndYyXpXmgTMH2grR7D65ODPlatDt3JsNe9U2Kj%2F7uVXdPR2Tey3ikL4W4Pn4%2FULq8ow3YHw%3D%3D&gmt_create=2017-03-13+13%3A23%3A03&buyer_pay_amount=0.01&receipt_amount=0.01&seller_id=2088102169252684&app_id=2016073100130857&seller_email=naacvg9185%40sandbox.com&notify_id=27d63b0f7da1e21d932b6ec9176a052ipa';


    parse_str($tmp, $data);
    //$_GET = $data;
    $_POST = $data;
    $proxy = \Payment\Client::ALIPAY;
} elseif ($from === 'wx') {
    $config = $wxConfig;
    $proxy  = \Payment\Client::WECHAT;
} else {
    $config = $cmbConfig;
    $proxy  = \Payment\Client::CMB;
}

$callback = new TestNotify();

try {
    $client = new \Payment\Client($proxy, $config);
    $xml    = $client->notify($callback);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\GatewayException $e) {
    echo $e->getMessage();
    exit;
} catch (\Payment\Exceptions\ClassNotFoundException $e) {
    echo $e->getMessage();
    exit;
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

var_dump($xml);
