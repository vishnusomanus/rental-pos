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
        CREATE TRIGGER update_products_quantity AFTER INSERT ON payments
        FOR EACH ROW
        BEGIN
            DECLARE total_amount DECIMAL(14,4);
            DECLARE total_rec DECIMAL(14,4);
            DECLARE total_quantity INT;
            
            -- Calculate the sum of amounts in the payments table
            SELECT SUM(amount) INTO total_amount FROM payments WHERE order_id = NEW.order_id;
            
            -- Calculate the sum of prices multiplied by quantities in the order_items table
            SELECT SUM(price) INTO total_rec FROM order_items WHERE order_id = NEW.order_id;
            
            -- Update the quantity in the products table if the total amount is greater than or equal to the total quantity
            IF total_amount >= total_rec THEN
                -- Create a temporary table to hold the fetched product_id and quantity
                CREATE TEMPORARY TABLE IF NOT EXISTS temp_order_items (product_id INT, quantity INT);
                
                -- Insert the relevant rows into the temporary table
                INSERT INTO temp_order_items (product_id, quantity)
                SELECT product_id, quantity FROM order_items WHERE order_id = NEW.order_id;
                
                -- Fetch the rows from the temporary table and update the products table
                WHILE (SELECT COUNT(*) FROM temp_order_items) > 0 DO
                    SET @product_id := (SELECT product_id FROM temp_order_items LIMIT 1);
                    SET @quantity := (SELECT quantity FROM temp_order_items LIMIT 1);
                    
                    -- Update the quantity field in the products table
                    UPDATE products SET quantity = quantity + @quantity WHERE id = @product_id;
                    
                    -- Delete the processed row from the temporary table
                    DELETE FROM temp_order_items LIMIT 1;
                END WHILE;
                
                -- Drop the temporary table
                DROP TEMPORARY TABLE IF EXISTS temp_order_items;
            END IF;
        END
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
