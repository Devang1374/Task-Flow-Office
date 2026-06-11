<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TaskImport;

//view to pdf
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component
{
  use WithFileUploads;
  use WithPagination;
    public $user_id;
    public $tamp;
    public $task_id;

    public $tasks;

    //function that sets deffulte values
    #[on('task-updated')]
    public function mount(){
        $this->tasks = auth()->user()->task;
        $this->categorys = auth()->user()->category;
        $this->category = '';
        $this->user_id = auth()->user()->id;
        $this->isEditing = false;
        $this->showDownload = false;
        $this->isImport = false;
    }

    //function that shows add task form
    //varibale
    public $showForm = false;
    #[on('show')]
    public function show(){
        if(!$this->categorys->isNotEmpty()){
            return $this->dispatch('sendMessage', msg: 'You Have To add Atlest One Category Before adding task');
        }

        $this->showForm = $this->showForm ? false : true;
    }

    //function that handels the search input and returns search result
    //varibales
    public $search;
    public function updatedSearch(){
        $this->tasks = auth()->user()->task()->where(function ($query){
            $query->where('title','like',"%{$this->search}%")
                  ->orWhere('caption','like',"%{$this->search}%")
                  ->orWhere('isActive','like',"%{$this->search}%");
        })->where('category','like',"%{$this->category}%")->get();
    }

    // function that removes task from database
    public function remove($id){
        auth()->user()->task()->where('id',$id)->where('user_id', $this->user_id)->delete();

        $this->message = "Task Deleted Successfully!";
        $this->dispatch('task-updated');
    }

    // function that handles active and compelete status of task
    public function update($id){
        $isActive = auth()->user()->task()->where('id', $id)->value('isActive');

        if($isActive == 0){
            $newValue =  true;
        }else{
            $newValue =  false;
        }
        auth()->user()->task()->where('id',$id)->update([
            'isActive'  =>  $newValue
        ]);

        $this->dispatch('task-updated');
    }

    // function that handles the notification message
    //varibales
    public string $message = '';
    #[On('sendMessage')]
    public function handleMessage($msg)
    {
        $this->message = $msg;
    }

    //function that fetches category from database
    //varibales
    public $category = '';
    public $categorys;
    public function updatedCategory(){
        $this->tasks = auth()->user()->task()->where('category','like',"%{$this->category}%")->get();
    }

    // finding caption for category tool tip
    public function findCaption($title){
        foreach($this->categorys as $cat){
            if($cat['title'] == $title){
              return $cat['caption'];
            }
        }
    }

    //this function opens an edit form and sends edit id to to edit component
    public $isEditing = false;
    public function edit($id){
      $this->task_id = $id;
      $this->isEditing = $this->isEditing ? false : true;
      $this->dispatch('showUpdate');
    }
      
    //this function closes the edit form
    #[on('closeEdit')]
    public function closeEditing(){
      $this->isEditing = $this->isEditing ? false : true;
    }

    // download system
    //varibales
    public $showDownload;
    //open download links
    #[On('showDownload')]
    public function showDownloadLinks(){
      $this->showDownload = $this->showDownload ? false : true;
    }

    //download pdf
    public function pdf(){
      $this->redirectRoute('pdf');
    }

    //download csv
    public function csv(){
      $this->redirectRoute('csv');
    }

    //download xlsx
    public function xlsx(){
      $this->redirectRoute('xlsx');
    }

    public $csv_file;
    public $isImport;
    public function import(){
        $this->validate([
          'csv_file' => 'required'
        ]);

        Excel::import(
          new TaskImport,
          $this->csv_file
        );

        $this->message = "File Imported successfully";
        $this->reset('csv_file');
        $this->dispatch('task-updated');
    }

    public function isImportBtn(){
        $this->isImport = $this->isImport ? false : true;
    }
};
?>

