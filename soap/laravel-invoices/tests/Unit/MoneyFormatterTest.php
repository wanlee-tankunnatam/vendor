<?php

namespace Soap\Invoices\Tests\Unit;

use Soap\Invoices\Tests\TestCase;
use Soap\Invoices\MoneyFormatter;

class MoneyFormatterTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
        $this->formatter = new MoneyFormatter();
    }

    /** @test */
    public function test_can_handle_negative_values()
    {
        $this->assertTrue(in_array($this->formatter->format(-123456), [
            '-₺1.234,56',
            '₺1.234,56-',
        ]));
    }

    /** @test */
    public function test_can_format_money()
    {
        $this->assertEquals('₺1.234,56', $this->formatter->format(123456));
    }

    /** @test */
    public function test_changing_the_currency_changes_the_formatting()
    {
        $this->formatter->setCurrency('USD');
        $this->assertEquals('$1.234,56', $this->formatter->format(123456));
    }

    /** @test */
    public function test_changing_the_locale_changes_the_formatting()
    {
        $this->formatter->setLocale('en_US');
        $this->formatter->setCurrency('USD');

        $this->assertEquals('$1,234.56', $this->formatter->format(123456));
    }

    /** @test */
    public function test_changing_the_currency_and_locale_changes_the_formatting()
    {
        $this->formatter->setCurrency('USD');
        $this->formatter->setLocale('en_US');
        $this->assertEquals('$1,234.56', $this->formatter->format(123456));
    }
}
