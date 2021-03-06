<?php

namespace App\Spiders;

use App\Enums\TeaStore;
use App\Enums\TeaType;
use App\Spiders\Drivers\Abicart;

class TeKungen extends Abicart
{
    protected string $baseUrl = "https://www.tekungen.se/backend/jsonrpc/v1?webshop=47000&language=sv&vat_country=SE";
    protected TeaStore $store = TeaStore::TEKUNGEN;

    public array $articleGroups = [
        [
            'articleGroup' => 2637230,
            'teaType' => TeaType::GREEN,
        ],
        [
            'articleGroup' => 2637231,
            'teaType' => TeaType::OOLONG,
        ],
        [
            'articleGroup' => 2637229,
            'teaType' => TeaType::BLACK,
        ],
    ];
}
