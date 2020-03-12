<?php


namespace W2w\Test\ApieSpoutPlugin\Encoders;

use PHPUnit\Framework\TestCase;
use W2w\Lib\ApieSpoutPlugin\Encoders\XlsxEncoder;

class XlsxEncoderTest extends TestCase
{
    public function testEncode()
    {
        $item = new XlsxEncoder(sys_get_temp_dir());
        $result = $item->encode(
            [
                ['test' => 1],
                ['test' => 2],
            ],
            []
        );
        $this->assertNotEmpty($result);
    }
}
