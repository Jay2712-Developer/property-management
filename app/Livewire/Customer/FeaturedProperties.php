<?php

namespace App\Livewire\Customer;

use App\Models\Property;
use App\Models\PropertyType;
use Livewire\Component;

class FeaturedProperties extends Component
{
    public string $typeFilter = 'all';

    public function filterByType(string $type): void
    {
        $this->typeFilter = $type;
    }

    public function render()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('properties')) {
            return view('livewire.customer.featured-properties', [
                'properties' => collect(),
                'types' => collect(),
            ]);
        }

        $query = Property::query()
            ->with(['type', 'status', 'location', 'primaryImage', 'images'])
            ->where('is_active', true)
            ->where('is_featured', true);

        if ($this->typeFilter !== 'all') {
            $query->whereHas('type', function ($q) {
                $q->where('slug', $this->typeFilter);
            });
        }

        $properties = $query->latest()->take(6)->get();

        $types = (class_exists(PropertyType::class) && \Illuminate\Support\Facades\Schema::hasTable('property_types'))
            ? PropertyType::where('is_active', true)->take(5)->get() 
            : collect();

        return view('livewire.customer.featured-properties', [
            'properties' => $properties,
            'types' => $types,
        ]);
    }
}
