<?php
namespace W2w\Lib\ApieSpoutPlugin;

use erasys\OpenApi\Spec\v3\Document;
use erasys\OpenApi\Spec\v3\MediaType;
use erasys\OpenApi\Spec\v3\Operation;
use erasys\OpenApi\Spec\v3\PathItem;
use erasys\OpenApi\Spec\v3\Reference;
use erasys\OpenApi\Spec\v3\Response;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use W2w\Lib\Apie\Interfaces\FormatRetrieverInterface;
use W2w\Lib\Apie\PluginInterfaces\ApieAwareInterface;
use W2w\Lib\Apie\PluginInterfaces\ApieAwareTrait;
use W2w\Lib\Apie\PluginInterfaces\EncoderProviderInterface;
use W2w\Lib\Apie\PluginInterfaces\OpenApiEventProviderInterface;
use W2w\Lib\Apie\Plugins\Core\Encodings\FormatRetriever;
use W2w\Lib\ApieSpoutPlugin\Encoders\OdsEncoder;
use W2w\Lib\ApieSpoutPlugin\Encoders\XlsxEncoder;

class ApieSpoutPlugin implements ApieAwareInterface, OpenApiEventProviderInterface, EncoderProviderInterface
{
    use ApieAwareTrait;

    const DATA = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
        'text/csv' => 'csv',
    ];

    /**
     * @return EncoderInterface[]|DecoderInterface[]
     */
    public function getEncoders(): array
    {
        return [
            new CsvEncoder(),
            new OdsEncoder($this->getApie()->getCacheFolder()),
            new XlsxEncoder($this->getApie()->getCacheFolder())
        ];
    }

    /**
     * @return FormatRetrieverInterface
     */
    public function getFormatRetriever(): FormatRetrieverInterface
    {
        return new FormatRetriever(self::DATA);
    }

    public function onOpenApiDocGenerated(Document $document): Document
    {
        /** @var PathItem[] $paths */
        $paths = $document->paths ?? [];
        foreach ($paths as $path) {
            $this->patch($path->delete);
            $this->patch($path->get);
            $this->patch($path->trace);
            $this->patch($path->head);
            $this->patch($path->patch);
            $this->patch($path->post);
            $this->patch($path->options);
            $this->patch($path->put);
        }
        return $document;
    }

    private function patch(?Operation $operation): ?Operation
    {
        if (null === $operation) {
            return null;
        }
        foreach ($operation->responses as &$response) {
            if ($response instanceof Reference) {
                continue;
            }
            foreach (self::DATA as $contentType => $description) {
                $response->content[$contentType] = new MediaType([]);
            }
        }
        return $operation;
    }
}
