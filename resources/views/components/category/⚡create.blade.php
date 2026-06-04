<?php

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\User;

new class extends Component
{
    public $title;
    public $caption;

    // save category in database
    public function save(){
        $validate = $this->validate([
            'title' => 'required|min:3',
            'caption' => 'required|min:3'
        ]);

        User::find(auth()->user()->id)->category()->create($validate);

        $this->dispatch('category-updated');
        $this->dispatch('show');
        $this->dispatch('sendMessage', msg: 'Category Added successfully!');
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
                        <x-input-label for="title" :value="__('title')" />
                        <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" name="title"  autofocus autocomplete="username" placeholder="Task Title" />
                        <x-input-error :messages="$errors->get('form.title')" class="mt-2" />

                        <x-input-label for="caption" :value="__('caption')" />
                        <x-text-input wire:model="caption" id="caption" class="block mt-1 w-full" type="text" name="caption"  autocomplete="username" placeholder="Task caption" />
                        <x-input-error :messages="$errors->get('form.caption')" class="mt-2" />

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