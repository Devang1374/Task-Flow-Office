<?php

use Livewire\Attributes\On;
use App\Models\roles;
use Livewire\Component;

new class extends Component
{
    public $search;
    public $showForm = false;
    public $roles;
    public $role_id;
    public $message;
    public $delete_id;
    public $isEditing;

    #[on('roles-updated')]
    public function mount(){
        $this->roles = roles::get();
        $this->delete_id = '';
        $this->isEditing = false;
    }

    public function edit($id){
      $this->role_id = $id;
      $this->isEditing = $this->isEditing ? false : true;
    }

    #[on('show')]
    public function show(){
        $this->showForm = $this->showForm ? false : true;
    }

    #[on('sendMessage')]
    public function sendMessage($msg){
        $this->message = $msg;
    }

    #[on('closeEdit')]
    public function closeEditing(){
      $this->isEditing = $this->isEditing ? false : true;
    }

    public function updatedSearch(){
        $this->roles = roles::where('name','like',"%{$this->search}%")->get();
    }

    public function delete($id){
        $this->delete_id = $id;
    }
};
?>

<div>
    <!-- show flash message -->
    @if($message)
        <div>
          <!-- Main Position Container (Fixed to bottom-right corner) -->
          <div id="livewire-toast-message" class="fixed bottom-5 right-5 z-50 w-full max-w-sm">

            <!-- Inner Component Box Layout -->
            <div 
              wire:poll.5s="$set('message', '')"
              class="relative flex items-start gap-3 overflow-hidden rounded-xl border border-gray-200 bg-white p-4 shadow-xl transition-all duration-300 dark:border-gray-700 dark:bg-gray-800"
            >
              <!-- Success Status Icon -->
              <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
              </div>

              <!-- Message Content -->
              <div class="flex-1 min-w-0">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Notification</h4>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 break-words">
                  {{ $message }}
                </p>
              </div>

              <!-- "X" Close Button -->
              <button 
                type="button" 
                wire:click="$set('message', '')"
                aria-label="Close notification"
                class="shrink-0 rounded-lg p-1 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-gray-300"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>

              <!-- 5-Second Progress Bar Footer -->
              <div class="absolute bottom-0 left-0 h-1 w-full bg-gray-100 dark:bg-gray-700/50">
                <div 
                  class="h-full bg-green-500" 
                  style="animation: toast-progress 5s linear forwards;"
                ></div>
              </div>
            </div>
          </div>

          <!-- CSS Animation Injection Block -->
          <style>
            @keyframes toast-progress {
              from { width: 100%; }
              to { width: 0%; }
            }
          </style>
        </div>
    @endif

    <!-- delete roles -->
    @if($delete_id)
        <livewire:roles.delete :id="$delete_id"/>
    @endif

    <!-- create roles -->
    <div wire:key="roles-create-container">
        @if($showForm)
            <livewire:roles.create wire:key="roles-create-component"/>
        @endif
    </div>

    <!-- search and add button -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2 flex w-full justify-between text-gray-900 dark:text-gray-100">
                  <!-- search input -->
                  <x-text-input wire:model.live="search" placeholder="Search..." id="search"/>
                  <!-- add button -->
                  <x-primary-button wire:click="show">ADD</x-primary-button>
                </div>
            </div>
        </div>
    </div>

    <!-- roles table -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800">                    
                  <div class="w-full overflow-x-auto">
                        <table class="w-full border-collapse text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-medium">Name</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Remove</th>
                                    <th scope="col" class="px-6 py-4 font-medium text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">   
                                @foreach($roles as $role)     
                                <tr class="hover:bg-gray-50/70 transition-colors dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">
                                      <span class="px-2 py-0.5 rounded-full bg-{{$role['color']}}-50 dark:bg-{{$role['color']}}-900/30 text-{{$role['color']}}-600 dark:text-{{$role['color']}}-400 font-medium">
                                        {{$role['name']}}
                                      </span>
                                    </td>
                                    <td class="px-6 py-4">
                                      <button 
                                        type="button" wire:click="delete({{$role['id']}})"
                                        aria-label="Remove item"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-sm font-medium text-red-600 transition-colors duration-150 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
                                      >
                                        <!-- Trash Icon -->
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                        Remove
                                      </button>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="edit({{$role['id']}})" class="font-medium text-blue-600 hover:underline dark:text-blue-400">Edit</button>
                                    </td>
                                </tr>  
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- edit form -->
    <div wire:key="roles-update-container">
      @if($isEditing)
        <livewire:roles.update :id="$role_id"/>
      @endif
    </div>
</div>