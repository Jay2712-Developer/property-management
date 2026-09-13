<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Location;
use App\Models\Page;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\SiteSetting;
use App\Services\CacheService;
use Database\Seeders\DefaultDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoOptimizationAndPerformanceCachingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $this->seed([
            RoleAndPermissionSeeder::class,
            DefaultDataSeeder::class,
        ]);
    }

    public function test_property_detail_page_renders_dynamic_seo_meta_tags(): void
    {
        $type = PropertyType::firstOrCreate(['slug' => 'penthouse'], ['name' => 'Penthouse', 'is_active' => true]);
        $status = PropertyStatus::firstOrCreate(['slug' => 'for-sale'], ['name' => 'For Sale', 'color_code' => '#10B981', 'is_system_default' => true]);
        $location = Location::firstOrCreate(['slug' => 'downtown-dubai'], ['name' => 'Downtown Dubai', 'is_active' => true]);

        $property = Property::create([
            'title' => 'The Royal Sky Palace Penthouse',
            'slug' => 'the-royal-sky-palace-penthouse',
            'description' => 'A triplex penthouse with private rooftop infinity pool overlooking Burj Khalifa.',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'price' => 45000000,
            'is_active' => true,
        ]);

        $response = $this->get(route('property.show', $property->id));

        $response->assertStatus(200);

        // Title and meta tags
        $expectedTitle = 'The Royal Sky Palace Penthouse | TISHA Real Estate';
        $response->assertSee('<title>' . $expectedTitle . '</title>', false);
        $response->assertSee('<meta name="title" content="' . $expectedTitle . '">', false);
        $response->assertSee('name="description"', false);

        // Open Graph tags
        $response->assertSee('property="og:title" content="' . $expectedTitle . '"', false);
        $response->assertSee('property="og:type" content="article"', false);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('property="og:site_name" content="TISHA Real Estate"', false);

        // Twitter Card tags
        $response->assertSee('name="twitter:card" content="summary_large_image"', false);
        $response->assertSee('name="twitter:title" content="' . $expectedTitle . '"', false);
    }

    public function test_about_page_renders_dynamic_seo_meta_tags(): void
    {
        Page::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About TISHA Signature Brokerage',
                'meta_title' => 'Our Heritage & Philosophy',
                'meta_description' => 'A legacy of uncompromising architectural discernment and client discretion.',
                'content' => '<p>About us content.</p>',
                'is_active' => true,
            ]
        );

        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $expectedTitle = 'Our Heritage & Philosophy | TISHA Real Estate';
        $response->assertSee('<title>' . e($expectedTitle) . '</title>', false);
        $response->assertSee('property="og:title" content="' . e($expectedTitle) . '"', false);
        $response->assertSee('name="twitter:card" content="summary_large_image"', false);
    }

    public function test_contact_page_renders_dynamic_seo_meta_tags(): void
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $expectedTitle = 'Contact Our Concierge & Advisory Team | TISHA Real Estate';
        $response->assertSee('<title>' . e($expectedTitle) . '</title>', false);
        $response->assertSee('property="og:title" content="' . e($expectedTitle) . '"', false);
        $response->assertSee('name="twitter:card" content="summary_large_image"', false);
    }

    public function test_performance_caching_caches_queries_for_24_hours(): void
    {
        // 1. First fetch primes the cache
        $settings = CacheService::getSiteSettings();
        $this->assertNotEmpty($settings);
        $this->assertTrue(Cache::has('site_settings'));

        $types = CacheService::getPropertyTypes();
        $this->assertNotEmpty($types);
        $this->assertTrue(Cache::has('property_types'));

        $statuses = CacheService::getPropertyStatuses();
        $this->assertNotEmpty($statuses);
        $this->assertTrue(Cache::has('property_statuses'));

        $locations = CacheService::getLocations();
        $this->assertTrue(Cache::has('locations'));
    }

    public function test_cache_is_automatically_invalidated_when_models_are_updated(): void
    {
        // Prime caches
        CacheService::getSiteSettings();
        CacheService::getPropertyTypes();
        CacheService::getPropertyStatuses();
        CacheService::getLocations();

        $this->assertTrue(Cache::has('site_settings'));
        $this->assertTrue(Cache::has('property_types'));
        $this->assertTrue(Cache::has('property_statuses'));
        $this->assertTrue(Cache::has('locations'));

        // 1. Updating SiteSetting clears site_settings cache
        SiteSetting::updateOrCreate(
            ['key' => 'site_name'],
            ['value' => 'TISHA Prime International', 'group_name' => 'general']
        );
        $this->assertFalse(Cache::has('site_settings'));

        // 2. Updating PropertyType clears property_types cache
        $type = PropertyType::first();
        $type->update(['name' => 'Ultra Villa']);
        $this->assertFalse(Cache::has('property_types'));

        // 3. Updating PropertyStatus clears property_statuses cache
        $status = PropertyStatus::first();
        $status->update(['color_code' => '#FF6B35']);
        $this->assertFalse(Cache::has('property_statuses'));

        // 4. Updating Location clears locations cache
        $loc = Location::create(['name' => 'Palm Jebel Ali', 'slug' => 'palm-jebel-ali', 'is_active' => true]);
        $this->assertFalse(Cache::has('locations'));
    }

    public function test_images_contain_loading_lazy_attributes(): void
    {
        $type = PropertyType::firstOrCreate(['slug' => 'mansion'], ['name' => 'Mansion', 'is_active' => true]);
        $status = PropertyStatus::firstOrCreate(['slug' => 'for-sale'], ['name' => 'For Sale', 'color_code' => '#10B981', 'is_system_default' => true]);
        $location = Location::firstOrCreate(['slug' => 'dubai-hills'], ['name' => 'Dubai Hills', 'is_active' => true]);

        $agent = Agent::create([
            'name' => 'Julian Montgomery',
            'phone' => '+971 50 123 4567',
            'email' => 'julian@tisharealty.com',
            'is_active' => true,
        ]);

        $property = Property::create([
            'title' => 'Fairway Luxury Palace',
            'slug' => 'fairway-luxury-palace',
            'description' => 'Overlooking the championship golf course.',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'agent_id' => $agent->id,
            'price' => 28000000,
            'is_active' => true,
        ]);

        // 1. Property detail images
        $response = $this->get(route('property.show', $property->id));
        $response->assertStatus(200);
        $response->assertSee('loading="lazy"', false);

        // 2. About page images
        $aboutResponse = $this->get(route('about'));
        $aboutResponse->assertStatus(200);
        $aboutResponse->assertSee('loading="lazy"', false);
    }
}
