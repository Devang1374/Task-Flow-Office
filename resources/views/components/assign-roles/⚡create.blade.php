<?php

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\User;
use App\Models\Roles;
use App\Models\roles_user;

new class extends Component
{
    public $users;
    public $roles;
    public $selectedUser;
    public $selectedRole;

    public function mount(){
        $this->users = User::get();
        $this->roles = roles::get();
        $this->selectedUser = '';
        $this->selectedRole = '';
    }

    public function assign(){
        if($this->selectedUser == '' || $this->selectedRole == ""){
            return $this->dispatch("sendMessage", msg:'Please select User and Role');
        }        

        roles_user::create([
            'user_id' => $this->selectedUser,
            'roles_id' => $this->selectedRole,
        ]);

        $this->selectedRole = '';
        $this->selectedUser = '';

        $this->dispatch('roleUpdated');
        $this->dispatch('sendMessage', msg: 'Role Assigned');
    }
};
?>

<div class="mt-10">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Assign Role To User
                </h2>
                <div class="w-full mt-5 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800">                    
                  <div class="w-full overflow-x-auto">
            <form wire:submit.prevent="assign" class="flex gap-4 items-center">
                <select wire:model.live="selectedUser" class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/30">
                    <option value="" disabled selected class="text-gray-400 dark:text-gray-500">Select a User...</option>
                    @foreach($users as $user)
                        <option value="{{$user['id']}}" class="text-gray-900 dark:bg-gray-800 dark:text-gray-200">{{$user['name']}}</option>
                    @endforeach
                </select>

                <select wire:model.live="selectedRole" class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/30">
                    <option value="" disabled selected class="text-gray-400 dark:text-gray-500">Select a Role...</option>
                    @foreach($roles as $role)
                        <option value="{{$role['id']}}" class="text-gray-900 dark:bg-gray-800 dark:text-gray-200">{{$role['name']}}</option>
                    @endforeach
                </select>

                
                <x-primary-button class="ms-3">
                    {{ __('Assign') }}
                </x-primary-button>
            
            </form>
        </div>
        </div>
    </div>
        </div>
    </div>
</div>