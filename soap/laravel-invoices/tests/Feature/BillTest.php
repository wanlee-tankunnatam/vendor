<?php

namespace Soap\Invoices\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Soap\Invoices\Tests\TestCase;
use Soap\Invoices\Tests\CustomerTestModel;
use Soap\Invoices\Interfaces\BillServiceInterface;
use Soap\Invoices\Tests\ProductTestModel;

class BillTest extends TestCase
{

    private $bill;

    /**
     * @var ProductTestModel $product
     */
    private $product;

    /**
     * @var CustomerTestModel $customer
     */
    private $customer;

    /**
     * @var BillServiceInterface $service
     */
    private $service;


    public function setUp(): void
    {
        parent::setUp();
        $this->customer = new CustomerTestModel();
        $this->customer->save();
        $this->product = new ProductTestModel();
        $this->product->save();

        $this->service  = $this->app->make(BillServiceInterface::class);
    }


    /** @test */
    public function canCreateBill()
    {
        $new_bill = $this->service->create($this->customer)->getBill();

        $this->assertEquals('0', (string) $new_bill->total);
        $this->assertEquals('0', (string) $new_bill->tax);
        $this->assertEquals('THB', $new_bill->currency);
        $this->assertEquals('draft', $new_bill->status);
        $this->assertNotNull($new_bill->reference);
    }

    /** @test */
    public function canAddAmountExclTaxPercentageToBill()
    {
        $this->service->create($this->customer);

        $this->service->addTaxPercentage('VAT', 0.18)->addAmountExclTax($this->product, 100, 'Some description');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountExclTax($this->product, 100, 'Some description');

        $this->assertEquals('236', (string) $this->service->getBill()->total);
        $this->assertEquals('36', (string) $this->service->getBill()->tax);
    }

    /** @test */
    public function canAddAmountInclTaxPercentageToBill()
    {
        $new_bill = $this->service->create($this->customer)->getBill();

        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');

        $this->assertEquals('236', (string) $new_bill->total);
        $this->assertEquals('36', (string) $new_bill->tax);
    }

    /** @test */
    public function canAddAmountExclTaxAmountToBill()
    {
        $this->service->create($this->customer);

        $this->service->addTaxFixed('VAT', 18)->addAmountExclTax($this->product, 100, 'Some description');
        $this->service->addTaxFixed('VAT', 18)->addAmountExclTax($this->product, 100, 'Some description');

        $this->assertEquals('236', (string) $this->service->getBill()->total);
        $this->assertEquals('36', (string) $this->service->getBill()->tax);
    }

    /** @test */
    public function canAddAmountInclTaxAmountToBill()
    {
        $new_bill = $this->service->create($this->customer)->getBill();

        $this->service->addTaxFixed('VAT', 18)->addAmountInclTax($this->product, 118, 'Some description');
        $this->service->addTaxFixed('VAT', 18)->addAmountInclTax($this->product, 118, 'Some description');

        $this->assertEquals('236', (string) $new_bill->total);
        $this->assertEquals('36', (string) $new_bill->tax);
    }

    /** @test */
    public function canAddAmountExclTaxAmountAndTaxPercentageToBill()
    {
        $this->service->create($this->customer);

        $this->service->addTaxFixed('VAT', 18)->addAmountExclTax($this->product, 100, 'Some description');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountExclTax($this->product, 100, 'Some description');

        $this->assertEquals('236', (string) $this->service->getBill()->total);
        $this->assertEquals('36', (string) $this->service->getBill()->tax);
    }

    /** @test */
    public function canAddAmountInclTaxAmountAndTaxPercentageToBill()
    {
        $new_bill = $this->service->create($this->customer)->getBill();

        $this->service->addTaxFixed('VAT', 18)->addAmountInclTax($this->product, 118, 'Some description');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');

        $this->assertEquals('236', (string) $new_bill->total);
        $this->assertEquals('36', (string) $new_bill->tax);
    }

    /** @test */
    public function canAddAmountExclMultipleTaxToBill()
    {
        $new_bill = $this->service->create($this->customer);

        $this->service
            ->addTaxFixed('TAX1', 1)
            ->addTaxPercentage('TAX2', 0.18)
            ->addTaxFixed('TAX3', 30)
            ->addAmountExclTax($this->product, 100, 'Some description');

        $this->assertEquals('149', (string) $new_bill->getBill()->total);
        $this->assertEquals('49', (string) $new_bill->getBill()->tax);
    }

    /** @test */
    public function canAddAmountInclMultipleTaxToBill()
    {
        $new_bill = $this->service->create($this->customer)->getBill();

        $this->service
            ->addTaxFixed('TAX1', 1)
            ->addTaxPercentage('TAX2', 0.18)
            ->addTaxFixed('TAX3', 30)
            ->addAmountInclTax($this->product, 149, 'Some description');

        $this->assertEquals('149', (string) $new_bill->total);
        $this->assertEquals('49', (string) $new_bill->tax);
    }


