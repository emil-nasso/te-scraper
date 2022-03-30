<?php

namespace App\Spiders;

use App\Enums\TeaStore;
use App\Enums\TeaType;
use App\ItemProcessors\TeaProcessor;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;

class TeCentralen extends BasicSpider
{
    public array $startUrls = [
    ];

    public array $indexPages = [
        [
            'url' => "https://www.tecentralen.se/svart-te.html?product_list_limit=all",
            'teaType' => TeaType::BLACK,
        ],
        [
            'url' => "https://www.tecentralen.se/gront-te.html?product_list_limit=all",
            'teaType' => TeaType::GREEN,
        ],
        [
            'url' => "https://www.tecentralen.se/oolong-te.html?product_list_limit=all",
            'teaType' => TeaType::OOLONG,
        ],
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        TeaProcessor::class
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    public function parseProductPage(Response $response): Generator
    {
        $request = $response->getRequest();

        $name = $response->filter('h1')->text();

        $offers = $response->filter("tr.bagsize")
            ->each(fn ($node) => [
                'size' => (int) $node->filter("[data-th=Paket]")->text(),
                'price' => (double) $node->filter("[data-th='Produkt Pris']")->text()
            ]);

        $imageUrl = $response->filter('.product.media img')->attr('src');
        $type = $request->getOptions()['teaType'];
        $store = TeaStore::TE_CENTRALEN;
        $url = $request->getUri();

        yield $this->item(compact('name', 'offers', 'type', 'store', 'url', 'imageUrl'));
    }

    /**
     * @return Generator<ParseResult>
     */
    public function parseIndexPage(Response $response): Generator
    {
        $teaType = $response->getRequest()->getOptions()['teaType'];
        $urls = collect($response->filter('[data-product-id]')->each(fn ($node) => $node->attr('href')))
            ->filter()
            ->all();

        foreach ($urls as $url) {
            yield $this->request('GET', $url, 'parseProductPage', compact('teaType'));
        }
    }

    public function initialRequests(): array
    {
        return collect($this->indexPages)
            ->map(fn ($page) => new Request('GET', $page['url'], [$this, 'parseIndexPage'], ['teaType' => $page['teaType']]))
            ->all();
    }

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        yield;
    }
}
