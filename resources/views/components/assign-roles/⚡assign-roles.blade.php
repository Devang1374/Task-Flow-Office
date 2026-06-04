<?php

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public $message;
    public $search;
    public $users;
    public $user_id;
    public $role_id;


    #[on('role-updated')]
    public function mount(){
      $this->users = User::with('roles')->withCount('roles')->get();
      $this->role_id = '';
      $this->user_id = '';
    }

    public function updatedSearch(){
      return $this->users = User::where('name','like',"%{$this->search}%")->with('roles')->withCount('roles')->get();
    }

    #[on('sendMessage')]
    public function sendMessage($msg){
        $this->message = $msg;
    }

    public function delete($role_id, $user_id){
        $this->role_id = $role_id;
        $this->user_id = $user_id;
        $this->dispatch('sendMessage',msg: $role_id);
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

    @if($role_id)
        <livewire:assign-roles.delete :role_id="$role_id" :user_id="$user_id"/>
    @endif

    <livewire:assign-roles.create/>

    <div wire:key="main-container" class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800">                    
                  <div class="w-full overflow-x-auto">
                        <x-text-input wire:model.live="search" placeholder="Search..." id="search"/>
                        <table class="w-full border-collapse text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-medium">Users</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Total Roles</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Roles</th>
                                </tr>
                            </thead>

                            <tbody wire:key="tbody" class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">   
                                @foreach($users as $user)     
                                <tr wire:key="user-container-{{$user['id']}}" class="hover:bg-gray-50/70 transition-colors dark:hover:bg-gray-700/30">
                                    <td wire:key="user-{{$user['id']}}" class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$user['name']}}</td>
                                    <td wire:key="role-counte-{{$user['id']}}" class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$user->roles_count}}</td>
                                    <td wire:key="role-container-{{$user['id']}}" class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">
                                        @foreach($user->roles as $role)
                                        <div wire:key="role-{{$role['id']}}" class="flex flex-row items-center">
                                            <div class="grow"><span class="px-2 py-0.5 rounded-full bg-{{$role['color']}}-50 dark:bg-{{$role['color']}}-900/30 text-{{$role['color']}}-600 dark:text-{{$role['color']}}-400 font-medium">
                                                {{$role['name']}}
                                            </span>
                                            </div>
                                            <div class="grow text-center">
                                                created-at:{{$role->pivot->created_at}}
                                            </div>
                                            <div>
                                            <button 
                                                type="button" wire:click="delete({{$role['id']}}, {{$user['id']}})"
                                                aria-label="Remove item"
                                                class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-sm font-medium text-red-600 transition-colors duration-150 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
                                              >
                                                <!-- Trash Icon -->
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                                Remove
                                            </button>
                                            </div>
                                        </div>
                                        @endforeach
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
</div>