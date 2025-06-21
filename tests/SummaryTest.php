<?php

namespace Wcms\Tests;

use PHPUnit\Framework\TestCase;
use Wcms\Header;
use Wcms\Summary;

class SummaryTest extends TestCase
{
    /**
     * @test
     * @param mixed[] $options
     * @dataProvider sumparserProvider
     */
    public function sumparserTest(array $options, string $expected): void
    {
        $summary = new Summary($options);
        $this->assertEquals($expected, $summary->sumparser());
    }

    /** @return mixed[][] */
    public function sumparserProvider(): array
    {
        return [
            [
                [
                    'sum' => [
                        new Header('test-1', 1, 'Test 1'),
                        new Header('test-1-2', 2, 'Test 1.2')
                    ]
                ],
                // phpcs:ignore Generic.Files.LineLength.TooLong
                '<ul class="summary"><li><a href="#test-1">Test 1</a><ul><li><a href="#test-1-2">Test 1.2</a></li></ul></li></ul>'
            ],
            [
                [
                    'sum' => [
                        new Header('test-1', 1, 'Test 1'),
                        new Header('test-1-2', 2, 'Test 1.2')
                    ],
                    'min' => 2
                ],
                '<ul class="summary"><li><a href="#test-1-2">Test 1.2</a></li></ul>'
            ],
            [
                [
                    'sum' => [
                        new Header('test-1', 1, 'Test 1'),
                        new Header('test-1-2', 2, 'Test 1.2')
                    ],
                    'max' => 1
                ],
                '<ul class="summary"><li><a href="#test-1">Test 1</a></li></ul>'
            ]
        ];
    }
}
