<?php

use Livewire\Component;
use App\Models\User;

new class extends Component
{
    public $id;

    public function mount(){
        User::find(auth()->user()->id)->category()->where('id',$this->id)->delete();

        $this->dispatch('category-updated');
        $this->dispatch('sendMessage',msg:'Category Deleted Successfully');
    }
};
?>

<div>
    
</div>