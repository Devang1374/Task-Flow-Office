<?php

use Livewire\Component;
use App\Models\roles;

new class extends Component
{
    public $id;

    public function mount(){
        roles::where('id',$this->id)->delete();

        $this->dispatch('roles-updated');
        $this->dispatch('sendMessage',msg:'Role Deleted Successfully');
    }
};
?>

<div>
    
</div>