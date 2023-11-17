<?php

namespace Soap\Invoices\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Soap\Invoices\Tests\TestCase;
use Soap\Invoices\InvoiceReferenceGenerator;

class InvoiceReferenceTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp() : void
    {
        parent::setUp();
        $this->reference = InvoiceReferenceGenerator::generate();
        $this->date = Carbon::now();
    }

    /** Invoice reference must be 17 character long **/
    public function test_reference_must_be_17_characters_long()
    {
        $this->assertEquals(17, strlen($this->reference));
    }

    /** @test */
    public function test_reference_must_match_format()
    {
        // assert invoice reference matches format YYYY-MM-DD-XXXXXX (X = alphanumeric character)
        $list = explode('-', $this->reference);

        $this->assertEquals($list[0], $this->date->year);
        $this->assertEquals($list[1], $this->date->month);
        $this->assertEquals($list[2], $this->date->day);
        $this->assertEquals(6, strlen($list[3]));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]+$/', $list[3]);
    }

    /** @test */
    public function test_reference_cannot_contain_ambiguous_characters()
    {
        $code = substr($this->reference, -6);

        $this->assertFalse(strpos($code, '1'));
        $this->assertFalse(strpos($code, 'I'));
        $this->assertFalse(strpos($code, '0'));
        $this->assertFalse(strpos($code, 'O'));
    }

    /** test if reference must be unique */
    public function test_reference_must_be_unique()
    {
        $references = array_map(function () {
            return InvoiceReferenceGenerator::generate();
        }, range(1, 100));

        $this->assertCount(100, array_unique($references));
    }
}
