<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUpdateProductsQuantityTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER update_products_quantity AFTER INSERT ON payments FOR EACH ROW
            IF EXISTS (
                SELECT *
                FROM order_items
                WHERE order_id = NEW.order_id
            )
            THEN
                UPDATE products
                SET quantity = quantity + (
                    SELECT SUM(quantity)
                    FROM order_items
                    WHERE order_id = NEW.order_id
                )
                WHERE id IN (
                    SELECT product_id
                    FROM order_items
                    WHERE order_id = NEW.order_id
                );
            END IF
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_products_quantity');
    }
}
