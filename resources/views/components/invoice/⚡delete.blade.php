<?php

use Livewire\Component;

new class extends Component
{
    //this variable will be set from value that is send with component call
    public $order_number;
    public $customer_id;

    //this funcion deletes records, sends message and calls initial funtion of invoice component to reflect changes
    public function mount(){
        auth()->user()->customer()->find($this->customer_id)->product()->where('order_number', $this->order_number)->delete();
        auth()->user()->invoice()->where('order_number', $this->order_number)->delete();

        $this->dispatch('invoice-updated');
        $this->dispatch('sendMessage', msg:'Invoice Deleted successfully!');
    }
};
?>

<div>
    
</div>