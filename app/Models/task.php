<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(TaskFactory::class)]
class task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'caption',
        'category',
        'isActive'
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string'
        ];
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'id' => 'string'
    ];
}
