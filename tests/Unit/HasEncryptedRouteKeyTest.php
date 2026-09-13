<?php

namespace Tests\Unit;

use App\Facades\HashidsHelper;
use App\Models\Agent;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\User;
use App\Services\HashidsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HasEncryptedRouteKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_hashids_service_and_facade(): void
    {
        $service = app(HashidsService::class);
        $encoded = $service->encode(42);

        $this->assertIsString($encoded);
        $this->assertGreaterThanOrEqual(8, strlen($encoded));

        $decoded = $service->decode($encoded);
        $this->assertEquals(42, $decoded);

        // Via Facade
        $facadeEncoded = HashidsHelper::encode(99);
        $this->assertEquals(99, HashidsHelper::decode($facadeEncoded));
    }

    public function test_property_encrypted_route_key_and_model_binding(): void
    {
        $type = PropertyType::create(['name' => 'Villa', 'slug' => 'villa', 'is_active' => true]);
        $status = PropertyStatus::create(['name' => 'For Sale', 'slug' => 'for-sale', 'is_system_default' => true]);
        $location = Location::create(['name' => 'Downtown', 'slug' => 'downtown', 'is_active' => true]);

        $property = Property::create([
            'title' => 'Luxury Skyline Penthouse',
            'slug' => 'luxury-skyline-penthouse',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $location->id,
            'price' => 1200000.00,
            'is_active' => true,
        ]);

        // 1. Verify getRouteKeyName returns 'hashid'
        $this->assertEquals('hashid', $property->getRouteKeyName());

        // 2. Verify getRouteKey returns the encoded Hashid (min 8 chars)
        $routeKey = $property->getRouteKey();
        $this->assertIsString($routeKey);
        $this->assertGreaterThanOrEqual(8, strlen($routeKey));
        $this->assertEquals($property->hashid, $routeKey);

        // 3. Test URL generation using route helper
        //    Dynamically register a test-only route and bind it immediately.
        Route::middleware(\Illuminate\Routing\Middleware\SubstituteBindings::class)
            ->get('/test/properties/{property}', function (Property $property) {
                return response()->json(['id' => $property->id, 'title' => $property->title]);
            })
            ->name('test.properties.show');

        app()->make(\Illuminate\Routing\Router::class)->getRoutes()->refreshNameLookups();
        app()->make(\Illuminate\Routing\Router::class)->getRoutes()->refreshActionLookups();

        $url = route('test.properties.show', $property);
        $this->assertStringContainsString('/test/properties/' . $property->hashid, $url);

        // 4. Test Route Model Binding via HTTP GET request
        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertJson(['id' => $property->id, 'title' => 'Luxury Skyline Penthouse']);

        // 5. Test slug fallback
        $slugUrl = '/test/properties/' . $property->slug;
        $slugResponse = $this->get($slugUrl);
        $slugResponse->assertStatus(200);
        $slugResponse->assertJson(['id' => $property->id]);
    }

    public function test_user_and_agent_use_has_encrypted_route_key(): void
    {
        $user = User::factory()->create();
        $this->assertEquals('hashid', $user->getRouteKeyName());
        $this->assertGreaterThanOrEqual(8, strlen($user->getRouteKey()));

        $agent = Agent::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@tisha.com',
            'is_active' => true,
        ]);
        $this->assertEquals('hashid', $agent->getRouteKeyName());
        $this->assertGreaterThanOrEqual(8, strlen($agent->getRouteKey()));
    }

    public function test_secure_session_and_cookie_configuration(): void
    {
        $this->assertTrue(config('session.secure'));
        $this->assertTrue(config('session.http_only'));
        $this->assertEquals('lax', config('session.same_site'));
    }
}
