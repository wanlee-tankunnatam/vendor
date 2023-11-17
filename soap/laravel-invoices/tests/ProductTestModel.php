<?php

namespace Soap\Invoices\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Soap\Invoices\Traits\Invoiceable;

class ProductTestModel extends Model
{
    use Invoiceable;

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            /**
             * @var \Illuminate\Database\Eloquent\Model $model
             */
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}
