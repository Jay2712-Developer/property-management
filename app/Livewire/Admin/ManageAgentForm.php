<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Agent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('admin.layouts.app')]
#[Title('Manage Agent - TISHA Real Estate')]
class ManageAgentForm extends Component
{
    use WithFileUploads;

    public ?Agent $agent = null;
    public ?string $agentId = null;
    public bool $isEditing = false;

    #[Rule('required|string|min:2|max:100')]
    public string $name = '';

    #[Rule('nullable|string|max:100')]
    public ?string $designation = '';

    #[Rule('nullable|integer|min:0|max:80')]
    public $experience_years = 0;

    #[Rule('nullable|string|max:30')]
    public ?string $phone = '';

    #[Rule('required|email|max:150')]
    public string $email = '';

    #[Rule('nullable|string|max:2000')]
    public ?string $bio = '';

    #[Rule('nullable|image|max:3072')]
    public $photo = null;

    public ?string $existing_photo_path = null;

    // Social Links inputs
    public array $social_links = [
        'facebook' => '',
        'linkedin' => '',
        'twitter' => '',
        'instagram' => '',
        'whatsapp' => '',
    ];

    #[Rule('boolean')]
    public bool $is_active = true;

    public function mount(?string $agentId = null): void
    {
        if ($agentId) {
            $this->isEditing = true;
            $this->agentId = $agentId;

            abort_unless(
                auth()->user()?->can('edit_agents') || auth()->user()?->can('manage_agents'),
                403,
                'Unauthorized. You do not have permission to edit agents.'
            );

            $id = Agent::decodeHashid($agentId);
            $this->agent = Agent::findOrFail($id);

            $this->name = $this->agent->name;
            $this->designation = $this->agent->designation;
            $this->experience_years = (int) $this->agent->experience_years;
            $this->phone = $this->agent->phone;
            $this->email = $this->agent->email;
            $this->bio = $this->agent->bio;
            $this->existing_photo_path = $this->agent->photo_path;
            $this->is_active = (bool) $this->agent->is_active;

            if (is_array($this->agent->social_links)) {
                $this->social_links = array_merge($this->social_links, $this->agent->social_links);
            }
        } else {
            $this->isEditing = false;

            abort_unless(
                auth()->user()?->can('create_agents') || auth()->user()?->can('manage_agents'),
                403,
                'Unauthorized. You do not have permission to create agents.'
            );
        }
    }

    /**
     * Remove the newly selected photo.
     */
    public function removeSelectedPhoto(): void
    {
        $this->photo = null;
    }

    /**
     * Remove the currently saved photo from disk and database.
     */
    public function removeExistingPhoto(): void
    {
        if (!$this->isEditing || !$this->agent) {
            return;
        }

        abort_unless(
            auth()->user()?->can('edit_agents') || auth()->user()?->can('manage_agents'),
            403,
            'Unauthorized.'
        );

        if ($this->agent->photo_path && Storage::disk('public')->exists($this->agent->photo_path)) {
            Storage::disk('public')->delete($this->agent->photo_path);
        }

        $this->agent->update(['photo_path' => null]);
        $this->existing_photo_path = null;

        session()->flash('status', 'Agent profile photo removed.');
    }

    /**
     * Save agent (create or update).
     */
    public function save()
    {
        if ($this->isEditing) {
            abort_unless(
                auth()->user()?->can('edit_agents') || auth()->user()?->can('manage_agents'),
                403,
                'Unauthorized.'
            );
        } else {
            abort_unless(
                auth()->user()?->can('create_agents') || auth()->user()?->can('manage_agents'),
                403,
                'Unauthorized.'
            );
        }

        $this->validate();

        $emailRule = $this->isEditing
            ? ValidationRule::unique('agents', 'email')->ignore($this->agent->id)
            : ValidationRule::unique('agents', 'email');

        $this->validate([
            'email' => ['required', 'email', 'max:150', $emailRule],
            'social_links.*' => ['nullable', 'string', 'max:255'],
        ]);

        $data = [
            'name' => $this->name,
            'designation' => $this->designation ?: null,
            'experience_years' => (int) $this->experience_years,
            'phone' => $this->phone ?: null,
            'email' => $this->email,
            'bio' => $this->bio ?: null,
            'social_links' => array_filter($this->social_links),
            'is_active' => (bool) $this->is_active,
        ];

        // Process profile photo upload
        if ($this->photo) {
            if ($this->isEditing && $this->agent->photo_path && Storage::disk('public')->exists($this->agent->photo_path)) {
                Storage::disk('public')->delete($this->agent->photo_path);
            }

            $data['photo_path'] = $this->photo->store('agents', 'public');
        }

        if ($this->isEditing) {
            $this->agent->update($data);
            $message = "Agent '{$this->agent->name}' profile updated successfully.";

            ActivityLog::record("Updated agent profile '{$this->agent->name}'", 'Agents', $this->agent->id);
        } else {
            $agent = Agent::create($data);
            $message = "Agent '{$agent->name}' created successfully.";

            ActivityLog::record("Created new agent '{$agent->name}'", 'Agents', $agent->id);
        }

        session()->flash('status', $message);

        return redirect()->route('admin.agents.index');
    }

    public function render()
    {
        $required = $this->isEditing ? 'edit_agents' : 'create_agents';
        abort_unless(
            auth()->user()?->can($required) || auth()->user()?->can('manage_agents'),
            403,
            'Unauthorized.'
        );

        return view('livewire.admin.manage-agent-form');
    }
}
