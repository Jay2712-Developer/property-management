<?php

namespace App\Livewire\Customer;

use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyListing extends Component
{
    use WithPagination;

    // Component Prop
    public string $status = '';

    // Filter properties
    public string $search = '';
    public string $location_id = '';
    public string $type_id = '';
    public ?float $min_price = null;
    public ?float $max_price = null;
    public string $bedrooms = '';
    public string $sort = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'location_id' => ['except' => ''],
        'type_id' => ['except' => ''],
        'min_price' => ['except' => null],
        'max_price' => ['except' => null],
        'bedrooms' => ['except' => ''],
        'sort' => ['except' => 'latest'],
    ];

    public function mount(string $status = ''): void
    {
        $this->status = $status;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLocationId(): void
    {
        $this->resetPage();
    }

    public function updatedTypeId(): void
    {
        $this->resetPage();
    }

    public function updatedMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatedMaxPrice(): void
    {
        $this->resetPage();
    }

    public function updatedBedrooms(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'location_id', 'type_id', 'min_price', 'max_price', 'bedrooms', 'sort']);
        $this->resetPage();
    }

    public function render()
    {
        if (!Schema::hasTable('properties')) {
            return view('livewire.customer.property-listing', [
                'properties' => Property::query()->paginate(6),
                'locations' => collect(),
                'types' => collect(),
                'totalCount' => 0,
            ]);
        }

        $query = Property::query()
            ->with(['type', 'status', 'location', 'primaryImage', 'images'])
            ->where('is_active', true);

        // 1. Filter by Status Prop (Sale or Rent)
        if (!empty($this->status)) {
            $statusTerm = strtolower(trim($this->status));
            $query->whereHas('status', function ($q) use ($statusTerm) {
                $q->where('slug', $statusTerm)
                  ->orWhere('slug', 'for-' . $statusTerm)
                  ->orWhere('slug', 'like', '%' . $statusTerm . '%');
            });
        }

        // 2. Keyword / Title Search
        if (!empty(trim($this->search))) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('location', function ($locQ) use ($search) {
                      $locQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Location Filter
        if (!empty($this->location_id)) {
            $query->where('location_id', $this->location_id);
        }

        // 4. Property Type Filter
        if (!empty($this->type_id)) {
            $query->where('property_type_id', $this->type_id);
        }

        // 5. Price Filters
        if (!is_null($this->min_price) && $this->min_price > 0) {
            $query->where('price', '>=', $this->min_price);
        }

        if (!is_null($this->max_price) && $this->max_price > 0) {
            $query->where('price', '<=', $this->max_price);
        }

        // 6. Bedrooms Filter
        if (!empty($this->bedrooms)) {
            if ($this->bedrooms === '5+') {
                $query->where('bedrooms', '>=', 5);
            } else {
                $query->where('bedrooms', (int) $this->bedrooms);
            }
        }

        // 7. Sorting
        switch ($this->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('is_featured', 'desc')->latest();
                break;
        }

        $properties = $query->paginate(6);

        $locations = \App\Services\CacheService::getLocations();
        $types = \App\Services\CacheService::getPropertyTypes();

        return view('livewire.customer.property-listing', [
            'properties' => $properties,
            'locations' => $locations,
            'types' => $types,
            'totalCount' => $properties->total(),
        ]);
    }
}
