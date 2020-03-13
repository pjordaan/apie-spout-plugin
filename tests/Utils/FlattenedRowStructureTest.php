<?php

namespace W2w\Test\ApieSpoutPlugin\Utils;

use PHPUnit\Framework\TestCase;
use W2w\Lib\ApieSpoutPlugin\Utils\FlattenedRowStructure;

class FlattenedRowStructureTest extends TestCase
{
    /**
     * @dataProvider toArrayProvider
     */
    public function testToArray(array $expected, array $input)
    {
        $testItem = new FlattenedRowStructure($input);
        $this->assertEquals($expected, $testItem->toArray());
    }

    public function toArrayProvider()
    {
        yield [
            [
                [],
            ],
            [],
        ];

        yield [
            [
                ['test', 'null'],
                [1, ''],
                [5, 'null'],
            ],
            [
                [
                    'test' => 1,
                    'null' => null
                ],
                [
                    'test' => 5,
                    'null' => 'null'
                ],
            ],
        ];

        yield [
            [
                ['test', 'sub.test', 'sub.sub.0', 'sub.sub.1', 'sub.sub'],
                [1, 2, '', '', 'a'],
                [5, 2, 'a', 'b', ''],
            ],
            [
                [
                    'test' => 1,
                    'sub' => [
                        'test' => 2,
                        'sub' => 'a',
                    ]
                ],
                [
                    'test' => 5,
                    'sub' => [
                        'test' => 2,
                        'sub' => [
                            'a',
                            'b',
                        ]
                    ]
                ],
            ],
        ];
    }
}
