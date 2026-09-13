<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\PropertyStatus;
use App\Models\PropertyType;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Property Types
        $propertyTypes = [
            ['name' => 'House', 'icon' => 'lucide:home'],
            ['name' => 'Apartment', 'icon' => 'lucide:building-2'],
            ['name' => 'Villa', 'icon' => 'lucide:castle'],
            ['name' => 'Townhouse', 'icon' => 'lucide:warehouse'],
            ['name' => 'Penthouse', 'icon' => 'lucide:hotel'],
            ['name' => 'Studio', 'icon' => 'lucide:door-open'],
        ];

        foreach ($propertyTypes as $type) {
            PropertyType::firstOrCreate(
                ['slug' => Str::slug($type['name'])],
                [
                    'name' => $type['name'],
                    'icon' => $type['icon'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Seed Property Statuses
        $propertyStatuses = [
            [
                'name' => 'For Sale',
                'color_code' => '#10B981', // Emerald green
                'is_system_default' => true,
            ],
            [
                'name' => 'For Rent',
                'color_code' => '#3B82F6', // Blue
                'is_system_default' => false,
            ],
            [
                'name' => 'Sold',
                'color_code' => '#EF4444', // Red
                'is_system_default' => false,
            ],
            [
                'name' => 'Rented',
                'color_code' => '#8B5CF6', // Purple
                'is_system_default' => false,
            ],
        ];

        foreach ($propertyStatuses as $status) {
            PropertyStatus::firstOrCreate(
                ['slug' => Str::slug($status['name'])],
                [
                    'name' => $status['name'],
                    'color_code' => $status['color_code'],
                    'is_system_default' => $status['is_system_default'],
                ]
            );
        }

        // 3. Seed Amenities grouped by category
        $amenities = [
            // Interior Amenities
            ['name' => 'Central Air Conditioning', 'icon' => 'lucide:fan', 'category' => 'Interior', 'sort_order' => 1],
            ['name' => 'Smart Home System', 'icon' => 'lucide:cpu', 'category' => 'Interior', 'sort_order' => 2],
            ['name' => 'Walk-in Closet', 'icon' => 'lucide:shirt', 'category' => 'Interior', 'sort_order' => 3],
            ['name' => 'Fitted Kitchen', 'icon' => 'lucide:utensils', 'category' => 'Interior', 'sort_order' => 4],
            ['name' => 'High-Speed Wi-Fi', 'icon' => 'lucide:wifi', 'category' => 'Interior', 'sort_order' => 5],

            // Exterior Amenities
            ['name' => 'Swimming Pool', 'icon' => 'lucide:waves', 'category' => 'Exterior', 'sort_order' => 6],
            ['name' => 'Private Garden', 'icon' => 'lucide:trees', 'category' => 'Exterior', 'sort_order' => 7],
            ['name' => 'Garage Parking', 'icon' => 'lucide:car', 'category' => 'Exterior', 'sort_order' => 8],
            ['name' => 'Balcony / Terrace', 'icon' => 'lucide:sun', 'category' => 'Exterior', 'sort_order' => 9],
            ['name' => 'Barbecue Area', 'icon' => 'lucide:flame', 'category' => 'Exterior', 'sort_order' => 10],

            // Community & Security Amenities
            ['name' => '24/7 Security & CCTV', 'icon' => 'lucide:shield-check', 'category' => 'Community', 'sort_order' => 11],
            ['name' => 'Fitness Center / Gym', 'icon' => 'lucide:dumbbell', 'category' => 'Community', 'sort_order' => 12],
            ['name' => 'Children Play Area', 'icon' => 'lucide:smile', 'category' => 'Community', 'sort_order' => 13],
            ['name' => 'Elevator Access', 'icon' => 'lucide:arrow-up-down', 'category' => 'Community', 'sort_order' => 14],
        ];

        foreach ($amenities as $amenity) {
            Amenity::firstOrCreate(
                ['name' => $amenity['name']],
                [
                    'icon' => $amenity['icon'],
                    'category' => $amenity['category'],
                    'sort_order' => $amenity['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Default Site Settings
        $siteSettings = [
            ['key' => 'site_name', 'value' => 'TISHA Real Estate', 'group_name' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Discover Luxury Living & Premium Properties', 'group_name' => 'general'],
            ['key' => 'contact_email', 'value' => 'info@tishaproperty.com', 'group_name' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+1 (555) 234-5678', 'group_name' => 'contact'],
            ['key' => 'currency_symbol', 'value' => '$', 'group_name' => 'property'],
        ];

        foreach ($siteSettings as $setting) {
            SiteSetting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group_name' => $setting['group_name'],
                ]
            );
        }
    }
}
