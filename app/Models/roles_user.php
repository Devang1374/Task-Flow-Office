<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class roles_user extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','roles_id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
            'created_at' => 'datetime:Y-m-d'
        ];
    
}
