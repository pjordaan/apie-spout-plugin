<?php


namespace W2w\Lib\ApieSpoutPlugin\Utils;

use Symfony\Component\Serializer\Encoder\CsvEncoder;

/**
 * Logic based on the Symfony Serializer CsvEncoder
 *
 * @see CsvEncoder::flatten()
 * @see CsvEncoder::extractHeaders()
 */
class FlattenedRowStructure
{
    private $input = [];

    private $headers = [];

    public function __construct(array $input)
    {
        foreach ($input as $key => $value) {
            $this->input[$key] = [];
            $this->flatten($value, $this->input[$key]);
        }
        $this->headers = $this->extractHeaders($this->input);
    }

    public function toArray(): array
    {
        $rows = [];
        $rows[] = $this->headers;
        foreach ($this->input as $rowToProcess) {
            $newRow = [];
            foreach ($this->headers as $header) {
                $newRow[] = $rowToProcess[$header] ?? '';
            }
            $rows[] = $newRow;
        }
        return $rows;
    }

    private function flatten(iterable $array, array &$result, string $parentKey = '')
    {
        foreach ($array as $key => $value) {
            if (is_iterable($value)) {
                $this->flatten($value, $result, $parentKey.$key.'.');
            } else {
                // Ensures an actual value is used when dealing with true and false
                if ($value === false) {
                    $result[$parentKey . $key] = 0;
                } elseif ($value === true) {
                    $result[$parentKey . $key] = 1;
                } else {
                    $result[$parentKey . $key] = $value;
                }
            }
        }
    }

    /**
     * @return string[]
     */
    private function extractHeaders(iterable $data): array
    {
        $headers = [];
        $flippedHeaders = [];

        foreach ($data as $row) {
            $previousHeader = null;
            foreach ($row as $header => $_) {
                if (isset($flippedHeaders[$header])) {
                    $previousHeader = $header;
                    continue;
                }

                if (null === $previousHeader) {
                    $n = \count($headers);
                } else {
                    $n = $flippedHeaders[$previousHeader] + 1;

                    for ($j = \count($headers); $j > $n; --$j) {
                        ++$flippedHeaders[$headers[$j] = $headers[$j - 1]];
                    }
                }

                $headers[$n] = $header;
                $flippedHeaders[$header] = $n;
                $previousHeader = $header;
            }
        }

        return $headers;
    }
}
