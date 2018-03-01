<?php
/**
 * Created by PhpStorm.
 * User: zeroke
 * Date: 2018-3-1
 * Time: 14:13
 */

namespace App\Classes;


use Illuminate\Support\Facades\Cache;

class EVEHelper
{
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
            if ($order->location_id != '60003760') continue;
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