    /** @test */
    public function canHandleNegativeAmounts()
    {
        $new_bill = $this->service->create($this->customer)->getBill();

        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, -118, 'Some negative amount description');

        $this->assertEquals('0', (string) $new_bill->total);
        $this->assertEquals('0', (string) $new_bill->tax);
    }

    /** @test */
    public function hasUniqueReference()
    {
        $references = array_map(function () {
            return $this->service->create($this->customer)->getBill()->reference;
        }, range(1, 100));

        $this->assertCount(100, array_unique($references));
    }

    /** @test */
    public function canGetBillView()
    {
        $this->service->create($this->customer);

        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');
        $view = $this->service->view();
        $rendered = $view->render(); // fails if view cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canGetBillPdf()
    {
        $this->service->create($this->customer);
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');
        $pdf = $this->service->pdf();  // fails if pdf cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canDownloadBillPdf()
    {
        $this->service->create($this->customer);
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');
        $download = $this->service->download(); // fails if pdf cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canFindByReference()
    {
        $new_bill = $this->service->create($this->customer)->getBill();
        $this->assertEquals($new_bill->id, $this->service->findByReference($new_bill->reference)->id);
    }

    /** @test */
    public function canFindByReferenceOrFail()
    {
        $new_bill = $this->service->create($this->customer)->getBill();
        $this->assertEquals($new_bill->id, $this->service->findByReferenceOrFail($new_bill->reference)->id);
    }

    /** @test */
    public function canFindByReferenceOrFailThrowsExceptionForNonExistingReference()
    {
        $this->expectException('Illuminate\Database\Eloquent\ModelNotFoundException');
        $this->service->findByReferenceOrFail('non-existing-reference');
    }

    /** @test */
    public function canAccessRelated()
    {
        $new_bill = $this->service->create($this->customer)->getBill();
        // Check if correctly set on bill
        $this->assertEquals(CustomerTestModel::class, $new_bill->related_type);
        $this->assertEquals($this->customer->id, $new_bill->related_id);

        // Check if related is accessible
        $this->assertNotNull($new_bill->related);
        $this->assertEquals(CustomerTestModel::class, get_class($new_bill->related));
        $this->assertEquals($this->customer->id, $new_bill->related->id);
    }

    /** @test */
    public function canSaleFree()
    {
        $new_bill = $this->service->create($this->customer)->getBill();

        $this->service->setFree()->addTaxPercentage('VAT', 0.18)->addAmountExclTax($this->product, 100, 'Free sale');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountExclTax($this->product, 100, 'Some description');

        $this->assertEquals('118', (string) $new_bill->total);
        $this->assertEquals('18', (string) $new_bill->tax);
        $this->assertEquals('118', (string) $new_bill->discount);
    }

    /** @test */
    public function canSaleComplimentary()
    {
        $new_bill = $this->service->create($this->customer)->getBill();

        $this->service->setComplimentary()->addTaxPercentage('VAT', 0.18)->addAmountExclTax($this->product, 100, 'Complimentary sale');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountExclTax($this->product, 100, 'Some description');

        $this->assertEquals('118', (string) $new_bill->total);
        $this->assertEquals('18', (string) $new_bill->tax);
        $this->assertEquals('118', (string) $new_bill->discount);
    }

    /** @test */
    public function canSaleComplimentaryAndFree()
    {
        $new_bill = $this->service->create($this->customer)->getBill();

        $this->service->setComplimentary()->addTaxPercentage('VAT', 0.18)->addAmountExclTax($this->product, 100, 'Complimentary sale');
        $this->service->setFree()->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Free sale');

        $this->assertEquals('0', (string) $new_bill->total);
        $this->assertEquals('0', (string) $new_bill->tax);
        $this->assertEquals('236', (string) $new_bill->discount);
    }

    /** @test */
    public function canSaleComplimentaryAndFreeAndRegular()
    {
        $new_bill = $this->service->create($this->customer)->getBill();

        $this->service->setComplimentary()->addTaxPercentage('VAT', 0.18)->addAmountExclTax($this->product, 100, 'Complimentary sale');
        $this->service->setFree()->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Free sale');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Regular sale');

        $this->assertEquals('118', (string) $new_bill->total);
        $this->assertEquals('18', (string) $new_bill->tax);
        $this->assertEquals('236', (string) $new_bill->discount);
    }

    /** @test */
    public function canFindByInvoicable()
    {
        $new_bill = $this->service->create($this->customer);

        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');

        $bills = $this->service->findByInvoiceable($this->product);


        $this->assertTrue($bills->where('id', $new_bill->getBill()->id)->count() === 1);
    }

    /** @test */
    public function canFindByRelated()
    {
        $new_bill = $this->service->create($this->customer);

        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');
        $this->service->addTaxPercentage('VAT', 0.18)->addAmountInclTax($this->product, 118, 'Some description');

        $bills = $this->service->findByRelated($this->customer);

        $this->assertTrue($bills->where('id', $new_bill->getBill()->id)->count() === 1);
    }
}
