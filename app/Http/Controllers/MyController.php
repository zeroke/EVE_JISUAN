<?php

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class MyController extends Controller
{
    private $_Item = [
        33362 => [
            'item'   => [16654, 16669],
            'output' => 300
        ],

        16679 => [
            'item'   => [16659, 16662],
            'output' => 3000
        ],

        16680 => [
            'item'   => [16658, 16663, 17959],
            'output' => 2200
        ],

        16678 => [
            'item'   => [16660, 16665],
            'output' => 6000
        ],

        16682 => [
            'item'   => [16664, 16668, 17959],
            'output' => 750
        ],

        33360 => [
            'item'   => [16657, 33337],
            'output' => 300
        ],

        33359 => [
            'item'   => [16655, 33336],
            'output' => 300
        ],

        16670 => [
            'item'   => [16655, 16659],
            'output' => 10000
        ],

        16683 => [
            'item'   => [16665, 16666, 16669, 17960],
            'output' => 400
        ],

        33361 => [
            'item'   => [16656, 16667],
            'output' => 300
        ],

        17317 => [
            'item'   => [16663, 16668, 17769, 17960],
            'output' => 200
        ],

        16671 => [
            'item'   => [16654, 16658],
            'output' => 10000
        ],

        16672 => [
            'item'   => [16657, 16661],
            'output' => 10000
        ],

        16673 => [
            'item'   => [16656, 16660],
            'output' => 10000
        ],

        16681 => [
            'item'   => [16661, 16662, 16667],
            'output' => 1500
        ],


        /////////

        16663 => [
            'item' => [16643, 16647]
        ],
        16659 => [
            'item' => [16633, 16636]
        ],
        16660 => [
            'item' => [16635, 16636]
        ],
        16655 => [
            'item' => [16640, 16643]
        ],
        16668 => [
            'item' => [16646, 16650]
        ],
        16656 => [
            'item' => [16639, 16642]
        ],
        16669 => [
            'item' => [16648, 16650]
        ],
        17769 => [
            'item' => [16651, 16653]
        ],
        16665 => [
            'item' => [16641, 16644]
        ],
        16666 => [
            'item' => [16642, 16652]
        ],
        16667 => [
            'item' => [16646, 16651]
        ],
        16662 => [
            'item' => [16644, 16649]
        ],
        33337 => [
            'item' => [16646, 16652]
        ],
        17960 => [
            'item' => [16643, 16652]
        ],
        16657 => [
            'item' => [16637, 16644]
        ],
        16658 => [
            'item' => [16635, 16636]
        ],
        16664 => [
            'item' => [16641, 16647]
        ],
        16661 => [
            'item' => [16634, 16635]
        ],
        33336 => [
            'item' => [16648, 16653]
        ],
        16654 => [
            'item' => [16638, 16641]
        ],
        17959 => [
            'item' => [16642, 16648]
        ],
    ];

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
        $eve_price = Cache::get('eve_price');

        foreach ($this->_Item as $id => $item) {
            $item['name']           = $eve_price[$id]['name'];
            $item['sell_money_in']  = 0;
            $item['buy_money_in']   = 0;
            $item['sell']           = $eve_price[$id]['sell'];
            $item['buy']            = $eve_price[$id]['buy_avg'];
            $output                 = isset($item['output']) ? $item['output'] : 200;
            $item['sell_money_out'] = bcmul($output, $item['sell']);
            $item['buy_money_out']  = bcmul($output, $item['buy']);
            $item['output']         = $output;
            foreach ($item['item'] as $_id) {
                $item['item_price'][$_id] = [
                    'sell' => $eve_price[$_id]['sell'],
                    'buy'  => $eve_price[$_id]['buy_avg']
                ];
                $item['sell_money_in']    += bcmul($eve_price[$_id]['sell'], 100);
                $item['buy_money_in']     += bcmul($eve_price[$_id]['buy_avg'], 100);
            }

            // 卖单进 卖单出
            $item['profit_0'] = $item['sell_money_out'] - $item['sell_money_in'] - $this->f_money;
            // 卖单进 买单出
            $item['profit_1'] = $item['buy_money_out'] - $item['sell_money_in'] - $this->f_money;
            // 买单进 卖单出
            $item['profit_2'] = $item['sell_money_out'] - $item['buy_money_in'] - $this->f_money;
            // 买单进 买单出
            $item['profit_3'] = $item['buy_money_out'] - $item['buy_money_in'] - $this->f_money;

            $this->_Item[$id] = $item;
        }

        $Composite = config('Reactions.Composite');

        $data = [];
        foreach ($Composite as $id) {
            $_item = $this->_Item[$id];

            $_item['profit_item_0'] = 0;
            $_item['profit_item_1'] = 0;
            $_item['profit_item_2'] = 0;
            $_item['profit_item_3'] = 0;
            foreach ($_item['item'] as $id2) {
                $_item['profit_item_0'] += $this->_Item[$id2]['profit_0'];
                $_item['profit_item_1'] += $this->_Item[$id2]['profit_1'];
                $_item['profit_item_2'] += $this->_Item[$id2]['profit_2'];
                $_item['profit_item_3'] += $this->_Item[$id2]['profit_3'];
            }

            $count = count($_item['item']) + 2;

            $_item['profit_avg_0'] = bcdiv($_item['profit_0'] * 2 + $_item['profit_item_0'], $count);
            $_item['profit_avg_1'] = bcdiv($_item['profit_1'] * 2 + $_item['profit_item_0'], $count);
            $_item['profit_avg_2'] = bcdiv($_item['profit_2'] * 2 + $_item['profit_item_3'], $count);
            $_item['profit_avg_3'] = bcdiv($_item['profit_3'] * 2 + $_item['profit_item_3'], $count);

            $data[$id] = $_item;
        }

        return view('my.main2', ['data' => $data]);
    }

    public function updatePrice()
    {
        set_time_limit(0);
        $http = new Client(['verify' => false]);
        $list = config('Reactions.item');
        $uri  = config('Reactions.api');

        $price = [];
        foreach ($list as $id => $name) {
            $res     = $http->get($uri . '&type_id=' . $id);
            $jsonStr = $res->getBody()->getContents();
            $json    = \GuzzleHttp\json_decode($jsonStr);
            $orders  = [];
            foreach ($json as $order) {
                if ($order->location_id != 60003760) continue;
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

            array_multisort(array_column($orders['buy'], 'price'), SORT_DESC, $orders['buy']);
            array_multisort(array_column($orders['sell'], 'price'), SORT_ASC, $orders['sell']);

            $buy_max_price = $orders['buy'][0]['price'];
            $buy_total_num = 0;
            $buy_total_bal = 0;
            foreach ($orders['buy'] as $o) {
                if ($o['price'] < $buy_max_price * 0.99) continue;
                $buy_total_num += $o['num'];
                $buy_total_bal += $o['num'] * $o['price'];
            }

            $info['name']    = $name;
            $info['sell']    = $orders['sell'][0]['price'];
            $info['buy']     = $buy_max_price;
            $info['buy_avg'] = bcdiv($buy_total_bal, $buy_total_num, 2);
            $info['buy_num'] = $buy_total_num;

            $price[$id] = $info;
        }
        Cache::forever('eve_price', $price);
        return 'done';
    }
}
