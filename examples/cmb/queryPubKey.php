<?php

/*
 * The file is part of the payment lib.
 *
 * (c) Leo <dayugog@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Payment\Common\PayException;
use Payment\Config;

date_default_timezone_set('Asia/Shanghai');
$cmbConfig = require_once __DIR__ . '/../cmbconfig.php';

$channel = 'cmb_pub_key';

try {
    $ret = \Payment\Client\Helper::run(Config::CMB_PUB_KEY, $cmbConfig);
} catch (PayException $e) {
    echo $e->errorMessage();
    exit;
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
