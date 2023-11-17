<?php

namespace Soap\Invoices\Traits;

use Soap\Invoices\Models\InvoiceLine;

trait Invoiceable
{
    /**
     * Set the polymorphic relation.
     *
     * @return mixed
     */
    public function invoiceLines()
    {
        return $this->morphMany(InvoiceLine::class, 'invoiceable');
    }
}
