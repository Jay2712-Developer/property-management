<?php

namespace App\Livewire\Customer;

use App\Models\ContactInquiry;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Rule;
use Livewire\Component;

class NewsletterSubscribe extends Component
{
    #[Rule('required|email|max:255', as: 'email address')]
    public string $email = '';

    public bool $subscribed = false;

    public function subscribe(): void
    {
        $this->validate();

        $email = strtolower(trim($this->email));

        // 1. Save to newsletter_subscribers table (check for duplicates)
        if (class_exists(NewsletterSubscriber::class) && Schema::hasTable('newsletter_subscribers')) {
            try {
                NewsletterSubscriber::firstOrCreate(
                    ['email' => $email],
                    ['is_active' => true]
                );
            } catch (\Throwable $e) {
                // Ignore unique constraint conflict
            }
        }

        // 2. Also log as lead in contact_inquiries table if available
        if (class_exists(ContactInquiry::class) && Schema::hasTable('contact_inquiries')) {
            try {
                ContactInquiry::firstOrCreate(
                    ['email' => $email, 'subject' => 'VIP Newsletter Subscription'],
                    [
                        'name' => 'Newsletter Subscriber',
                        'phone' => null,
                        'message' => 'Client joined the TISHA VIP Newsletter and Market Insights list.',
                        'status' => 'new',
                    ]
                );
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        $this->subscribed = true;
        $this->reset('email');
    }

    public function render()
    {
        return view('livewire.customer.newsletter-subscribe');
    }
}
