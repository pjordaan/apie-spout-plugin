<?php


namespace W2w\Test\ApieSpoutPlugin;


use erasys\OpenApi\Spec\v3\Reference;
use PHPUnit\Framework\TestCase;
use W2w\Lib\Apie\Apie;
use W2w\Lib\Apie\Core\SearchFilters\SearchFilterRequest;
use W2w\Lib\Apie\Plugins\ApplicationInfo\ApiResources\ApplicationInfo;
use W2w\Lib\Apie\Plugins\ApplicationInfo\ApplicationInfoPlugin;
use W2w\Lib\Apie\Plugins\StaticConfig\StaticConfigPlugin;
use W2w\Lib\ApieSpoutPlugin\ApieSpoutPlugin;
use Zend\Diactoros\ServerRequest;

class ApieSpoutPluginTest extends TestCase
{
    public function testPlugin_content_types_were_added()
    {
        $apie = new Apie(
            [
                new ApieSpoutPlugin(),
                new ApplicationInfoPlugin('app', 'test', 'hash'),
                new StaticConfigPlugin('https://apie.nl')
            ]
        );
        $document = $apie->getOpenApiSpecGenerator()->getOpenApiSpec();
        foreach ($document->paths as $path) {
            if ($path->get) {
                foreach ($path->get->responses as $response) {
                    if ($response instanceof Reference) {
                        continue;
                    }
                    foreach (ApieSpoutPlugin::DATA as $key => $value) {
                        $this->assertArrayHasKey($key, $response->content);
                    }
                }
            }
        }
    }

    public function testPlugin_csv_content_type_works()
    {
        $apie = new Apie(
            [
                new ApieSpoutPlugin(),
                new ApplicationInfoPlugin('app', 'test', 'hash'),
                new StaticConfigPlugin('https://apie.nl')
            ]
        );
        $request = (new ServerRequest())->withAddedHeader('Accept', 'text/csv');

        $response = $apie->getApiResourceFacade()->getAll(ApplicationInfo::class, $request, new SearchFilterRequest());
        $actual = (string) $response->getResponse()->getBody();
        //file_put_contents(__DIR__ . '/data/expected.csv', $actual);
        $this->assertEquals(file_get_contents(__DIR__ . '/data/expected.csv'), $actual);
    }
}
