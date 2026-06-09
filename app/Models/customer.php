<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'number',
        'email',
    ];

    public function product(): HasMany
    {
        return $this->hasMany(product::class);
    }

    public function invoice(): HasMany
    {
        return $this->hasMany(invoice::class);
    }
}
