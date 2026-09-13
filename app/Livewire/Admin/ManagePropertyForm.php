<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Agent;
use App\Models\Amenity;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('admin.layouts.app')]
#[Title('Manage Property - TISHA Real Estate')]
class ManagePropertyForm extends Component
{
    use WithFileUploads;

    public ?Property $property = null;
    public ?string $propertyId = null;
    public bool $isEditing = false;

    #[Rule('required|string|min:3|max:255')]
    public string $title = '';

    #[Rule('required|string|max:255')]
    public string $slug = '';

    #[Rule('nullable|string')]
    public ?string $description = '';

    #[Rule('required|exists:property_types,id')]
    public ?int $property_type_id = null;

    #[Rule('required|exists:property_statuses,id')]
    public ?int $property_status_id = null;

    #[Rule('required|exists:locations,id')]
    public ?int $location_id = null;

    #[Rule('nullable|exists:agents,id')]
    public ?int $agent_id = null;

    #[Rule('required|numeric|min:0')]
    public $price = null;

    #[Rule('nullable|string|max:50')]
    public ?string $price_label = null;

    #[Rule('required|integer|min:0|max:99')]
    public $bedrooms = 1;

    #[Rule('required|integer|min:0|max:99')]
    public $bathrooms = 1;

    #[Rule('nullable|integer|min:0')]
    public $sqft = null;

    #[Rule('nullable|integer|min:0|max:50')]
    public $garage = 0;

    #[Rule('nullable|integer|min:1800|max:2099')]
    public $year_built = null;

    #[Rule('boolean')]
    public bool $is_featured = false;

    #[Rule('boolean')]
    public bool $is_active = true;

    #[Rule('array')]
    public array $selectedAmenities = [];

    #[Rule(['newImages.*' => 'image|max:5120'])]
    public array $newImages = [];

    public array $existingImages = [];

    /**
     * Mount component and load property if editing.
     */
    public function mount(?string $propertyId = null): void
    {
        if ($propertyId) {
            $this->isEditing = true;
            $this->propertyId = $propertyId;

            abort_unless(
                auth()->user()?->can('edit_properties'),
                403,
                'Unauthorized. You do not have permission to edit properties.'
            );

            $decodedId = Property::decodeHashid($propertyId) ?? (is_numeric($propertyId) ? (int) $propertyId : null);

            $this->property = $decodedId
                ? Property::with(['amenities', 'images'])->findOrFail($decodedId)
                : Property::with(['amenities', 'images'])->where('slug', $propertyId)->firstOrFail();

            $this->title = $this->property->title;
            $this->slug = $this->property->slug;
            $this->description = $this->property->description;
            $this->property_type_id = $this->property->property_type_id;
            $this->property_status_id = $this->property->property_status_id;
            $this->location_id = $this->property->location_id;
            $this->agent_id = $this->property->agent_id;
            $this->price = (float) $this->property->price;
            $this->price_label = $this->property->price_label;
            $this->bedrooms = (int) $this->property->bedrooms;
            $this->bathrooms = (int) $this->property->bathrooms;
            $this->sqft = $this->property->sqft;
            $this->garage = (int) $this->property->garage;
            $this->year_built = $this->property->year_built;
            $this->is_featured = (bool) $this->property->is_featured;
            $this->is_active = (bool) $this->property->is_active;
            $this->selectedAmenities = $this->property->amenities->pluck('id')->toArray();

            $this->loadExistingImages();
        } else {
            $this->isEditing = false;

            abort_unless(
                auth()->user()?->can('create_properties'),
                403,
                'Unauthorized. You do not have permission to create properties.'
            );

            $this->year_built = (int) date('Y');

            // Default to first active property type and status if available
            $defaultType = PropertyType::active()->first();
            if ($defaultType) {
                $this->property_type_id = $defaultType->id;
            }

            $defaultStatus = PropertyStatus::where('is_system_default', true)->first() ?? PropertyStatus::first();
            if ($defaultStatus) {
                $this->property_status_id = $defaultStatus->id;
            }

            $defaultLocation = Location::active()->first();
            if ($defaultLocation) {
                $this->location_id = $defaultLocation->id;
            }
        }
    }

