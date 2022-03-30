<?php

namespace App\Models;

use App\Enums\TeaStore;
use App\Enums\TeaType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tea extends Model
{
    use HasFactory;
    
    protected $casts = [
        'type' => TeaType::class,
        'store' => TeaStore::class,
        'offers' => 'array',
    ];

    protected $guarded = [];

    // Lägga till något fält för "last seen?", så att man kan ta bort entries som inte längre finns kvar på sidan?
}
