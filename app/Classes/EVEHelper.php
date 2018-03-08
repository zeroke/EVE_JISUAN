<?php
/**
 * Created by PhpStorm.
 * User: zeroke
 * Date: 2018-3-1
 * Time: 14:13
 */

namespace App\Classes;


use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Illuminate\Support\Facades\Cache;

class EVEHelper
{

    public static function getMarketHistory()
    {
        $uri = "https://esi.tech.ccp.is/latest/markets/__region__/history/?type_id=__type__";

        $config = config('Reactions');

        $urlList = [];
        foreach ($config['region'] as $region) {
            foreach ($config['Composite'] as $id) {
                $urlList[] = [
                    'url'         => str_replace(['__region__', '__type__'], [$region['id'], $id], $uri),
                    'region'      => $region['id'],
                    'item'        => $id,
                    'region_name' => $region['name']
                ];
            }
        }

        set_time_limit(0);
        $client = new Client(['verify' => false]);

        $requests = function () use ($client, $urlList) {
            foreach ($urlList as $v) {
                yield function () use ($client, $v) {
                    return $client->getAsync($v['url']);
                };
            }
        };

        $result = [];
        $pool   = new Pool($client, $requests(), [
//            'concurrency' => 10,
            'fulfilled' => function ($response, $index) use ($urlList, &$result) {
                $item = $urlList[$index];
                $res  = $response->getBody()->getContents();
                $json = \GuzzleHttp\json_decode($res);

                $arr = array_slice($json, -30, 30);

                $totalVol = 0;
                foreach ($arr as $v) {
                    $totalVol += $v->volume;
                }
                $avg_vol = $totalVol / count($arr);

                $result[$item['region']][$item['item']] = intval($avg_vol);
            },
            'rejected'  => function ($reason, $index) {
                logger('获取历史交易记录出错', compact('reason', 'index'));
            },
        ]);

        // 开始发送请求
        $promise = $pool->promise();
        $promise->wait();

        Cache::forever('eve_history', $result);
    }


    public static function BuildPriceList($json)
    {
        $orders = self::formatOrderList($json);

        $result = [];
        foreach ($orders as $type => $order) {
            $result[$type] = self::formatPrice($type, $order);
        }

        return $result;
    }

    public static function formatOrderList($arr)
    {
        $itemList = config('Reactions.item');
        $orders   = [];
        foreach ($arr as $order) {
            if (!array_key_exists($order->type_id, $itemList)) continue;
            $temp = [
                'price' => $order->price,
                'num'   => $order->volume_remain
            ];
            if ($order->is_buy_order) {
                $orders[$order->type_id]['buy'][] = $temp;
            } else {
                $orders[$order->type_id]['sell'][] = $temp;
            }
        }

        return $orders;
    }

    public static function formatOrder($arr)
    {
        $orders = [];
        foreach ($arr as $order) {
            if (!$order->is_buy_order && $order->location_id != '60003760') continue;
            $temp = [
                'price' => $order->price,
                'num'   => $order->volume_remain
            ];
            if ($order->is_buy_order) {
                $orders['buy'][] = $temp;
            } else {
                $orders['sell'][] = $temp;
            }
        }

        return $orders;
    }

    public static function formatPrice($type, $orders)
    {
        $itemList = config('Reactions.item');
        if (!empty($orders['sell'])) {
            array_multisort(array_column($orders['sell'], 'price'), SORT_ASC, $orders['sell']);
        }

        $buy_max_price = 0;
        $buy_total_num = 0;
        $buy_total_bal = 0;
        if (!empty($orders['buy'])) {
            array_multisort(array_column($orders['buy'], 'price'), SORT_DESC, $orders['buy']);
            $buy_max_price = $orders['buy'][0]['price'];
            foreach ($orders['buy'] as $o) {
                if ($o['price'] < $buy_max_price * 0.99) continue;
                $buy_total_num += $o['num'];
                $buy_total_bal += $o['num'] * $o['price'];
            }
        }

        $info['name']    = $itemList[$type]['name'];
        $info['sell']    = empty($orders['sell']) ? 0 : $orders['sell'][0]['price'];
        $info['buy']     = $buy_max_price;
        $info['buy_avg'] = empty($orders['buy']) ? 0 : bcdiv($buy_total_bal, $buy_total_num, 2);
        $info['buy_num'] = $buy_total_num;
        return $info;
    }

}