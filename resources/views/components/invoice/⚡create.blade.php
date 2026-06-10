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

    //company Details
    public $companyName;
    public $companyAddress;
    public $companyNumber;
    public $companyEmail;
    public $companyTerms;
    
    //coustmer Details
    public $customerName;
    public $customerAddress;
    public $customerNumber;
    public $customerEmail;

    public function mount(){
        $this->customers = auth()->user()->customer;
        $this->selectedCustomer = '';
        $this->addProductRow();

        //fetching company details in input
        $phone = auth()->user()->phone()->first();
        $this->companyName = $phone['company_name']; 
        $this->companyAddress = $phone['address']; 
        $this->companyNumber = $phone['number']; 
        $this->companyEmail = $phone['email']; 
        $this->companyTerms = $phone['terms']; 

    }

    public function updatedselectedCustomer(){
        $customers = auth()->user()->customer()->where('id',$this->selectedCustomer)->first();

         //fetching customer details in input
        $this->customerName = $customers['name']; 
        $this->customerAddress = $customers['address']; 
        $this->customerNumber = $customers['number']; 
        $this->customerEmail = $customers['email']; 
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
            'company_name' => $this->companyName,
            'company_email' => $this->companyEmail,
            'company_number' => $this->companyNumber,
            'company_address' => $this->companyAddress,
            'terms' => $this->companyTerms,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'customer_number' => $this->customerNumber,
            'customer_address' => $this->customerAddress,
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
                <div class="p-6 bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-600">
                    <form wire:submit.prevent="save" class="flex flex-col gap-4">
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Add Category
                        </h2>
                        
                        <select wire:model.live="selectedCustomer" class="w-full appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2.5 pr-10 text-sm font-medium text-gray-700 shadow-sm transition duration-150 ease-in-out hover:border-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/30">
                            <option value="" disabled selected class="bg-white text-gray-400 dark:text-gray-500">Select a customer...</option>
                            @foreach($customers as $customer)
                                <option value="{{$customer['id']}}" class="bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-200">{{$customer['name']}}</option>
                            @endforeach
                        </select>

                            <x-input-label for="due-date" :value="__('Due-date:')" />
                            <input type="date" wire:model="dueDate" id="due-date" placeholder="Due Date" class="flex w-full items-center px-4 py-2 bg-white-800 text-gray dark:bg-gray-200 rounded-md font-semibold text-xs dark:bg-gray-800 dark:text-gray-200 uppercase tracking-widest dark:hover:bg-gray-700 focus:bg-white-200 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" />
                            <x-input-error :messages="$errors->get('dueDate')" class="mt-2" />

                            <div class="flex justify-between items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50" wire:key="user-company-details">
                                <div class="logo">
                                    <img class="w-30" src="{{asset('images/taskFlow-logo.png')}}" alt="TaskFlow">
                                </div>
                                <div>
                                    <h1 class="text-gray-800 text-xl dark:text-gray-200">Invoice</h1>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-1 lg:grid-cols-2">
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50" wire:key="user-company-details">
                                    <div>
                                        <x-input-label for="Company-name" :value="__('From:')" />
                                        <x-text-input wire:model="companyName" id="Company-name" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Company name" />
                                        <x-input-error :messages="$errors->get('form.companyName')" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <x-text-input wire:model="companyAddress" id="Company-address" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Company Address..." />
                                        <x-input-error :messages="$errors->get('form.companyAddress')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-number-input wire:model="companyNumber" id="Company-number" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Company Number..." />
                                        <x-input-error :messages="$errors->get('form.companyNumber')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-text-input wire:model="companyEmail" id="Company-email" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Company@Email..." />
                                        <x-input-error :messages="$errors->get('form.companyEmail')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50" wire:key="costumer-details">
                                    <div>
                                        <x-input-label for="Customer-name" :value="__('To:')" />
                                        <x-text-input wire:model="customerName" id="Customer-name" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Customer name" />
                                        <x-input-error :messages="$errors->get('form.customerName')" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <x-text-input wire:model="customerAddress" id="Customer-address" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Customer Address..." />
                                        <x-input-error :messages="$errors->get('form.customerAddress')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-number-input wire:model="customerNumber" id="Customer-number" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Customer Number..." />
                                        <x-input-error :messages="$errors->get('form.customerNumber')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-text-input wire:model="customerEmail" id="Customer-email" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Customer@Email..." />
                                        <x-input-error :messages="$errors->get('form.customerEmail')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                        @foreach($products as $index => $product)
                            <div class="grid grid-cols-6 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-4 items-end p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50" wire:key="product-row-{{ $index }}">
                                
                                <div class="sm:col-span-2 lg:col-span-1 mt-2 lg:mt-0">
                                    @if(count($products) > 1)
                                        <button type="button" wire:click="removeProductRow({{ $index }})" class="w-full bg-red-500 text-white px-3 py-2.5 rounded-md shadow-sm hover:bg-red-600 transition focus:ring-2 focus:ring-red-500/50 outline-none">
                                            Remove
                                        </button>
                                    @endif
                                </div>

                                <div>
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
                                    <x-number-input value="{{$products[$index]['total']}}" id="product-total-{{ $index }}" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="0.00" readonly />
                                    <x-input-error :messages="$errors->get('form.product.'.$index.'.total')" class="mt-2" />
                                </div>
                            </div>
                        @endforeach

                        <div class="w-full flex justify-end">
                            <div class="w-1/3 grid grid-cols-2 gap-4 sm:grid-cols-1 lg:grid-cols-2">
                                @php
                                    $grandTotal = 0;
                                @endphp
                                @foreach($products as $product)
                                <x-text-input value="Sub-Total" id="product-sub-total" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="Sub Total" readonly />
                                <x-number-input value="{{$product['total']}}" id="product-sub-total" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="0.00" readonly />
                                    @php
                                        $grandTotal += $product['total'];
                                    @endphp
                                @endforeach
                                    <x-text-input value="Total" id="product-sub-total" class="bg-gray-200 block mt-1 w-full" type="number" autocomplete="off" placeholder="Total" readonly />
                                    <x-number-input value="{{$grandTotal}}" id="product-sub-total" class="block mt-1 w-full bg-gray-200" type="number" autocomplete="off" placeholder="0.00" readonly />
                            </div>
                        </div>

                        <div>
                            <x-text-input wire:model="companyTerms" id="Company-terms" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Company Terms..." />
                            <x-input-error :messages="$errors->get('form.companyTerms')" class="mt-2" />
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between gap-4 mt-2">
                            <button type="button" wire:click="addProductRow" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded-md shadow-sm hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500/50 outline-none">
                                + Add Product
                            </button>

                            <div>
                            <button type="reset" wire:click="$dispatch('show')" class="w-full sm:w-auto bg-red-600 text-white px-6 py-2.5 rounded-md shadow-sm hover:bg-red-700 transition focus:ring-2 focus:ring-red-500/50 outline-none">
                                Cancel
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