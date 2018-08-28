<?php
/**
 * Created by PhpStorm.
 * User: exwe
 * Date: 18-8-22
 * Time: pm 4:09
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'Api.php';
$api = new Api\Api();

//行情类接口测试
$markets = $api->getMarkets();
var_dump($markets);


//交易类接口测试
$balances = $api->getBalances();
var_dump($balances);