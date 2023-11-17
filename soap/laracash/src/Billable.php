<?php

namespace Soap\Laracash;

use Soap\Invoices\Traits\Invoiceable;

trait Billable 
{
    use Invoiceable; // enables the ->invoices() Eloquent relationship

    public function invoiceFor()
    {
        
    }
}
