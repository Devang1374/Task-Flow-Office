<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\User;

new class extends Component
{
    public $id;
    public $title;
    public $caption;
    public $task;
    public $categorys;
    public $selectedCategory;

    #[on('showUpdate')]
    public function mound(){
        $this->task = User::find(auth()->user()->id)->task()->where('id',$this->id)->first();
        $this->title = $this->task['title'];
        $this->caption = $this->task['caption'];
        $this->selectedCategory = $this->task['category'];
    }

    public function update(){
        User::find(auth()->user()->id)->task()->where('id',$this->id)->update([
            'title' => $this->title,
            'caption' => $this->caption,
            'category' => $this->selectedCategory,
        ]);

        $this->dispatch('task-updated');
        $this->dispatch('sendMessage',  msg: "Task Updated successfully!");
    }
};
?>

<div>
    <div class="py-6 absolute top-0 left-0 z-400 w-full h-full dark:bg-gray-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @session('message')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            {{$value}}
                        </div>
                    </div>
                    @endsession
                    
                    <form wire:submit.prevent="update" class="flex flex-col gap-4">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Update Task
                        </h2>
                        <x-input-label for="title" :value="__('title')" />
                        <x-text-input wire:model="title" id="title" class="block mt-1 w-full" type="text" name="title"  autofocus autocomplete="username" placeholder="Task Title" />
                        <x-input-error :messages="$errors->get('form.title')" class="mt-2" />

                        <x-input-label for="caption" :value="__('caption')" />
                        <x-text-input wire:model="caption" id="caption" class="block mt-1 w-full" type="text" name="caption"  autocomplete="username" placeholder="Task caption" />
                        <x-input-error :messages="$errors->get('form.caption')" class="mt-2" />

                        <x-input-label for="category" :value="__('category')" />
                        <select wire:model.live="selectedCategory" class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/30">
                            <option value="" disabled selected class="text-gray-400 dark:text-gray-500">Select a category...</option>
                            @foreach($categorys as $cat)
                                <option value="{{$cat['title']}}" class="text-gray-900 dark:bg-gray-800 dark:text-gray-200">{{$cat['title']}}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.category')" class="mt-2" />

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