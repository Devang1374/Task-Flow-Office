<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\User;

new class extends Component
{

    
    public $name;
    public $address;
    public $number;
    public $email;

    public $customer;
    public $id;

    #[on('showUpdate')]
    public function mount(){
        $this->customer = User::find(auth()->user()->id)->customer()->where('id',$this->id)->first();
        $this->name = $this->customer['name'];
        $this->address = $this->customer['address'];
        $this->number = $this->customer['number'];
        $this->email = $this->customer['email'];
    }

    public function update(){
        // $this->validate();

        User::find(auth()->user()->id)->customer()->where('id',$this->id)->update([
            'name' => $this->name,
            'address' => $this->address,
            'number' => $this->number,
            'email' => $this->email
        ]);

        $this->dispatch('customer-updated');
        $this->dispatch('sendMessage', msg: 'Customer Updated Successfully!');
    }
};
?>

<div>
    <div class="py-6 absolute top-0 left-0 z-400 w-full h-full dark:bg-gray-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form wire:submit.prevent="update" class="flex flex-col gap-4">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Update Customer
                        </h2>
                        <x-input-label for="name" :value="__('name')" />
                        <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name"  autofocus autocomplete="username" placeholder="Customer Name" />
                        <x-input-error :messages="$errors->get('form.name')" class="mt-2" />
                        
                        <x-input-label for="address" :value="__('address')" />
                        <x-text-input wire:model="address" id="address" class="block mt-1 w-full" type="text" name="address"  autofocus autocomplete="username" placeholder="Customer Address" />
                        <x-input-error :messages="$errors->get('form.address')" class="mt-2" />
                        
                        <x-input-label for="number" :value="__('number')" />
                        <x-text-input wire:model="number" id="number" class="block mt-1 w-full" type="text" name="number"  autofocus autocomplete="username" placeholder="Customer Number" />
                        <x-input-error :messages="$errors->get('form.number')" class="mt-2" />

                        <x-input-label for="email" :value="__('email')" />
                        <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="text" name="email"  autocomplete="username" placeholder="Customer email" />
                        <x-input-error :messages="$errors->get('form.email')" class="mt-2" />

                        <div class="flex items-center justify-end mt-4">
                            <x-cancel-button wire:click="$dispatch('closeEdit')">
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