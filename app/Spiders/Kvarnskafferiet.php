<?php

namespace App\Spiders;

use App\Enums\TeaStore;
use App\Enums\TeaType;
use App\Spiders\Drivers\Abicart;

class Kvarnskafferiet extends Abicart
{
    protected string $baseUrl = "https://kvarnskafferiet.se/backend/jsonrpc/v1?webshop=94235&language=sv&vat_country=SE";
    protected TeaStore $store = TeaStore::KVARNSKAFFERIET;

    public array $articleGroups = [
        [
            'articleGroup' => 6816301,
            'teaType' => TeaType::GREEN,
        ],
        [
            'articleGroup' => 6816307,
            'teaType' => TeaType::OOLONG,
        ],
        [
            'articleGroup' => 6816311,
            'teaType' => TeaType::BLACK,
        ],
    ];
}
