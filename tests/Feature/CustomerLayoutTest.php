<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class CustomerLayoutTest extends TestCase
{
    use RefreshDatabase;
    public function test_customer_layout_contains_tisha_branding_and_tailwind_palette(): void
    {
        $rendered = Blade::render(
            '@extends("layouts.customer")
             @section("content")
                 <div id="test-content">Welcome to TISHA Real Estate</div>
             @endsection'
        );

        // Verify Brand & Colors
        $this->assertStringContainsString('#FF6B35', $rendered);
        $this->assertStringContainsString('#1A1A1A', $rendered);
        $this->assertStringContainsString('#0F0F0F', $rendered);
        $this->assertStringContainsString('tisha:', $rendered);

        // Verify Dark Mode Strategy
        $this->assertStringContainsString("x-data=\"{ \n        darkMode: localStorage.getItem('darkMode') === 'true' \n    }\"", $rendered);
        $this->assertStringContainsString(":class=\"darkMode ? 'dark' : ''\"", $rendered);
        $this->assertStringContainsString('bg-gray-50 text-gray-800 dark:bg-[#0F0F0F] dark:text-gray-200', $rendered);

        // Verify Assets: Inter font & Font Awesome 6
        $this->assertStringContainsString('family=Inter', $rendered);
        $this->assertStringContainsString('font-awesome/6', $rendered);

        // Verify Structure: Navbar, Content, Footer
        $this->assertStringContainsString('Schedule Tour', $rendered);
        $this->assertStringContainsString('Welcome to TISHA Real Estate', $rendered);
        $this->assertStringContainsString('TISHA Luxury Real Estate', $rendered);

        // Verify Animations: scroll-smooth & animate-fade-up
        $this->assertStringContainsString('scroll-smooth', $rendered);
        $this->assertStringContainsString('animate-fade-up', $rendered);
    }

    public function test_customer_layout_renders_via_component_syntax(): void
    {
        $rendered = Blade::render(
            '<x-layouts.customer title="Exclusive Villas">
                 <p>Explore luxury residences</p>
             </x-layouts.customer>'
        );

        $this->assertStringContainsString('Exclusive Villas', $rendered);
        $this->assertStringContainsString('Explore luxury residences', $rendered);
        $this->assertStringContainsString('#FF6B35', $rendered);
    }
}
