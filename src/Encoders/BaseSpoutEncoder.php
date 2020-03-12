<?php
namespace W2w\Lib\ApieSpoutPlugin\Encoders;

use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterAbstract;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

abstract class BaseSpoutEncoder implements EncoderInterface
{
    private $cacheFolder;

    public function __construct(?string $cacheFolder = null)
    {
        $this->cacheFolder = $cacheFolder ?? sys_get_temp_dir();
    }

    abstract protected function getFormat(): string;

    abstract protected function createWriter() :WriterAbstract;


    /**
     * Encodes data into the given format.
     *
     * @param mixed $data Data to encode
     * @param string $format Format name
     * @param array $context Options that normalizers/encoders have access to
     *
     * @return string|int|float|bool
     *
     * @throws UnexpectedValueException
     */
    public function encode($data, $format, array $context = [])
    {
        $filename = $this->cacheFolder . DIRECTORY_SEPARATOR . time() . '.' . $this->getFormat();
        $writer = $this->createWriter();
        if (method_exists($writer, 'setTempFolder') && is_callable([$writer, 'setTempFolder'])) {
            $writer = $writer->setTempFolder($this->cacheFolder);
        }
        $writer
            ->openToFile($filename)
            ->addRows($this->toRowList($data));
        $writer->close();
        return file_get_contents($filename);
    }

    /**
     * @param array $data
     * @return Row[]
     */
    private function toRowList(array $data): array
    {
        $list = $this->isList($data) ? $data : [$data];
        $result = [];
        foreach ($list as $item) {
            $result[] = WriterEntityFactory::createRowFromArray($item)   ;
        }
        return $result;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isList(array $data): bool
    {
        return array_keys($data) === array_keys(array_values($data));
    }

    /**
     * Checks whether the serializer can encode to given format.
     *
     * @param string $format Format name
     *
     * @return bool
     */
    public function supportsEncoding($format)
    {
        return $format === $this->getFormat();
    }
}
