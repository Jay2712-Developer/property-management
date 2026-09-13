<?php

namespace App\Livewire\Customer;

use App\Models\ContactInquiry;
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

        if (class_exists(ContactInquiry::class)) {
            try {
                ContactInquiry::create([
                    'name' => 'Newsletter Subscriber',
                    'email' => $this->email,
                    'phone' => null,
                    'subject' => 'VIP Newsletter Subscription',
                    'message' => 'Requested subscription to TISHA luxury portfolio and market insights newsletter.',
                    'status' => 'new',
                ]);
            } catch (\Throwable $e) {
                // Silently fallback if table doesn't exist
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
