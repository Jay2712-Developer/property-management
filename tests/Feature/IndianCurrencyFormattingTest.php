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

    // ─── WhatsApp number formatting tests ─────────────────────────────────────

    public function test_whatsapp_number_strips_non_digits_and_adds_country_code(): void
    {
        // Bare 10-digit Indian number → prepend 91
        $this->assertSame('919876543210', formatWhatsAppNumber('9876543210'));

        // Number with dashes and spaces
        $this->assertSame('919876543210', formatWhatsAppNumber('98765-43210'));
        $this->assertSame('919876543210', formatWhatsAppNumber('98765 43210'));

        // Number with leading +91
        $this->assertSame('919876543210', formatWhatsAppNumber('+91 98765 43210'));

        // Number already prefixed with 91 (no double prefix)
        $this->assertSame('919876543210', formatWhatsAppNumber('919876543210'));

        // Brackets and dots
        $this->assertSame('919876543210', formatWhatsAppNumber('+91 (987) 654-3210'));
    }

    public function test_whatsapp_number_handles_empty_and_null(): void
    {
        $this->assertSame('', formatWhatsAppNumber(null));
        $this->assertSame('', formatWhatsAppNumber(''));
        $this->assertSame('', formatWhatsAppNumber('  '));
    }

    public function test_whatsapp_number_respects_custom_country_code(): void
    {
        // UAE country code 971
        $this->assertSame('9714567890', formatWhatsAppNumber('4567890', '971'));
        $this->assertSame('9714567890', formatWhatsAppNumber('9714567890', '971'));
    }
}
