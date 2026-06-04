<?php

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\roles;

new class extends Component
{
    public $name;
    public $selectedColor;
    public $colors = [
    'red',
    'orange',
    'amber',
    'yellow',
    'lime',
    'green',
    'emerald',
    'teal',
    'cyan',
    'sky',
    'blue',
    'indigo',
    'violet',
    'purple',
    'fuchsia',
    'pink',
    'rose'
];

    public function mount(){
        $this->selectedColor = '';
    }


    // save category in database
    public function save(){
        roles::create([
            'name' => $this->name,
            'color' => $this->selectedColor
        ]);

        $this->dispatch('roles-updated');
        $this->dispatch('show');
        $this->dispatch('sendMessage', msg: 'Role Added successfully!');
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
                            Add Category
                        </h2>
                        <x-input-label for="name" :value="__('name')" />
                        <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name"  autofocus autocomplete="username" placeholder="Role name" />
                        <x-input-error :messages="$errors->get('form.name')" class="mt-2" />

                        <x-input-label for="name" :value="__('Color Label')" />
                        <select wire:model.live="selectedColor" class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/30">
                            <option value="" disabled selected class="text-gray-400 dark:text-gray-500">Select a Color...</option>
                            @foreach($colors as $color)
                                <option value="{{$color}}" class="text-gray-900 dark:bg-gray-800 dark:text-gray-200">{{$color}}</option>
                            @endforeach
                        </select>

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