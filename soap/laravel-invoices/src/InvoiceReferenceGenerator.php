<?php

namespace Soap\Invoices;

use Carbon\Carbon;

/**
 * Generate invoice reference identification string
 * @todo format should be user settable
 * @package Soap\Invoices
 */
class InvoiceReferenceGenerator
{
    public static function generate()
    {
        $date = Carbon::now();
        return $date->format('Y-m-d') . '-' . self::generateRandomCode();
    }

    protected static function generateRandomCode()
    {
        $pool = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        return substr(str_shuffle(str_repeat($pool, 6)), 0, 6);
    }
}
