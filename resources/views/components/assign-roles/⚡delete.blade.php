<?php

use Livewire\Component;
use App\Models\roles_user;

new class extends Component
{
    public $role_id;
    public $user_id;

    public function mount(){
        roles_user::where('roles_id',$this->role_id)->where('user_id', $this->user_id)->delete();

        $this->dispatch('role-updated');
        $this->dispatch('sendMessage',msg:'Role Deleted Successfully');
    }
};
?>

<div>
    
</div>