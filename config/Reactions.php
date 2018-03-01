<?php

return [

    'token' => '0Z40ehOdLxs10VklSnOfNvj1nekRm2zrE256190PoyO5IXJlGh2aHfkQIuA95pXu1ARluBhltkwoEJlqZV1u5A2',

    'api' => 'https://esi.tech.ccp.is/latest/markets/__region__/orders/?order_type=all&type_id=__type__',

    'region' => [
        ['id' => 10000002, 'name' => '伏尔戈'],
//        ['id' => 10000060, 'name' => '绝地']
    ],

    'location' => [
        10000002 => 60003760,
        10000060 => 1022734985679
    ],

    'item' => [
        17317 => ['name' => "Fermionic Condensates"],
        16650 => ['name' => "Dysprosium"],
        16652 => ['name' => "Promethium"],
        16651 => ['name' => "Neodymium"],
        16668 => ['name' => "Dysporite"],
        17769 => ['name' => "Fluxed Condensates"],
        16669 => ['name' => "Ferrofluid"],
        17960 => ['name' => "Prometium"],
        33337 => ['name' => "Promethium Mercurite"],
        16667 => ['name' => "Neo Mercurite"],
        16666 => ['name' => "Hyperflurite"],
        16653 => ['name' => "Thulium"],
        16683 => ['name' => "Ferrogel"],
        33336 => ['name' => "Thulium Hafnite"],
        33362 => ['name' => "Nonlinear Metamaterials"],
        33360 => ['name' => "Terahertz Metamaterials"],
        33361 => ['name' => "Plasmonic Metamaterials"],
        33359 => ['name' => "Photonic Metamaterials"],
        16663 => ['name' => "Caesarium Cadmide"],
        16665 => ['name' => "Hexite"],
        16662 => ['name' => "Platinum Technite"],
        16648 => ['name' => "Hafnium"],
        16649 => ['name' => "Technetium"],
        17959 => ['name' => "Vanadium Hafnite"],
        16641 => ['name' => "Chromium"],
        16682 => ['name' => "Hypersynaptic Fibers"],
        16644 => ['name' => "Platinum"],
        16664 => ['name' => "Solerium"],
        16643 => ['name' => "Cadmium"],
        16647 => ['name' => "Caesium"],
        16654 => ['name' => "Titanium Chromide"],
        16646 => ['name' => "Mercury"],
        16660 => ['name' => "Ceramic Powder"],
        16658 => ['name' => "Silicon Diborite"],
        16655 => ['name' => "Crystallite Alloy"],
        16657 => ['name' => "Rolled Tungsten Alloy"],
        16656 => ['name' => "Fernite Alloy"],
        16661 => ['name' => "Sulfuric Acid"],
        16642 => ['name' => "Vanadium"],
        16636 => ['name' => "Silicates"],
        16635 => ['name' => "Evaporite Deposits"],
        16659 => ['name' => "Carbon Polymers"],
        16681 => ['name' => "Nanotransistors"],
        16637 => ['name' => "Tungsten"],
        16638 => ['name' => "Titanium"],
        16639 => ['name' => "Scandium"],
        16640 => ['name' => "Cobalt"],
        16680 => ['name' => "Phenolic Composites"],
        16679 => ['name' => "Fullerides"],
        16633 => ['name' => "Hydrocarbons"],
        16678 => ['name' => "Sylramic Fibers"],
        16634 => ['name' => "Atmospheric Gases"],
        16671 => ['name' => "Titanium Carbide"],
        16670 => ['name' => "Crystalline Carbonide"],
        16672 => ['name' => "Tungsten Carbide"],
        16673 => ['name' => "Fernite Carbide"]
    ],

    'Composite' => [
        17317,
        16683,
        33362,
        33360,
        33361,
        33359,
        16682,
        16681,
        16680,
        16679,
        16678,
        16671,
        16670,
        16672,
        16673,
    ],

    'price' => [
        10000060 => [
            16679 => [
                'sell' => 1399.99
            ],
            16673 => [
                'sell' => 219
            ],
            16678 => [
                'sell' => 540
            ],
        ]
    ],

    'item_detail' => [
        33362 => [
            'item'   => [16654, 16669],
            'output' => 300,
            'vol'    => 1
        ],

        16679 => [
            'item'   => [16659, 16662],
            'output' => 3000,
            'vol'    => 0.15
        ],

        16680 => [
            'item'   => [16658, 16663, 17959],
            'output' => 2200,
            'vol'    => 0.2
        ],

        16678 => [
            'item'   => [16660, 16665],
            'output' => 6000,
            'vol'    => 0.05
        ],

        16682 => [
            'item'   => [16664, 16668, 17959],
            'output' => 750,
            'vol'    => 0.6
        ],

        33360 => [
            'item'   => [16657, 33337],
            'output' => 300,
            'vol'    => 1
        ],

        33359 => [
            'item'   => [16655, 33336],
            'output' => 300,
            'vol'    => 1
        ],

        16670 => [
            'item'   => [16655, 16659],
            'output' => 10000,
            'vol'    => 0.01
        ],

        16683 => [
            'item'   => [16665, 16666, 16669, 17960],
            'output' => 400,
            'vol'    => 1
        ],

        33361 => [
            'item'   => [16656, 16667],
            'output' => 300,
            'vol'    => 1
        ],

        17317 => [
            'item'   => [16663, 16668, 17769, 17960],
            'output' => 200,
            'vol'    => 1.3
        ],

        16671 => [
            'item'   => [16654, 16658],
            'output' => 10000,
            'vol'    => 0.01
        ],

        16672 => [
            'item'   => [16657, 16661],
            'output' => 10000,
            'vol'    => 0.01
        ],

        16673 => [
            'item'   => [16656, 16660],
            'output' => 10000,
            'vol'    => 0.01
        ],

        16681 => [
            'item'   => [16661, 16662, 16667],
            'output' => 1500,
            'vol'    => 0.25
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
    ]
];
