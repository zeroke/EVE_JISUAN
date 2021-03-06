<?php

namespace App\Http\Controllers;


use App\Classes\EVEHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use Illuminate\Support\Facades\Cache;

class MyController extends Controller
{
    private $f_money = 120000;

    public function main()
    {


        return view('my.main', ['data' => $data]);
    }

    public function main2()
    {
        $data['10000002'] = $this->profit_jisuan(10000002, 10000002);
        $data['10000060'] = $this->profit_jisuan(10000002, 10000060);
        return view('my.main2', ['data' => $data]);
    }

    public function jisuan()
    {
        $data['10000002'] = $this->profit_jisuan(10000002, 10000002);
        $data['10000060'] = $this->profit_jisuan(10000002, 10000060);
        return response()->json(array_values($data['10000002']));
    }

    public function updateMarketHistory()
    {
        EVEHelper::getMarketHistory();

        return 'done';
    }

    public function updatePrice()
    {
        // 绝地 10000060  伏尔戈  10000002
        // 绝地 1022734985679
        // 吉他 60003760

        $config  = config('Reactions');
        $urlList = [];
        foreach ($config['region'] as $region) {
            if ($region['id'] == 10000060) continue;
            foreach ($config['item'] as $id => $item) {
                $urlList[] = [
                    'url'         => str_replace(['__region__', '__type__'], [$region['id'], $id], $config['api']),
                    'region'      => $region['id'],
                    'item'        => $id,
                    'name'        => $item['name'],
                    'region_name' => $region['name'],
                    'location'    => $config['location'][$region['id']]
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
                $item   = $urlList[$index];
                $res    = $response->getBody()->getContents();
                $json   = \GuzzleHttp\json_decode($res);
                $orders = EVEHelper::formatOrder($json);

                $result[$item['item']] = EVEHelper::formatPrice($item['item'], $orders);
            },
            'rejected'  => function ($reason, $index) {
                logger('获取订单出错', compact('reason', 'index'));
            },
        ]);

        // 开始发送请求
        $promise = $pool->promise();
        $promise->wait();

        Cache::forever('eve_price:10000002', $result);

        return "done " . count($result) . " item update price";
    }

    public function updateDelvePrice()
    {
        $config  = config('Reactions');
        $urlList = [];

        set_time_limit(0);
        $client = new Client(['verify' => false]);

        $url = "https://esi.tech.ccp.is/latest/markets/structures/1022734985679/?token={$config['token']}";

        $requests = function () use ($client, $url) {
            for ($i = 1; $i <= 10; $i++) {
                yield function () use ($client, $i, $url) {
                    return $client->getAsync($url . "&page={$i}");
                };
            }
        };


        $orderList = [];
        $pool      = new Pool($client, $requests(), [
            'fulfilled' => function ($response, $index) use ($urlList, &$orderList) {
                $res  = $response->getBody()->getContents();
                $json = \GuzzleHttp\json_decode($res);

                if (empty($json)) return;

                $orderList = array_merge($orderList, $json);
            },
            'rejected'  => function ($reason, $index) {
                logger('获取订单出错', compact('reason', 'index'));
            },
        ]);


        // 开始发送请求
        $promise = $pool->promise();
        $promise->wait();
        $result = EVEHelper::BuildPriceList($orderList);

        Cache::forever('eve_price:10000060', $result);

        return 'done';
    }


    // pub
    public function profit_jisuan($buy_region, $sell_region)
    {
        $history    = Cache::get('eve_history');
        $buy_Price  = Cache::get("eve_price:{$buy_region}");
        $sell_Price = Cache::get("eve_price:{$sell_region}");
        $itemDetail = config('Reactions.item_detail');
        $Composite  = config('Reactions.Composite');
        $base_Price = config('Reactions.price');

        foreach ($itemDetail as $id => $item) {
            $item['id'] = $id;
            if (in_array($id, $Composite)) {
                $price        = $sell_Price[$id];
                $item['sell'] = isset($base_Price[$sell_region][$id]) ? $base_Price[$sell_region][$id]['sell'] : $price['sell'];
            } else {
                $price        = $buy_Price[$id];
                $item['sell'] = isset($base_Price[$buy_region][$id]) ? $base_Price[$buy_region][$id]['sell'] : $price['sell'];
            }
            $item['name']           = $price['name'];
            $item['sell_money_in']  = 0;
            $item['buy_money_in']   = 0;
            $item['buy']            = $price['buy_avg'];
            $item['buy_num']        = $price['buy_num'];
            $output                 = isset($item['output']) ? $item['output'] : 200;
            $item['sell_money_out'] = bcmul($output, $item['sell']);
            $item['buy_money_out']  = bcmul($output, $item['buy']);
            $item['output']         = $output;
            foreach ($item['item'] as $_id) {
                $item_price               = $buy_Price[$_id];
                $item['item_price'][$_id] = [
                    'sell' => $item_price['sell'],
                    'buy'  => $item_price['buy_avg']
                ];
                $item['sell_money_in']    += bcmul($item_price['sell'], 100);
                $item['buy_money_in']     += bcmul($item_price['buy_avg'], 100);
            }

            // 卖单进 卖单出
            $item['profit_0'] = $item['sell_money_out'] - $item['sell_money_in'] - $this->f_money;
            // 卖单进 买单出
            $item['profit_1'] = $item['buy_money_out'] - $item['sell_money_in'] - $this->f_money;
            // 买单进 卖单出
            $item['profit_2'] = $item['sell_money_out'] - $item['buy_money_in'] - $this->f_money;
            // 买单进 买单出
            $item['profit_3'] = $item['buy_money_out'] - $item['buy_money_in'] - $this->f_money;

            $itemDetail[$id] = $item;
        }

        $data = [];
        foreach ($Composite as $id) {
            $_item = $itemDetail[$id];

            $_item['profit_item_0'] = 0;
            $_item['profit_item_1'] = 0;
            $_item['profit_item_2'] = 0;
            $_item['profit_item_3'] = 0;
            foreach ($_item['item'] as $id2) {
                $_item['profit_item_0'] += $itemDetail[$id2]['profit_0'];
                $_item['profit_item_1'] += $itemDetail[$id2]['profit_1'];
                $_item['profit_item_2'] += $itemDetail[$id2]['profit_2'];
                $_item['profit_item_3'] += $itemDetail[$id2]['profit_3'];
            }

            $count = (count($_item['item']) + 2) * ($sell_region == 10000002 ? 2 : 1.5);

            $_item['profit_avg_0'] = bcdiv($_item['profit_0'] * 2 + $_item['profit_item_0'], $count);
            $_item['profit_avg_1'] = bcdiv($_item['profit_1'] * 2 + $_item['profit_item_0'], $count);
            $_item['profit_avg_2'] = bcdiv($_item['profit_2'] * 2 + $_item['profit_item_3'], $count);
            $_item['profit_avg_3'] = bcdiv($_item['profit_3'] * 2 + $_item['profit_item_3'], $count);
            $_item['avg_vol']      = $history[$sell_region][$id];

            $b                            = $sell_region == 10000002 ? 2 : 1.5;
            $_item['profit_simple_avg_0'] = bcdiv($_item['profit_0'], $b);
            $_item['profit_simple_avg_3'] = bcdiv($_item['profit_3'], $b);

            $data[$id] = $_item;
        }

        return $data;
    }
}
