<?php
/**
 * Created by PhpStorm.
 * User: exwe
 * Date: 18-8-22
 * Time: pm 4:09
 */

namespace Api;

class Api
{
    //交易API前缀
    public $tradePath = 'https://core.exwe.com/api/';
    public $key = "Your API_KEY";
    public $secret = "Your API_SECRET";

    //POST请求
    public function query($url, array $data = [])
    {
        $headers = array(
            'KEY: ' . $this->key,
            'SIGN: ' . $this->createSign($data)
        );
        $url = $this->tradePath . $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $res = json_decode(curl_exec($ch), true);
        return $res;
    }

    //GET请求
    public function getJson($url)
    {
        $url = $this->tradePath . $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $res = json_decode(curl_exec($ch), true);
        return $res;
    }

    /*
     * 生成随机字符串
     * @param int $length 生成随机字符串的长度
     * @return string $string 生成的随机字符串
     */
    public function nonceStr($length = 10)
    {
        $char = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        if(!is_int($length) || $length < 0) {
            return false;
        }

        $string = '';
        for($i = $length; $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }
        return $string;
    }

    //POST数据发送前处理
    public function postData(array $postData = [])
    {
        $postData['nonce'] = $this->nonceStr();
        $postData['timestamp'] = time() * 1000;
        return $postData;
    }

    //生成签名
    public function createSign(array $postData)
    {
        $secret = $this->secret;
        ksort($postData);
        $postData = http_build_query($postData);
        $sign = hash_hmac('sha512', urldecode($postData), $secret);
        return $sign;
    }

    //获取市场列表
    public function getMarkets()
    {
        return $this->getJson('markets');
    }

    //获取单个市场详情
    public function getTicker()
    {
        return $this->getJson('ticker?pair=ETH_BTC');
    }

    //获取指定市场挂单
    public function getDepth()
    {
        return $this->getJson('depth?pair=ETH_BTC&size=3');
    }

    //获取指定市场成交记录
    public function getTrades()
    {
        return $this->getJson('trades?pair=ETH_BTC&size=3');
    }

    //获取K线图数据
    public function getKlines()
    {
        return $this->getJson('klines?pair=ETH_BTC&period=15min&limit=500');
    }

    //获取账户资金
    public function getBalances()
    {
        $postData = $this->postData();
        return $this->query('balances', $postData);
    }

    //委托下单
    public function getOrder()
    {
        $pair  = 'ETH_BTC';
        $amount  = '15';
        $price  = '0.00739286';
        $type  = 1;
        $data = compact('pair', 'amount', 'price', 'type');
        $postData = $this->postData($data);
        return $this->query('order', $postData);
    }

    //取消挂单
    public function getCancelOrder()
    {
        $orderId  = '1_2_1_137';
        $data = compact('orderId');
        $postData = $this->postData($data);
        return $this->query('cancelOrder', $postData);
    }

    //取消全部挂单
    public function getCancelAllOrder()
    {
        $pair  = 'ETH_BTC';
        $data = compact('pair');
        $postData = $this->postData($data);
        return $this->query('cancelAllOrder', $postData);
    }

    //获取挂单列表
    public function getOrderList()
    {
        $pair  = 'ETH_BTC';
        $limit  = 100;
        $data = compact('pair', 'limit');
        $postData = $this->postData($data);
        return $this->query('orderList', $postData);
    }

    //获取成交记录
    public function getTradeList()
    {
        $pair  = 'ETH_BTC';
        $limit  = 3;
        $data = compact('pair', 'limit');
        $postData = $this->postData($data);
        return $this->query('tradeList', $postData);
    }
}
