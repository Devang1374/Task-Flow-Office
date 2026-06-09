<?php

use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

use App\Models\product;

new class extends Component
{
    public $customers;
    public $selectedCustomer;
    public array $products = [];
    public $tamp;
    public $dueDate;

    public function mount(){
        $this->customers = auth()->user()->customer;
        $this->selectedCustomer = '';
        $this->addProductRow();
    }

    public function addProductRow()
    {
        $this->products[] = [
            'name' => '',
            'quantity' => 1,
            'price' => 0,
            'tax' => 0,
            'total' => 0,
        ];
    }

    public function save($print = false){
        $datePrefix = Carbon::today()->format('Ymd'); 
        $randomString = strtoupper(Str::random(4));
        $dueDate = Carbon::now()->addDays(10);

        $newOrder = 1;

        $lastOrder = auth()->user()->invoice()->latest()->first();

        if($this->dueDate){
           $dueDate = $this->dueDate; 
        }

        if($lastOrder){
            $newOrder = $lastOrder['order_number'] + 1;
        }

        auth()->user()->invoice()->create([
            'customer_id' => $this->selectedCustomer,
            'invoice_number' => "INV-{$datePrefix}-{$randomString}",
            'order_number' => $newOrder,
            'due_date' => $dueDate,
        ]);

        foreach($this->products as $product)
        {
            product::create([
                'customer_id' => $this->selectedCustomer,
                'order_number' => $newOrder,
                'name' => $product['name'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'tax' => $product['tax']
            ]);
        }

        $this->redirectRoute('invoicePdf', ['order_id' => $newOrder, 'customer_id' => $this->selectedCustomer]);
        $this->dispatch('show');
        
    }

    public function removeProductRow($index)
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products);
    }
};
?>

<div>
    <div class="py-6 fixed top-0 left-0 z-50 w-full h-full overflow-y-auto dark:bg-gray-900/80 bg-gray-200/80 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="h-full dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form wire:submit.prevent="save" class="flex flex-col gap-4">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Add Category {{$selectedCustomer}}
                        </h2>
                        {{$tamp}}
                        <select wire:model.live="selectedCustomer" class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/30">
                            <option value="" disabled selected class="text-gray-400 dark:text-gray-500">Select a customer...</option>
                            @foreach($customers as $customer)
                                <option value="{{$customer['id']}}" class="text-gray-900 dark:bg-gray-800 dark:text-gray-200">{{$customer['name']}}</option>
                            @endforeach
                        </select>

                            <x-input-label for="due-date" :value="__('Due-date:')" />
                            <input type="date" wire:model="dueDate" id="due-date" placeholder="Due Date" class="flex w-full items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" />
                            <x-input-error :messages="$errors->get('dueDate')" class="mt-2" />

                        @foreach($products as $index => $product)
                            <div class="grid grid-cols-6 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-4 items-end p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50" wire:key="product-row-{{ $index }}">
                                
                                <div class="">
                                    <x-input-label for="product-name-{{ $index }}" :value="__('Product-Name:')" />
                                    <x-text-input wire:model="products.{{ $index }}.name" id="product-name-{{ $index }}" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Product name" />
                                    <x-input-error :messages="$errors->get('form.product.'.$index.'.name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="product-quantity-{{ $index }}" :value="__('Product-Quantity:')" />
                                    <x-number-input wire:model.live="products.{{ $index }}.quantity" id="product-quantity-{{ $index }}" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="0" />
                                    <x-input-error :messages="$errors->get('form.product.'.$index.'.quantity')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="product-price-{{ $index }}" :value="__('Product-Price:')" />
                                    <x-number-input wire:model.live="products.{{ $index }}.price" id="product-price-{{ $index }}" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="0.00" />
                                    <x-input-error :messages="$errors->get('form.product.'.$index.'.price')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="product-tax-{{ $index }}" :value="__('Product-Tax:')" />
                                    <x-number-input wire:model.live="products.{{ $index }}.tax" id="product-tax-{{ $index }}" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="0.00" />
                                    <x-input-error :messages="$errors->get('form.product.'.$index.'.tax')" class="mt-2" />
                                </div>

                                <div>
                                    @php
                                        if(empty($products[$index]['price'])){
                                            $products[$index]['total'] = 0;
                                        } 
                                        else{
                                            $products[$index]['total'] = $products[$index]['price'] * $products[$index]['quantity']; 
                                            if(!empty($products[$index]['tax']) && $products[$index]['tax'] > 0){
                                                $products[$index]['total'] += $products[$index]['total'] * ($products[$index]['tax'] / 100);
                                            }
                                        }
                                    @endphp
                                    <x-input-label for="product-Total-{{ $index }}" :value="__('Product-Sub-Total:')" />
                                    <x-number-input wire:model.live="{{$products[$index]['total']}}" value="{{$products[$index]['total']}}" id="product-total-{{ $index }}" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="0.00" readonly />
                                    <x-input-error :messages="$errors->get('form.product.'.$index.'.total')" class="mt-2" />
                                </div>


                                <div class="sm:col-span-2 lg:col-span-1 mt-2 lg:mt-0">
                                    @if(count($products) > 1)
                                        <button type="button" wire:click="removeProductRow({{ $index }})" class="w-full bg-red-500 text-white px-3 py-2.5 rounded-md shadow-sm hover:bg-red-600 transition focus:ring-2 focus:ring-red-500/50 outline-none">
                                            Remove
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div>
                            @php
                                $grandTotal = 0;
                            @endphp
                            @foreach($products as $product)
                                @php
                                    $grandTotal += $product['total'];
                                @endphp
                            @endforeach

                            {{$grandTotal}}
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between gap-4 mt-2">
                            <button type="button" wire:click="addProductRow" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded-md shadow-sm hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500/50 outline-none">
                                + Add Product
                            </button>

                            <div>
                            <button type="reset" wire:click="$dispatch('show')" class="w-full sm:w-auto bg-red-600 text-white px-6 py-2.5 rounded-md shadow-sm hover:bg-red-700 transition focus:ring-2 focus:ring-red-500/50 outline-none">
                                Cansel
                            </button>
                            <button type="submit" class="w-full sm:w-auto bg-green-600 text-white px-6 py-2.5 rounded-md shadow-sm hover:bg-green-700 transition focus:ring-2 focus:ring-green-500/50 outline-none">
                                Save & Print
                            </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>