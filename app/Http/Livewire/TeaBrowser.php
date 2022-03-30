<?php

namespace App\Http\Livewire;

use App\Enums\TeaStore;
use App\Enums\TeaType;
use App\Models\Tea;
use Livewire\Component;
use Livewire\WithPagination;

class TeaBrowser extends Component
{
    use WithPagination;

    public ?string $teaType = null;
    public ?string $store = null;
    public string $name = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    public function updating($name, $value)
    {
        if (in_array($name, ['teaType', 'store', 'name', 'sortField', 'sortDirection']))  {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset();
    }

    public function render()
    {
        $teas = Tea::query()
            ->where(
                fn ($query) => $query
                    ->when(
                        $this->name,
                        fn ($query) => $query->where('name', 'like', '%' . $this->name . '%')
                    )
                    ->when(
                        $this->teaType,
                        fn ($query) => $query->where('type', TeaType::from($this->teaType))
                    )
                    ->when(
                        $this->store,
                        fn ($query) => $query->where('store', TeaStore::from($this->store))
                    )
            )
            ->orderBy(
                match ($this->sortField) {
                    'name' => 'name',
                    'price' => 'comparisonPrice',
                },
                $this->sortDirection
            )
            ->paginate(10);

        return view('livewire.tea-browser', [
            'teas' => $teas,
            'teaTypes' => TeaType::cases(),
            'stores' => TeaStore::cases(),
        ]);
    }
}
