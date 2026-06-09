<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public $showForm = false;
    public $customers;

    public function mount(){
        $this->customers = auth()->user()->customer()->with('invoice')->get();
    }
    
    #[on('show')]
    public function show(){
        $this->showForm = $this->showForm ? false : true;
    }

    public function print($order_number, $customer_number){
        $this->redirectRoute('invoicePdf', ['order_id' => $order_number, 'customer_id' => $customer_number]);
    }
};
?>

<div>
    <div wire:key="category-create-container">
        @if($showForm)
            <livewire:invoice.create wire:key="invoice-create-component"/>
        @endif
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2 flex w-full justify-between text-gray-900 dark:text-gray-100">
                  <!-- search input -->
                  <x-text-input wire:model.live="search" placeholder="Search..." id="search"/>
                  <!-- add button -->
                  <x-primary-button wire:click="show">Generate Invoice</x-primary-button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="w-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800">                    
                  <div class="w-full overflow-x-auto">
                        <table class="w-full border-collapse text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-medium">User</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Invoice-number</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Order-number</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Created_at</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Due-Date</th>
                                    <th scope="col" class="px-6 py-4 font-medium">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800"> 
                                @foreach($customers as $customer)
                                @php
                                    $row = count($customer['invoice']);
                                    $i = 1;
                                @endphp
                                @if(isset($customer['invoice'][0]))
                                <tr class="hover:bg-gray-50/70 transition-colors dark:hover:bg-gray-700/30">
                                    <td rowspan="{{$row}}" class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$customer['name']}}</td>
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$customer['invoice'][0]['invoice_number']}}</td>
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$customer['invoice'][0]['order_number']}}</td>
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$customer['invoice'][0]['created_at']}}</td>
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$customer['invoice'][0]['due_date']}}</td>
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">
                                        <button wire:click="print({{$customer['invoice'][0]['order_number']}}, {{$customer['id']}})" class="font-medium text-blue-600 hover:underline dark:text-blue-400">Print</button>
                                    </td>
                                </tr>
                                @endif
                                @if($row > 0)
                                @for($i = 1; $i < $row; $i++)
                                <tr class="hover:bg-gray-50/70 transition-colors dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$customer['invoice'][$i]['invoice_number']}}</td>
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$customer['invoice'][$i]['order_number']}}</td>
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$customer['invoice'][$i]['created_at']}}</td>
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">{{$customer['invoice'][$i]['due_date']}}</td>
                                    <td class="px-6 py-4 font-normal text-gray-600 dark:text-gray-300">
                                        <button wire:click="print({{$customer['invoice'][$i]['order_number']}}, {{$customer['id']}})" class="font-medium text-blue-600 hover:underline dark:text-blue-400">Print</button>
                                    </td>
                                </tr>
                                @endfor
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>