<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Property extends Model implements HasMedia
{
    use HasFactory, HasEncryptedRouteKey, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'property_type_id',
        'property_status_id',
        'location_id',
        'agent_id',
        'price',
        'price_label',
        'bedrooms',
        'bathrooms',
        'sqft',
        'garage',
        'year_built',
        'is_featured',
        'is_active',
    ];

    protected $appends = [
        'hashid',
        'primary_image_url',
        'formatted_price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'sqft' => 'integer',
            'garage' => 'integer',
            'year_built' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationship: Property type.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    /**
     * Relationship: Property status (For Sale, For Rent, Sold, etc.).
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(PropertyStatus::class, 'property_status_id');
    }

    /**
     * Relationship: Property location.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relationship: Assigned real estate agent.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Relationship: All gallery images ordered by sort_order.
     */
    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    /**
     * Relationship: Primary featured image.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    /**
     * Accessor: Price formatted for Indian currency display (e.g. ₹2.45 Cr, ₹45.50 L, ₹85,000).
     */
    public function getFormattedPriceAttribute(): string
    {
        return formatIndianCurrency($this->price);
    }

    /**
     * Accessor: Primary image URL.
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        if (method_exists($this, 'getFirstMediaUrl')) {
            $mediaUrl = $this->getFirstMediaUrl('images');
            if ($mediaUrl) {
                return $mediaUrl;
            }

            $propertyMedia = $this->getFirstMediaUrl('properties');
            if ($propertyMedia) {
                return $propertyMedia;
            }
        }

        if ($this->relationLoaded('primaryImage') && $this->primaryImage?->image_path) {
            return asset('storage/' . $this->primaryImage->image_path);
        }

        $primary = $this->primaryImage;
        if ($primary?->image_path) {
            return asset('storage/' . $primary->image_path);
        }

        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            return asset('storage/' . $this->images->first()->image_path);
        }

        return null;
    }

    /**
     * Relationship: Amenities associated with this property.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity')->withTimestamps();
    }

    /**
     * Relationship: Booking and visit requests.
     */
    public function visitRequests(): HasMany
    {
        return $this->hasMany(VisitRequest::class);
    }

    /**
     * Scope: Active properties only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Featured properties.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Filter by property type ID.
     */
    public function scopeOfType(Builder $query, int $typeId): Builder
    {
        return $query->where('property_type_id', $typeId);
    }

    /**
     * Scope: Filter by property status ID.
     */
    public function scopeOfStatus(Builder $query, int $statusId): Builder
    {
        return $query->where('property_status_id', $statusId);
    }

    /**
     * Scope: Filter by location ID.
     */
    public function scopeInLocation(Builder $query, int $locationId): Builder
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Scope: Filter by price range.
     */
    public function scopePriceBetween(Builder $query, ?float $min = null, ?float $max = null): Builder
    {
        if (!is_null($min)) {
            $query->where('price', '>=', $min);
        }

        if (!is_null($max)) {
            $query->where('price', '<=', $max);
        }

        return $query;
    }
}