    /**
     * Auto-generate slug when title is updated in create mode or when slug is empty.
     */
    public function updatedTitle(string $value): void
    {
        if (!$this->isEditing || empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    /**
     * Force re-generate slug from current title.
     */
    public function generateSlug(): void
    {
        if (!empty($this->title)) {
            $this->slug = Str::slug($this->title);
        }
    }

    /**
     * Remove a newly uploaded image before saving.
     */
    public function removeNewImage(int $index): void
    {
        if (isset($this->newImages[$index])) {
            unset($this->newImages[$index]);
            $this->newImages = array_values($this->newImages);
        }
    }

    /**
     * Load existing persisted images for editing.
     */
    public function loadExistingImages(): void
    {
        if ($this->property) {
            $this->existingImages = $this->property->images()
                ->orderBy('sort_order')
                ->get()
                ->toArray();
        }
    }

    /**
     * Delete an existing image from database and storage.
     */
    public function removeExistingImage(int $imageId): void
    {
        if (!$this->isEditing || !$this->property) {
            return;
        }

        abort_unless(auth()->user()?->can('edit_properties'), 403, 'Unauthorized.');

        $image = PropertyImage::where('property_id', $this->property->id)->find($imageId);
        if ($image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            $wasPrimary = $image->is_primary;
            $image->delete();

            if ($wasPrimary) {
                $next = PropertyImage::where('property_id', $this->property->id)->orderBy('sort_order')->first();
                if ($next) {
                    $next->update(['is_primary' => true]);
                }
            }

            $this->loadExistingImages();
        }
    }

    /**
     * Set a specific existing image as primary.
     */
    public function setAsPrimary(int $imageId): void
    {
        if (!$this->isEditing || !$this->property) {
            return;
        }

        abort_unless(auth()->user()?->can('edit_properties'), 403, 'Unauthorized.');

        PropertyImage::where('property_id', $this->property->id)->update(['is_primary' => false]);
        PropertyImage::where('property_id', $this->property->id)->where('id', $imageId)->update(['is_primary' => true]);

        $this->loadExistingImages();
    }

    /**
     * Save property (create or update).
     */
    public function save()
    {
        if ($this->isEditing) {
            abort_unless(auth()->user()?->can('edit_properties'), 403, 'Unauthorized.');
        } else {
            abort_unless(auth()->user()?->can('create_properties'), 403, 'Unauthorized.');
        }

        $this->validate();

        $slugRule = $this->isEditing
            ? ValidationRule::unique('properties', 'slug')->ignore($this->property->id)
            : ValidationRule::unique('properties', 'slug');

        $this->validate([
            'slug' => ['required', 'string', 'max:255', $slugRule],
            'newImages.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'property_type_id' => $this->property_type_id,
            'property_status_id' => $this->property_status_id,
            'location_id' => $this->location_id,
            'agent_id' => $this->agent_id ?: null,
            'price' => $this->price,
            'price_label' => $this->price_label,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'sqft' => $this->sqft ?: null,
            'garage' => $this->garage ?: 0,
            'year_built' => $this->year_built ?: null,
            'is_featured' => (bool) $this->is_featured,
            'is_active' => (bool) $this->is_active,
        ];

        if ($this->isEditing) {
            $this->property->update($data);
            $property = $this->property;
            $message = "Property '{$property->title}' updated successfully.";

            ActivityLog::record("Updated property '{$property->title}'", 'Properties', $property->id);
        } else {
            $property = Property::create($data);
            $this->property = $property;
            $message = "Property '{$property->title}' created successfully.";

            ActivityLog::record("Created property '{$property->title}'", 'Properties', $property->id);
        }

        // Sync amenities
        $property->amenities()->sync($this->selectedAmenities);

        // Upload and link new images
        if (!empty($this->newImages)) {
            $existingCount = $property->images()->count();
            $hasExistingPrimary = $property->images()->where('is_primary', true)->exists();

            foreach ($this->newImages as $index => $file) {
                $path = $file->store('properties', 'public');
                $isPrimary = (!$hasExistingPrimary && $index === 0);

                $property->images()->create([
                    'image_path' => $path,
                    'sort_order' => $existingCount + $index,
                    'is_primary' => $isPrimary,
                ]);

                // Also support Spatie Media Library
                try {
                    if (method_exists($property, 'addMedia')) {
                        $fullPath = storage_path('app/public/' . $path);
                        if (file_exists($fullPath)) {
                            $property->addMedia($fullPath)
                                ->preservingOriginal()
                                ->toMediaCollection('properties');
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore media library configuration variance
                }
            }

            $this->newImages = [];
        }

        session()->flash('status', $message);

        if (Route::has('admin.properties.index')) {
            return redirect()->route('admin.properties.index');
        }

        return redirect()->route('admin.properties.edit', ['propertyId' => $property->hashid]);
    }

    public function render()
    {
        $requiredPermission = $this->isEditing ? 'edit_properties' : 'create_properties';
        abort_unless(auth()->user()?->can($requiredPermission), 403, 'Unauthorized.');

        return view('livewire.admin.manage-property-form', [
            'propertyTypes' => PropertyType::active()->orderBy('name')->get(),
            'propertyStatuses' => PropertyStatus::orderBy('name')->get(),
            'locations' => Location::active()->orderBy('name')->get(),
            'agents' => Agent::active()->orderBy('name')->get(),
            'amenitiesByCategory' => Amenity::active()->ordered()->get()->groupBy('category'),
        ]);
    }
}
