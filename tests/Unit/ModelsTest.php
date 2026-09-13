<?php

namespace Tests\Unit;

use App\Models\ActivityLog;
use App\Models\Agent;
use App\Models\Amenity;
use App\Models\ContactInquiry;
use App\Models\Location;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\VisitRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_models_relationships_and_hashid(): void
    {
        // 1. Property Type
        $type = PropertyType::create([
            'name' => 'Villa',
            'slug' => 'villa',
            'icon' => 'home',
            'is_active' => true,
        ]);
        $this->assertNotEmpty($type->hashid);
        $this->assertEquals($type->id, (new PropertyType)->resolveRouteBinding($type->hashid)->id);

        // 2. Property Status
        $status = PropertyStatus::create([
            'name' => 'For Sale',
            'slug' => 'for-sale',
            'color_code' => '#10B981',
            'is_system_default' => true,
        ]);
        $this->assertNotEmpty($status->hashid);

        // 3. Location (Hierarchy)
        $parentLoc = Location::create([
            'name' => 'Dubai',
            'slug' => 'dubai',
            'is_active' => true,
        ]);
        $subLoc = Location::create([
            'name' => 'Downtown Dubai',
            'slug' => 'downtown-dubai',
            'parent_id' => $parentLoc->id,
            'is_active' => true,
        ]);
        $this->assertEquals($parentLoc->id, $subLoc->parent->id);
        $this->assertTrue($parentLoc->children->contains($subLoc));

        // 4. Amenity
        $amenity = Amenity::create([
            'name' => 'Private Pool',
            'category' => 'Exterior',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $this->assertNotEmpty($amenity->hashid);

        // 5. Agent
        $agent = Agent::create([
            'name' => 'John Doe',
            'designation' => 'Luxury Property Specialist',
            'email' => 'john@tisha.com',
            'social_links' => ['linkedin' => 'https://linkedin.com/in/johndoe'],
            'is_active' => true,
        ]);
        $this->assertIsArray($agent->social_links);
        $this->assertEquals('https://linkedin.com/in/johndoe', $agent->social_links['linkedin']);

        // 6. Property
        $property = Property::create([
            'title' => 'Stunning Sea View Villa',
            'slug' => 'stunning-sea-view-villa',
            'property_type_id' => $type->id,
            'property_status_id' => $status->id,
            'location_id' => $subLoc->id,
            'agent_id' => $agent->id,
            'price' => 2500000.00,
            'price_label' => 'Total',
            'bedrooms' => 5,
            'bathrooms' => 6,
            'sqft' => 6500,
            'garage' => 2,
            'is_featured' => true,
            'is_active' => true,
        ]);
        $this->assertNotEmpty($property->hashid);
        $this->assertEquals($property->id, (new Property)->resolveRouteBinding($property->hashid)->id);
        $this->assertEquals($type->id, $property->type->id);
        $this->assertEquals($status->id, $property->status->id);
        $this->assertEquals($subLoc->id, $property->location->id);
        $this->assertEquals($agent->id, $property->agent->id);

        // Attach Amenity
        $property->amenities()->attach($amenity->id);
        $this->assertTrue($property->amenities->contains($amenity));

        // 7. Property Image
        $image = PropertyImage::create([
            'property_id' => $property->id,
            'image_path' => 'properties/villa-front.jpg',
            'sort_order' => 1,
            'is_primary' => true,
        ]);
        $this->assertEquals($property->id, $image->property->id);
        $this->assertEquals($image->id, $property->primaryImage->id);

        // 8. Testimonial
        $testimonial = Testimonial::create([
            'client_name' => 'Alice Smith',
            'rating' => 5,
            'message' => 'TISHA helped us find our dream villa!',
            'is_active' => true,
        ]);
        $this->assertNotEmpty($testimonial->hashid);

        // 9. Contact Inquiry
        $inquiry = ContactInquiry::create([
            'name' => 'Robert Paul',
            'email' => 'robert@example.com',
            'message' => 'Interested in villa pricing.',
            'status' => 'new',
        ]);
        $this->assertCount(1, ContactInquiry::new()->get());

        // 10. Newsletter Subscriber
        $sub = NewsletterSubscriber::create([
            'email' => 'subscriber@test.com',
            'is_active' => true,
        ]);
        $this->assertNotEmpty($sub->hashid);

        // 11. Visit Request
        $visit = VisitRequest::create([
            'property_id' => $property->id,
            'name' => 'Jane Client',
            'email' => 'jane@example.com',
            'phone' => '+1234567890',
            'visit_date' => now()->addDays(2),
            'status' => 'pending',
            'assigned_agent_id' => $agent->id,
        ]);
        $this->assertEquals($property->id, $visit->property->id);
        $this->assertEquals($agent->id, $visit->agent->id);

        // 12. Site Setting
        SiteSetting::setValue('site_title', 'TISHA Real Estate');
        $this->assertEquals('TISHA Real Estate', SiteSetting::getValue('site_title'));

        // 13. Page
        $page = Page::create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => 'We are leaders in premium luxury properties.',
            'is_active' => true,
        ]);
        $this->assertNotEmpty($page->hashid);

        // 14. Activity Log & User relationship
        $user = User::factory()->create();
        $log = ActivityLog::record('create', 'Property', $property->id, $user->id);
        $this->assertEquals($user->id, $log->user->id);
        $this->assertTrue($user->activityLogs->contains($log));
    }
}
