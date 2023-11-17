<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('soap.invoices.table_names');

        Schema::create($tableNames['invoices'], function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('related_id');
            $table->string('related_type');
            $table->bigInteger('tax')->default(0)->description('in cents');
            $table->bigInteger('total')->default(0)->description('in cents, including tax');
            $table->bigInteger('discount')->default(0)->description('in cents');
            $table->char('currency', 3);
            $table->char('reference', 17);
            $table->char('status', 16)->nullable();
            $table->text('receiver_info')->nullable();
            $table->text('sender_info')->nullable();
            $table->text('payment_info')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_bill')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['invoicable_id', 'invoicable_type']);
        });

        Schema::create($tableNames['invoice_lines'], function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('amount')->default(0)->description('in cents, including tax');
            $table->bigInteger('tax')->default(0)->description('in cents');
            $table->json('tax_details')->nullable()->default(null);
            $table->uuid('invoice_id')->index();
            $table->char('description', 255);
            $table->uuid('invoiceable_id');
            $table->string('invoiceable_type');
            $table->char('name', 255)->nullable();
            $table->bigInteger('discount')->default(0)->description('in cents');
            $table->bigInteger('quantity')->default(1);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_complimentary')->default(false);
            $table->timestamps();
            $table->softDeletes();


            $table->index(['invoiceable_id', 'invoiceable_type']);
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('invoice.table_names');

        Schema::dropIfExists($tableNames['invoice_lines']);
        Schema::dropIfExists($tableNames['invoices']);
    }
}
