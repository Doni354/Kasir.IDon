<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DiskonPenjualan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->unsignedBigInteger('discount_id')->nullable()->after('produk_id');
            $table->boolean('discount_applied')->default(false)->after('discount_id');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_applied');
            // Jika field 'subtotal' belum ada, tambahkan juga. Misalnya:
            // $table->decimal('subtotal', 10, 2)->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_penjualans', function (Blueprint $table) {
            $table->dropColumn(['discount_id', 'discount_applied', 'discount_amount']);
            // Jika menambahkan 'subtotal' di up(), hapus juga di sini:
            // $table->dropColumn('subtotal');
        });
    }
}