<div>
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

    <!-- create task -->
    <div wire:key="task-create-container">
        @if($showForm)
            <livewire:task-create :categorys="$categorys" wire:key="task-create-component"/>
        @endif
    </div>

  <div wire:key="download-links-container">
    @if($showDownload)
    <div wire:key="download-links" class="absolute flex items-center justify-center bg-blue-50 w-full h-full top-0 left-0 z-111">
      <div class="flex flex-col gap-5">
          <x-primary-button class="dark:bg-red-700 dark:text-white dark:hover:bg-red-300 dark:hover:text-black" title="Download Task in PDF Formate" wire:click="pdf">PDF</x-primary-button>
          <x-primary-button class="dark:bg-violet-700 dark:text-white dark:hover:bg-red-300 dark:hover:text-black" title="Download Task in CSV formate" wire:click="csv">CSV</x-primary-button>
          <x-primary-button class="dark:bg-green-700 dark:text-white dark:hover:bg-green-500 dark:hover:text-black" title="Download Task in Excel formate" wire:click="xlsx">EXCEL</x-primary-button>
          <x-primary-button class="dark:bg-red-500 dark:text-white dark:hover:bg-red-300 dark:hover:text-black" title="Download Task in Excel formate" wire:click="$dispatch('showDownload')">Cancel</x-primary-button>
      </div>
    </div>
    @endif
      @if($isImport)
      <div wire:key="download-links" class="absolute flex items-center justify-center bg-blue-50 w-full h-full top-0 left-0 z-111">
        <div class="flex flex-col gap-5">
          <input type="file" class="border-2 p-2 border-solid border-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" wire:model="csv_file">
          <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
          <x-primary-button wire:click="import">import</x-primary-button>
          <x-primary-button wire:click="isImportBtn">Cancel</x-primary-button>
        </div>
      </div>
      @endif
    </div>
    <!-- add button and search input -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2 flex w-full justify-between text-gray-900 dark:text-gray-100">
                  <!-- search input -->
                  <x-text-input wire:model.live="search" title="Search Task Using Title And Caption You can input 0 for Complete task and 1 for Active Task" placeholder="Search..." id="search"/>
                  
                  <!-- category selector -->
                  <div class="relative w-full max-w-xs">
                    <!-- Visual Indicator Arrow -->
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>

                    <!-- The Styled Select Tag -->
                    <select 
                        wire:model.live="category"
                        title="select category to filter" class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/30"
                    >
                        <option value="" selected class="text-gray-400 dark:text-gray-500">Select a category...</option>
                        @foreach($categorys as $cat)
                        <option value="{{$cat['title']}}" class="text-gray-900 dark:bg-gray-800 dark:text-gray-200">{{$cat['title']}}</option>
                        @endforeach
                    </select>
                  </div>

                  <!-- add button -->
                  <div>
                    <x-primary-button title="Add New Task" wire:click="showDownloadLinks">Download</x-primary-button>
                    <x-primary-button title="Add New Task" wire:click="show">ADD</x-primary-button>
                    <x-primary-button wire:click="isImportBtn">Import</x-primary-button>
                  </div>
                </div>
            </div>
        </div>
    </div>

    <!-- task table -->
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800">                    
                  <div class="w-full overflow-x-auto">
                        <table class="w-full border-collapse text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-medium">Title</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Caption</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Category</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Status</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Remove</th>
                                    <th scope="col" class="px-6 py-4 font-medium text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">   
                                @foreach($tasks as $task)     
                                <tr class="hover:bg-gray-50/70 transition-colors dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$task['title']}}</td>
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$task['caption']}}</td>
                                    <td title="{{$this->findCaption($task['category'])}}" class="cursor-pointer px-6 py-4 font-normal text-gray-600 dark:text-gray-300">
                                      <span class="px-2 py-0.5 rounded-full bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 font-medium">  
                                        {{$task['category']}}
                                      </span>
                                    </td>
                                    <td class="px-6 py-4 cursor-pointer" title="Click To toggle Active or Complete" wire:click="update({{$task['id']}})">
                                      @if($task['isActive'])
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 dark:bg-green-950/50 dark:text-green-400">
                                          <span class="h-1.5 w-1.5 rounded-full bg-green-600 dark:bg-green-400"></span>
                                          Active
                                        </span>
                                      @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                          <span class="h-1.5 w-1.5 rounded-full bg-gray-400 dark:bg-gray-500"></span>
                                          Complete
                                        </span>
                                      @endif
                                    </td>
                                    <td class="px-6 py-4" title="Click To Delete Task">
                                      <button 
                                        type="button" wire:click="remove({{$task['id']}})"
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
                                        <button title="Click To Open Edit Form" wire:click="edit({{$task['id']}})" class="font-medium text-blue-600 hover:underline dark:text-blue-400">Edit</button>
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
    <div wire:key="task-create-container">
      @if($isEditing)
        <livewire:task-edit :categorys="$categorys" :id="$task_id"/>
      @endif
    </div>
</div>