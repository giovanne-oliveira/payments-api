<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\UuidPrimaryKey;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, UuidPrimaryKey, SoftDeletes;


    public $incrementing = false;

    protected $fillable = [
        'id',
        'payerId',
        'payeeId',
        'amount'
    ];

    public function accountPayer() : belongsTo
    {
        return $this->belongsTo(Account::class, 'payerId');
    }

    public function accountPayee() : belongsTo
    {
        return $this->belongsTo(Account::class,'payeeId');
    }
}