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
        $str  = file_get_contents('G:\\Price List.txt');
        $arr  = explode("\r\n", $str);
        $list = [];

        foreach ($arr as $k => $v) {
            if ($k == 0 || empty($v) || strpos($v, "Unrefined"))
                continue;

            $temp           = explode("|", $v);
            $list[$temp[5]] = [
                'name'  => $temp[1],
                'price' => $temp[2],
                'group' => $temp[0]
            ];
        }

        foreach ($list as $k => $v) {
            if (array_key_exists($k, $this->_Item)) {
                $_item              = $this->_Item[$k];
                $_item['money_in']  = 0;
                $_item['price']     = $list[$k]['price'];
                $output             = isset($_item['output']) ? $_item['output'] : 200;
                $_item['money_out'] = bcmul($output, $v['price']);
                $_item['output']    = $output;
                foreach ($_item['item'] as $id) {
                    $_item['item_money'][] = bcmul($list[$id]['price'], 100);
                    $_item['money_in']     += bcmul($list[$id]['price'], 100);
                }
                $_item['profit'] = $_item['money_out'] - $_item['money_in'] - $this->f_money;

                $this->_Item[$k] = $_item;
            }
        }

        $data = [];
        foreach ($list as $id => $v) {
            if ($v['group'] != 'Composite') continue;

            $temp         = [];
            $temp['name'] = $v['name'];
            $_item        = $this->_Item[$id];

            $temp['profit']      = $_item['profit'] * 2;
            $temp['profit_item'] = 0;
            foreach ($_item['item'] as $id2) {
                $temp['item'][] = [
                    'name'   => $list[$id2]['name'],
                    'price'  => $list[$id2]['price'],
                    'profit' => $this->_Item[$id2]['profit']
                ];

                $temp['profit_item'] += $this->_Item[$id2]['profit'];
            }

            $temp['profit_avg'] = intval(($temp['profit'] + $temp['profit_item']) / (count($_item['item']) + 2));
            $temp['output']     = $_item['output'];
            $temp['price']      = $_item['price'];

            $data[$v['group']][$id] = $temp;
        }

        array_multisort(array_column($data['Composite'], 'profit_avg'), SORT_DESC, $data['Composite']);

        return view('my.main', ['data' => $data]);
    }

    public function main2()
    {
        $data['feg']   = $this->profit_jisuan(10000002, 10000002);
        $data['delve'] = $this->profit_jisuan(10000002, 10000060);
        return view('my.main2', ['data' => $data]);
    }

    public function updatePrice()
    {
        // 绝地 10000060  伏尔戈  10000002
        // 绝地 1022734985679
        // 吉他 60003760

        $config  = config('Reactions');
        $urlList = [];
        foreach ($config['region'] as $region) {
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

        return 'done';
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
        $buy_Price  = Cache::get("eve_price:{$buy_region}");
        $sell_Price = Cache::get("eve_price:{$sell_region}");
        $itemDetail = config('Reactions.item_detail');
        $Composite  = config('Reactions.Composite');

        foreach ($itemDetail as $id => $item) {
            if (in_array($id, $Composite)) {
                $price = $sell_Price[$id];
            } else {
                $price = $buy_Price[$id];
            }
            $item['name']           = $price['name'];
            $item['sell_money_in']  = 0;
            $item['buy_money_in']   = 0;
            $item['sell']           = $price['sell'];
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

            $count = count($_item['item']) + 2;

            $_item['profit_avg_0'] = bcdiv($_item['profit_0'] * 2 + $_item['profit_item_0'], $count);
            $_item['profit_avg_1'] = bcdiv($_item['profit_1'] * 2 + $_item['profit_item_0'], $count);
            $_item['profit_avg_2'] = bcdiv($_item['profit_2'] * 2 + $_item['profit_item_3'], $count);
            $_item['profit_avg_3'] = bcdiv($_item['profit_3'] * 2 + $_item['profit_item_3'], $count);

            $data[$id] = $_item;
        }

        return $data;
    }
}
