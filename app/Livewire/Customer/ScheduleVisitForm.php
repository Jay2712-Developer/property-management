<?php

namespace App\Livewire\Customer;

use App\Models\Property;
use App\Models\VisitRequest;
use App\Traits\LogsActivity;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ScheduleVisitForm extends Component
{
    use LogsActivity;

    public int $propertyId;

    #[Rule('required|string|max:255', as: 'full name')]
    public string $name = '';

    #[Rule('required|email|max:255', as: 'email address')]
    public string $email = '';

    #[Rule('required|string|max:50', as: 'phone number')]
    public string $phone = '';

    #[Rule('required|date|after_or_equal:today', as: 'preferred visit date')]
    public string $visit_date = '';

    public bool $submitted = false;

    public function mount(int $propertyId): void
    {
        $this->propertyId = $propertyId;
        $this->visit_date = now()->addDays(1)->format('Y-m-d\TH:i');
    }

    public function submit(): void
    {
        $this->validate();

        $property = Property::find($this->propertyId);
        $agentId = $property?->agent_id;

        VisitRequest::create([
            'property_id' => $this->propertyId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'visit_date' => $this->visit_date,
            'status' => 'pending',
            'assigned_agent_id' => $agentId,
        ]);

        $this->logActivity(
            action: "Client '{$this->name}' scheduled private tour for property #{$this->propertyId}",
            module: 'Visits',
            recordId: $this->propertyId
        );

        $this->submitted = true;
        $this->reset(['name', 'email', 'phone']);
        $this->dispatch('visit-request-created');
    }

    public function resetForm(): void
    {
        $this->submitted = false;
        $this->visit_date = now()->addDays(1)->format('Y-m-d\TH:i');
    }

    public function render()
    {
        return view('livewire.customer.schedule-visit-form');
    }
}
