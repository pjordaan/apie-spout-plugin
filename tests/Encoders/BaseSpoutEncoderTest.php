<?php

namespace W2w\Test\ApieSpoutPlugin\Encoders;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\CSV\Writer;
use Box\Spout\Writer\WriterAbstract;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use W2w\Lib\ApieSpoutPlugin\Encoders\BaseSpoutEncoder;

class BaseSpoutEncoderTest extends TestCase
{
    public function testEncode()
    {
        $code = [
            ['test' => 1],
            ['test' => 2],
        ];
        $writer = $this->prophesize(Writer::class);
        $filename = null;
        try {
            $writer->openToFile(
                Argument::that(
                    function ($args) use (&$filename) {
                        $filename = $args;
                        return true;
                    }
                )
            )->shouldBeCalled()->willReturn($writer->reveal());
            $writer->addRows(
                Argument::that(
                    function ($args) use (&$filename) {
                        $this->assertIsArray($args);
                        $this->assertCount(3, $args);
                        foreach ($args as $item) {
                            $this->assertInstanceOf(Row::class, $item);
                        }
                        $this->assertNotEmpty($filename);
                        file_put_contents($filename, json_encode($args));
                        return true;
                    }
                )
            )->shouldBeCalled()->willReturn($writer->reveal());
            $writer->close()->shouldBeCalled();

            $testItem = new class($writer->reveal()) extends BaseSpoutEncoder
            {

                private $writer;

                public function __construct(WriterAbstract $writer)
                {
                    $this->writer = $writer;
                    parent::__construct(null);
                }

                protected function getFormat(): string
                {
                    return 'test';
                }

                protected function createWriter(): WriterAbstract
                {
                    return $this->writer;
                }
            };

            $result = $testItem->encode(
                $code,
                []
            );
        } finally {
            if ($filename) {
                @unlink($filename);
            }
        }
    }
}
