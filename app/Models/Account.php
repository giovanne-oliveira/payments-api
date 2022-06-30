<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\UuidPrimaryKey;

class Account extends Model
{
    use HasFactory, UuidPrimaryKey;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'userId',
        'balance',
        'isActive'
    ];

    protected $attributes = [
        "balance" => 0.0
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'userId')->first();
    }
}