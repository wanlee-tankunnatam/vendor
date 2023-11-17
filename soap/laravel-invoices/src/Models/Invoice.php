<?php

namespace Soap\Invoices\Models;

use Illuminate\Support\Str;
use Soap\Invoices\InvoiceReferenceGenerator;
use Soap\Invoices\Scopes\InvoiceScope;

class Invoice extends BaseModel
{

    /**
     * Invoice constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('soap.invoices.table_names.invoices'));
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new InvoiceScope());
        static::creating(function ($model) {
            /**
             * @var \Illuminate\Database\Eloquent\Model $model
             */
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }

            $model->total     = 0;
            $model->tax       = 0;
            $model->discount  = 0;
            $model->is_bill   = false;
            $model->currency  = config('soap.invoices.default_currency', 'THB');
            $model->status    = config('soap.invoices.default_status', 'concept');
            $model->reference = InvoiceReferenceGenerator::generate();
        });
    }
}
