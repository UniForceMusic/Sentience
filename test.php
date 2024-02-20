<?php
use src\util\Data;

include_once "requires.php";

$array = (object) [
    'test1' => [
        'test2' => [
            'test3' => [
                [
                    'test4' => 'You found me 1'
                ],
                [
                    'test4' => 'You found me 2'
                ]
            ]
        ]
    ]
];

echo json_encode(Data::exists($array, 'test1.test2.test3.1.test4'));
