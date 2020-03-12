<?php


namespace W2w\Lib\ApieSpoutPlugin\Encoders;


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterAbstract;

class XlsxEncoder extends BaseSpoutEncoder
{

    protected function getFormat(): string
    {
        return 'xlsx';
    }

    protected function createWriter(): WriterAbstract
    {
        return WriterEntityFactory::createXLSXWriter();
    }
}
