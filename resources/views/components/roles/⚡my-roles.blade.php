<?php
use Livewire\Component;

new class extends Component
{
    public $roles;    

    public function mount(){
        $this->roles = auth()->user()->roles;
    }
};
?>


    <!-- roles table -->
    <div class="max-w-7xl mx-auto mt-20 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800">                    
                  <div class="w-full overflow-x-auto">
                        <table class="w-full border-collapse text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-medium">Name</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">   
                                @foreach($roles as $role)     
                                <tr class="hover:bg-gray-50/70 transition-colors dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300"><span class="px-2 py-0.5 rounded-full bg-{{$role['color']}}-50 dark:bg-{{$role['color']}}-900/30 text-{{$role['color']}}-600 dark:text-{{$role['color']}}-400 font-medium">{{$role['name']}}</span></td>
                                </tr>  
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
