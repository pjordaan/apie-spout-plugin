<?php


namespace W2w\Lib\ApieSpoutPlugin\Encoders;


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterAbstract;

class OdsEncoder extends BaseSpoutEncoder
{

    protected function getFormat(): string
    {
        return 'ods';
    }

    protected function createWriter(): WriterAbstract
    {
        return WriterEntityFactory::createODSWriter();
    }
}
