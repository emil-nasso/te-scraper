<?php

namespace App\Spiders\Drivers;

use App\Enums\TeaStore;
use App\ItemProcessors\TeaProcessor;
use Generator;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;

class Abicart extends BasicSpider
{
    protected string $baseUrl;
    protected TeaStore $store;

    protected array $articleGroups = [];

    public array $startUrls = [];

    public array $downloaderMiddleware = [
    ];

    public array $spiderMiddleware = [
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

    public function parseProductList(Response $response): Generator
    {
        $request = $response->getRequest();
        $teaType = $request->getOptions()['teaType'];

        $items = json_decode($response->getBody(), true);

        $teas = collect($items['result'])->map(function ($item) use ($teaType) {
            $price = $item['pricing'][0]['regular']['incVat']['SEK'];
            return [
                'name' => $item['name']['sv'],
                'url' => $item['url']['sv'],
                'offers' => collect($item['choices'][0]['options'] ?? [])
                    ->map(fn ($choice) => [
                        'size' => (int) $choice['name']['sv'],
                        'price' =>  $price + ($choice['price']['SEK'] ?? 0),
                    ])
                    ->all(),
                'imageUrl' => $item['images'][0],
                'store' => $this->store,
                'type' => $teaType,
            ];
        })->all();

        foreach ($teas as $tea) {
            yield $this->item($tea);
        }
    }

    public function initialRequests(): array
    {
        return collect($this->articleGroups)
            ->map(function ($group) {
                $url = $this->baseUrl;
                $data = [
                    'id' => 77,
                    'jsonrpc' => '2.0',
                    'method' => 'Article.list',
                    'params' => [
                        [
                            'hasChoices' => true,
                            'isBuyable' => true,
                            'priceInquiryRequired' => true,
                            'presentationOnly' => true,
                            'quantityInfo' => true,
                            'type' => true,
                            'uid' => true,
                            'name' => 'sv',
                            'imagesAltText' => true,
                            'pricing' => true,
                            'url' => 'sv',
                            'images' => true,
                            'unit' => true,
                            'articlegroup' => true,
                            'news' => true,
                            'showPricesIncludingVat' => true,
                            'choices' => [
                                "name" =>  "sv",
                                "options" =>  (object) [],
                                "description" =>  "sv",
                                "type" =>  true,
                                "uid" =>  true,
                                "quantityChoicesAffectsOptionPrices" =>  true,
                                "mandatory" =>  true,
                                "maximum" =>  true,
                                "minimum" =>  true,
                                "maxSize" =>  true,
                                "multipleOf" =>  true,
                                "quantity" =>  true
                            ],
                        ],
                        [
                            'filters' => [
                                '/showInArticlegroups' => [
                                    'containsAny' => [
                                        $group['articleGroup'],
                                    ],
                                ],
                            ],
                            'descending' => true,
                            'sort' => 'numSold',
                            'limit' => 500,
                        ],
                    ],
                ];
                return new Request('POST', $url, [$this, 'parseProductList'], [
                    'teaType' => $group['teaType'],
                    'json' => $data,
                ]);
            })
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
