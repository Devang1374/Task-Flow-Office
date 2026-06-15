<?php

use Illuminate\Support\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

use Livewire\Attributes\Validate;

use App\Models\product;

new class extends Component
{
    public $customers;
    public $selectedCustomer;
    public array $tamp = [];
    public $dueDate;
    
    public $tampInvoice;

    #[Validate([
        'products.*.name' => 'required',
    ], message:'Please Enter Product Name Before Saving')]
    public array $products = [];

    public $message = "";

    //edit variable
    public $order_id;
    public $customer_id;

    //company Details
    #[Validate('required|min:3')] 
    public $companyName;
    public $companyAddress;
    public $companyNumber;
    public $companyEmail;

    #[Validate('required|min:3')]
    public $companyTerms;
    
    //coustmer Details
    #[Validate('required|min:3')]
    public $customerName;
    public $customerAddress;
    public $customerNumber;
    public $customerEmail;

    public function mount(){
        $this->isEdit = false;
        $this->customers = auth()->user()->customer;
        $this->selectedCustomer = '';
        if(!$this->order_id){
            $this->addProductRow();
            $this->customerName = ''; 
            $this->customerAddress = ''; 
            $this->customerNumber = ''; 
            $this->customerEmail = ''; 
            $this->selectedCustomer = '';
            $this->dueDate = '';
        }

        //fetching company details in input
        $phone = auth()->user()->phone()->first();
        $this->companyName = $phone['company_name']; 
        $this->companyAddress = $phone['address']; 
        $this->companyNumber = $phone['number']; 
        $this->companyEmail = $phone['email']; 
        $this->companyTerms = $phone['terms']; 

        if($this->order_id){
            $this->selectedCustomer = $this->customer_id;
            
            $invoice = auth()->user()->customer()->find($this->customer_id)->invoice()->where('order_number', $this->order_id)->first();
            
            $this->tampInvoice = $invoice;
            

            $this->companyName = $invoice['company_name']; 
            $this->companyAddress = $invoice['company_address']; 
            $this->companyNumber = $invoice['company_number']; 
            $this->companyEmail = $invoice['company_email']; 
            $this->companyTerms = $invoice['terms'];

            $this->customerName = $invoice['customer_name']; 
            $this->customerAddress = $invoice['customer_address']; 
            $this->customerNumber = $invoice['customer_number']; 
            $this->customerEmail = $invoice['customer_email']; 

            $this->dueDate = $invoice['due_date'];

            $products = auth()->user()->customer()->find($this->customer_id)->product()->where('order_number', $this->order_id)->get();

            foreach($products as $product){
                $this->products[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'tax' => $product['tax'],
                    'total' => 0,
                ];
            }
        }
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

    #[on('save')]
    public function save($print = true){
        
        if(empty($this->selectedCustomer)){
            return $this->message = "Please Select Customer Before Saving";
        }
        
        $this->validate();

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

        if($this->order_id){
            $newOrder = $this->order_id;
            auth()->user()->invoice()->where('order_number', $this->order_id)->update([
            'customer_id' => $this->selectedCustomer,
            'due_date' => $dueDate,
            //company details
            'company_name' => $this->companyName,
            'company_email' => $this->companyEmail,
            'company_number' => $this->companyNumber,
            'company_address' => $this->companyAddress,
            'terms' => $this->companyTerms,
            //cutomer details
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'customer_number' => $this->customerNumber,
            'customer_address' => $this->customerAddress,
        ]);

        if(!empty($this->tamp)){
            foreach($this->tamp as $t)
            {
                auth()->user()->customer()->find($this->customer_id)->product()->where('id', $t['id'])->delete();
            }
        }

        foreach($this->products as $product)
        {
            if(isset($product['id'])){
                product::where('id', $product['id'])->update([
                    'customer_id' => $this->selectedCustomer,
                    'order_number' => $newOrder,
                    'name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'tax' => $product['tax']
                ]);
            }else{
                product::create([
                'customer_id' => $this->selectedCustomer,
                'order_number' => $newOrder,
                'name' => $product['name'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'tax' => $product['tax']
            ]);
            }
            $this->dispatch('sendMessage', msg:'Invoice with Order Number '.$newOrder.' is Updated');
        }
        }else{
        auth()->user()->invoice()->create([
            'customer_id' => $this->selectedCustomer,
            'invoice_number' => "INV-{$datePrefix}-{$randomString}",
            'order_number' => $newOrder,
            'due_date' => $dueDate,
            //company details
            'company_name' => $this->companyName,
            'company_email' => $this->companyEmail,
            'company_number' => $this->companyNumber,
            'company_address' => $this->companyAddress,
            'terms' => $this->companyTerms,
            //cutomer details
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
        $this->dispatch('sendMessage', msg:'Invoice Created successfully and Order Number is '.$newOrder);
        }

        if($print)
            $this->redirectRoute('invoicePdf', ['order_id' => $newOrder, 'customer_id' => $this->selectedCustomer]);
        else{
            $this->dispatch('show');
        }
    }

    public function removeProductRow($index)
    {
        if(isset($this->products[$index]['id'])){
            $product = auth()->user()->customer()->find($this->customer_id)->product()->where('id', $this->products[$index]['id'])->first();
            $this->tamp[] = [
                'id' => $product['id'],
            ];
        }

        unset($this->products[$index]);
        $this->products = array_values($this->products);
    }

    public function cancel(){
        $this->dispatch('invoice-updated');
    }
};
?>

<div>
    @if($message)
        <div>
          <!-- Main Position Container (Fixed to bottom-right corner) -->
          <div id="livewire-toast-message" class="fixed bottom-5 right-5 z-520 w-full max-w-sm">

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
                                        <x-text-input required wire:model="companyName" id="Company-name" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Company name" />
                                        <x-input-error :messages="$errors->get('companyName')" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <x-text-input wire:model="companyAddress" id="Company-address" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Company Address..." />
                                        <x-input-error :messages="$errors->get('companyAddress')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-number-input wire:model="companyNumber" id="Company-number" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Company Number..." />
                                        <x-input-error :messages="$errors->get('companyNumber')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-text-input wire:model="companyEmail" id="Company-email" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Company@Email..." />
                                        <x-input-error :messages="$errors->get('companyEmail')" class="mt-2" />
                                    </div>
                                </div>

                                @if($tampInvoice)
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50" wire:key="user-company-details">
                                    <x-input-label for="Company-name" :value="__('Invoice Details:')" />
                                    <div class="grid grid-cols-2 gap-4">
                                        <x-text-input value="Invoice Number" class="bg-gray-200 block mt-1 w-full" readonly />
                                        <x-text-input class="block mt-1 w-full" type="text" readonly value="{{$tampInvoice['invoice_number']}}"  />
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <x-text-input value="Order Number" class="bg-gray-200 block mt-1 w-full" readonly />
                                        <x-text-input class="block mt-1 w-full" type="text" readonly value="{{$tampInvoice['order_number']}}"  />
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <x-text-input value="Invoice Date" class="bg-gray-200 block mt-1 w-full" readonly />
                                        <x-text-input class="block mt-1 w-full" type="text" readonly value="{{$tampInvoice['created_at']}}"  />
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <x-text-input value="Due Date" class="bg-gray-200 block mt-1 w-full" readonly />
                                        <x-text-input class="block mt-1 w-full" type="text" readonly value="{{$tampInvoice['due_date']}}"  />
                                    </div>
                                </div>
                                @endif

                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50" wire:key="costumer-details">
                                    <div>
                                        <x-input-label for="Customer-name" :value="__('To:')" />
                                        <x-text-input required wire:model="customerName" id="Customer-name" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Customer name" />
                                        <x-input-error :messages="$errors->get('customerName')" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <x-text-input wire:model="customerAddress" id="Customer-address" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Customer Address..." />
                                        <x-input-error :messages="$errors->get('customerAddress')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-number-input wire:model="customerNumber" id="Customer-number" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Customer Number..." />
                                        <x-input-error :messages="$errors->get('customerNumber')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-text-input wire:model="customerEmail" id="Customer-email" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Customer@Email..." />
                                        <x-input-error :messages="$errors->get('customerEmail')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                        @foreach($products as $index => $product)
                            <div class="grid grid-cols-6 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-4 items-end p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50" wire:key="product-row-{{ $index }}">

                                <div class="grow-1">
                                    <x-input-label for="product-name-{{ $index }}" :value="__('Product-Name:')" />
                                    <x-text-input wire:model="products.{{ $index }}.name" id="product-name-{{ $index }}" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Product name" />
                                    <x-input-error :messages="$errors->get('products.'.$index.'.name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="product-quantity-{{ $index }}" :value="__('Product-Quantity:')" />
                                    <x-number-input wire:model.live="products.{{ $index }}.quantity" id="product-quantity-{{ $index }}" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="0" />
                                    <x-input-error :messages="$errors->get('product.'.$index.'.quantity')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="product-price-{{ $index }}" :value="__('Product-Price:')" />
                                    <x-number-input wire:model.live="products.{{ $index }}.price" id="product-price-{{ $index }}" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="0.00" />
                                    <x-input-error :messages="$errors->get('product.'.$index.'.price')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="product-tax-{{ $index }}" :value="__('Product-Tax:')" />
                                    <x-number-input wire:model.live="products.{{ $index }}.tax" id="product-tax-{{ $index }}" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="0.00" />
                                    <x-input-error :messages="$errors->get('product.'.$index.'.tax')" class="mt-2" />
                                </div>

                                <div>
                                    @php
                                        if(empty($products[$index]['price']) || empty($products[$index]['quantity'])){
                                            $products[$index]['total'] = 0;
                                        } 
                                        else{
                                            $products[$index]['total'] = $products[$index]['price'] * $products[$index]['quantity']; 
                                            if(!empty($products[$index]['tax']) && $products[$index]['tax'] > 0){
                                                $products[$index]['total'] += $products[$index]['total'] * ($products[$index]['tax'] / 100);
                                            }
                                        }
                                    @endphp
                                    <x-input-label :value="__('Product-Sub-Total:')" />
                                    <x-number-input value="{{$products[$index]['total']}}" class="block mt-1 w-full" type="number" autocomplete="off" placeholder="0.00" readonly />
                                </div>

                                <div>
                                    @if(count($products) > 1)
                                        <button type="button" wire:click="removeProductRow({{ $index }})" class="w-full bg-red-500 text-white px-3 py-2.5 rounded-md shadow-sm hover:bg-red-600 transition focus:ring-2 focus:ring-red-500/50 outline-none">
                                            Remove
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="w-full flex justify-end">
                            <div class="w-1/3 grid grid-cols-2 gap-4 sm:grid-cols-1 lg:grid-cols-2">
                                @php
                                    $grandTotal = 0;
                                @endphp
                                @foreach($products as $product)
                                <x-text-input value="Sub-Total" class="block mt-1 w-full" placeholder="Sub Total" readonly />
                                <x-number-input value="{{$product['total']}}" class="block mt-1 w-full"  placeholder="0.00" readonly />
                                    @php
                                        $grandTotal += $product['total'];
                                    @endphp
                                @endforeach
                                    <x-text-input value="Total" class="bg-gray-200 block mt-1 w-full" placeholder="Total" readonly />
                                    <x-number-input value="{{$grandTotal}}" class="block mt-1 w-full bg-gray-200"  placeholder="0.00" readonly />
                            </div>
                        </div>

                        <div>
                            <x-text-input wire:model="companyTerms" id="Company-terms" class="block mt-1 w-full" type="text" autocomplete="off" placeholder="Company Terms..." />
                            <x-input-error :messages="$errors->get('companyTerms')" class="mt-2" />
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between gap-4 mt-2">
                            <button type="button" wire:click="addProductRow" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded-md shadow-sm hover:bg-blue-700 transition focus:ring-2 focus:ring-blue-500/50 outline-none">
                                + Add Product
                            </button>

                            <div>
                            <button type="button" wire:click="cancel" class="w-full sm:w-auto bg-red-600 text-white px-6 py-2.5 rounded-md shadow-sm hover:bg-red-700 transition focus:ring-2 focus:ring-red-500/50 outline-none">
                                Cancel
                            </button>
                            <button type="button" wire:click="$dispatch('save', {print:false})" class="w-full sm:w-auto bg-green-600 text-white px-6 py-2.5 rounded-md shadow-sm hover:bg-green-700 transition focus:ring-2 focus:ring-green-500/50 outline-none">
                                Save
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