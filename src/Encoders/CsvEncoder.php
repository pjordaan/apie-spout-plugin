<?php


namespace W2w\Lib\ApieSpoutPlugin\Encoders;


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterAbstract;

class CsvEncoder extends BaseSpoutEncoder
{

    protected function getFormat(): string
    {
        return 'csv';
    }

    protected function createWriter(): WriterAbstract
    {
        return WriterEntityFactory::createCsvWriter();
    }
}
