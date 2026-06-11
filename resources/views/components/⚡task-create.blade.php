<?php

use Livewire\Component;
use App\Models\task;

new class extends Component
{
    public $user_id;
    public $title;
    public $caption;
    public $categorys;
    public $selectedCategory;

    public function mount(){
        $this->user_id = auth()->user()->id;
        $this->selectedCategory = '';
    }

    public function save(){
        if($this->title == '' || $this->caption == ''){
            return session()->flash('message','All feildes are required');
        }

        task::create([
            'user_id' => $this->user_id,
            'title' => $this->title,
            'caption' => $this->caption,
            'category' => $this->selectedCategory,
            'isActive' => true
        ]);

        $this->dispatch('sendMessage', msg: 'Task Saved Successfully!');
        $this->dispatch('show');
        $this->dispatch('task-updated');
        
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
                            Add Task
                        </h2>
                        <x-input-label for="title" :value="__('title')" />
                        <x-text-input wire:model.live="title" id="title" class="block mt-1 w-full" type="text" name="title"  autofocus autocomplete="username" placeholder="Task Title" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />

                        <x-input-label for="caption" :value="__('caption')" />
                        <x-text-input wire:model.live="caption" id="caption" class="block mt-1 w-full" type="text" name="caption"  autocomplete="username" placeholder="Task caption" />
                        <x-input-error :messages="$errors->get('caption')" class="mt-2" />
                        
                        <x-input-label for="category" :value="__('category')" />
                        <select wire:model.live="selectedCategory" class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/30">
                            <option value="" disabled selected class="text-gray-400 dark:text-gray-500">Select a category...</option>
                            @foreach($categorys as $cat)
                                <option value="{{$cat['title']}}" class="text-gray-900 dark:bg-gray-800 dark:text-gray-200">{{$cat['title']}}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />


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