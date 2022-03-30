<?php

namespace App\ItemProcessors;

use App\Models\Tea;
use RoachPHP\ItemPipeline\ItemInterface;
use RoachPHP\ItemPipeline\Processors\ItemProcessorInterface;
use RoachPHP\Support\Configurable;

class TeaProcessor implements ItemProcessorInterface
{
    use Configurable;

    public function processItem(ItemInterface $item): ItemInterface
    {
        $item->set('comparisonPrice', $this->calculateComparisonPrice($item->get('offers')));

        Tea::updateOrCreate(
            ['url' => $item->get('url')],
            $item->all()
        );
        return $item;
    }

    private function calculateComparisonPrice(array $offers)
    {
        if (empty($offers)) {
            return null;
        }

        $closestOffer = collect($offers)->filter(fn ($offer) => $offer['size'] <= 100)->sortBy('size')->last();

        $comparisonOffer = $closestOffer ?: collect($offers)->sortBy('size')->first();

        return (100 / $comparisonOffer['size']) * $comparisonOffer['price'];
    }
}
