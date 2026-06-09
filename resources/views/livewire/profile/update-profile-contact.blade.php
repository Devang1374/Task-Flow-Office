<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;


new class extends Component
{
    public $phone;
    public $company_name = '';
    public $number = '';
    public $address = '';
    public $terms = '';

    public function mount(): void
    {
        $this->phone = User::find(auth()->user()->id)->phone;
        $this->number = $this->phone['number'];
        $this->company_name = $this->phone['company_name'];
        $this->address = $this->phone['address'];
        $this->terms = $this->phone['terms'];
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateContactInformation(): void
    {
        $validated = $this->validate([ 
            'company_name' => 'required',
            'number' => 'required|numeric|max_digits:13',
            'address' => 'required',
            'terms' => 'required',
        ]);

        $user = User::find(auth()->user()->id);

        $user->phone()->update($validated);

        $this->status = 'Number Updated Successfully!'; 
        $this->dispatch('profile-updated');
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Contact Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your Contact information") }}
        </p>
    </header>

    <form wire:submit="updateContactInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="company_name" :value="__('Company Name')" />
            <x-text-input wire:model="company_name" id="company_name" name="company_name" type="text" class="mt-1 block w-full" required autofocus autocomplete="company_name" />
            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
            
            <x-input-label for="number" :value="__('Phone Number')" />
            <x-text-input wire:model="number" id="number" name="number" type="text" class="mt-1 block w-full" required autofocus autocomplete="number" />
            <x-input-error class="mt-2" :messages="$errors->get('number')" />
            
            <x-input-label for="address" :value="__('Company Address')" />
            <x-text-input wire:model="address" id="address" name="address" type="text" class="mt-1 block w-full" required autofocus autocomplete="address" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
            
            <x-input-label for="terms" :value="__('Company Terms')" />
            <x-text-input wire:model="terms" id="terms" name="terms" type="text" class="mt-1 block w-full" required autofocus autocomplete="terms" />
            <x-input-error class="mt-2" :messages="$errors->get('terms')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
