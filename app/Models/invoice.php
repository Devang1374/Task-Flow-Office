<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'invoice_number',
        'order_number',
        'due_date',
        'company_name',
        'company_email',
        'company_number',
        'company_address',
        'terms',
        'customer_name',
        'customer_email',
        'customer_number',
        'customer_address',
    ];
}
