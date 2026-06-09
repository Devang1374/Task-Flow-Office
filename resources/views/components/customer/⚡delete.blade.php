<?php

use Livewire\Component;
use App\Models\User;

new class extends Component
{
    public $id;

    public function mount(){
        User::find(auth()->user()->id)->customer()->where('id',$this->id)->delete();

        $this->dispatch('customer-updated');
        $this->dispatch('sendMessage',msg:'Customer Deleted Successfully');
    }
};
?>

<div>
    
</div>