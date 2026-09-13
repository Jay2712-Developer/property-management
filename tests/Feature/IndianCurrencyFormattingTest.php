<?php

namespace Tests\Feature;

use App\Models\Property;
use Tests\TestCase;

class IndianCurrencyFormattingTest extends TestCase
{
    // ─── Helper function unit tests ────────────────────────────────────────────

    public function test_formats_crores_correctly(): void
    {
        $this->assertSame('₹2.45 Cr', formatIndianCurrency(2_45_00_000));
        $this->assertSame('₹10.00 Cr', formatIndianCurrency(10_00_00_000));
        $this->assertSame('₹1.00 Cr', formatIndianCurrency(1_00_00_000));
    }

    public function test_formats_lakhs_correctly(): void
    {
        $this->assertSame('₹45.50 L', formatIndianCurrency(45_50_000));
        $this->assertSame('₹1.00 L', formatIndianCurrency(1_00_000));
        $this->assertSame('₹99.99 L', formatIndianCurrency(99_99_000));
    }

    public function test_formats_below_lakh_with_indian_commas(): void
    {
        $this->assertSame('₹85,000', formatIndianCurrency(85_000));
        $this->assertSame('₹1,000', formatIndianCurrency(1000));
        $this->assertSame('₹500', formatIndianCurrency(500));
    }

    public function test_handles_null_and_zero(): void
    {
        $this->assertSame('₹0', formatIndianCurrency(null));
        $this->assertSame('₹0', formatIndianCurrency(0));
    }

    public function test_boundary_exactly_one_crore(): void
    {
        $this->assertSame('₹1.00 Cr', formatIndianCurrency(1_00_00_000));
    }

    public function test_boundary_exactly_one_lakh(): void
    {
        $this->assertSame('₹1.00 L', formatIndianCurrency(1_00_000));
    }

    // ─── Model accessor test ───────────────────────────────────────────────────

    public function test_property_model_formatted_price_accessor(): void
    {
        // 2.45 Crores
        $property = new Property(['price' => 2_45_00_000]);
        $this->assertSame('₹2.45 Cr', $property->formatted_price);

        // 45.5 Lakhs
        $property2 = new Property(['price' => 45_50_000]);
        $this->assertSame('₹45.50 L', $property2->formatted_price);

        // Below 1 lakh
        $property3 = new Property(['price' => 85_000]);
        $this->assertSame('₹85,000', $property3->formatted_price);
    }

    public function test_formatted_price_is_in_appends(): void
    {
        $property = new Property(['price' => 5_00_00_000]);
        $array = $property->toArray();
        $this->assertArrayHasKey('formatted_price', $array);
        $this->assertSame('₹5.00 Cr', $array['formatted_price']);
    }
}
