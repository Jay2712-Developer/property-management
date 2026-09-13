<?php

namespace App\Livewire\Customer;

use App\Models\ContactInquiry;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ContactForm extends Component
{
    use LogsActivity;

    #[Rule('required|string|max:255', as: 'full name')]
    public string $name = '';

    #[Rule('required|email|max:255', as: 'email address')]
    public string $email = '';

    #[Rule('required|string|max:50', as: 'phone number')]
    public string $phone = '';

    #[Rule('required|string|max:255', as: 'inquiry subject')]
    public string $subject = 'General Inquiry';

    #[Rule('required|string|min:10|max:3000', as: 'message')]
    public string $message = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $throttleKey = 'contact-inquiry:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Too many inquiry submissions. Please wait {$seconds} seconds before trying again.");
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $this->validate();

        if (class_exists(ContactInquiry::class)) {
            $inquiry = ContactInquiry::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'subject' => $this->subject,
                'message' => $this->message,
                'status' => 'new',
            ]);

            $this->logActivity(
                action: "Client '{$this->name}' submitted inquiry: '{$this->subject}'",
                module: 'Inquiries',
                recordId: $inquiry->id
            );
        }

        $this->submitted = true;
        $this->reset(['name', 'email', 'phone', 'message']);
        $this->subject = 'General Inquiry';
    }

    public function resetForm(): void
    {
        $this->submitted = false;
        $this->subject = 'General Inquiry';
    }

    public function render()
    {
        return view('livewire.customer.contact-form');
    }
}
