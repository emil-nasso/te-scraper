<div class="max-w-4xl">
    <h1 class="text-3xl mb-8">Tea browser - compare teas from different stores</h1>
    <div class="rounded border p-4 max-w-lg mb-4 mx-auto">
        <div class="text-xl mb-4">
            Filter
        </div>
        <div>
            Type:
            <select wire:model="teaType">
                <option value=""></option>
                @foreach ($teaTypes as $teaType)
                    <option value="{{ $teaType->value }}">{{ $teaType->value }}</option>
                @endforeach
            </select>
        </div>

        <div>
            Store:
            <select wire:model="store">
                <option value=""></option>
                @foreach ($stores as $store)
                    <option value="{{ $store->value }}">{{ $store->value }}</option>
                @endforeach
            </select>
        </div>

        <div>
            Name: <input class="border rounded" type="text" wire:model="name"/>
        </div>

        <div>
            Sort by:
            <select wire:model="sortField">
                <option value="name">name</option>
                <option value="price">price</option>
            </select>
            Sort direction
            <select wire:model="sortDirection">
                <option value="asc">ascending</option>
                <option value="desc">descending</option>
            </select>
        </div>
        <div>
            <button class="border rounded bg-gray-100 shadow px-2 py-1 mt-2" wire:action="resetFilters">Reset filters</button>
        </div>
    </div>

    {{-- @dump($teas) --}}

    <table class="w-full">
        @foreach ($teas as $tea)
            <tr class="border">
                <td class="px-2">
                    <img class="w-32" src="{{ route('tea.image', $tea->id) }}"/>
                </td>
                <td class="px-2">
                    <a class="text-blue-500" href="{{ $tea->url }}" target="_blank">
                        {{ $tea->name }}
                    </a>
                </td>
                <td class="px-2 {{ $tea->store->labelColor() }}">{{ $tea->store->label() }}</td>
                <td  class="px-2">{{ $tea->type->value }}</td>
                <td>
                    @if ($tea->comparisonPrice)
                        {{ $tea->comparisonPrice }}kr/100gr
                    @endif
                </td>
                <td  class="px-2">
                    <ul>
                        @foreach ($tea->offers as $offer)
                            <li>{{ $offer['size'] }} gr - {{ $offer['price'] }} kr</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        @endforeach
    </table>

    <div class="mt-4">
        {{ $teas->links() }}
    </div>
</div>
