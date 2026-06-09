<?php

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\User;

new class extends Component
{
    public $name;
    public $address;
    public $number;
    public $email;

    // save customer in database
    public function save(){
        $validate = $this->validate([
            'name' => 'required|min:3',
            'address' => 'required|min:3',
            'number' => 'required|min:3',
            'email' => 'required|min:3',
        ]);

        User::find(auth()->user()->id)->customer()->create($validate);

        $this->dispatch('customer-updated');
        $this->dispatch('show');
        $this->dispatch('sendMessage', msg: 'customer Added successfully!');
    }
};
?>

<div>
    <div class="py-6 absolute top-0 left-0 z-400 w-full h-full dark:bg-gray-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form wire:submit.prevent="save" class="flex flex-col gap-4">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Add customer
                        </h2>
                        <x-input-label for="name" :value="__('name')" />
                        <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name"  autofocus autocomplete="username" placeholder="Customer Name" />
                        <x-input-error :messages="$errors->get('form.name')" class="mt-2" />
                        
                        <x-input-label for="address" :value="__('address')" />
                        <x-text-input wire:model="address" id="address" class="block mt-1 w-full" type="text" name="address"  autofocus autocomplete="username" placeholder="Coustomer Address" />
                        <x-input-error :messages="$errors->get('form.address')" class="mt-2" />
                        
                        <x-input-label for="number" :value="__('number')" />
                        <x-text-input wire:model="number" id="number" class="block mt-1 w-full" type="text" name="number"  autofocus autocomplete="username" placeholder="Coustomer Number" />
                        <x-input-error :messages="$errors->get('form.number')" class="mt-2" />

                        <x-input-label for="email" :value="__('email')" />
                        <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="text" name="email"  autocomplete="username" placeholder="Costomer Email" />
                        <x-input-error :messages="$errors->get('form.email')" class="mt-2" />

                        <div class="flex items-center justify-end mt-4">
                            <x-cancel-button wire:click="$dispatch('show')">
                                {{__('cancel')}}
                            </x-cancel-button>
                            <x-primary-button class="ms-3">
                                {{ __('save') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>