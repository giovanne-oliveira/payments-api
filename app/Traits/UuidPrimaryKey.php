<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait UuidPrimaryKey
{
    public static function bootUuidPrimaryKey()
    {
        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }
}