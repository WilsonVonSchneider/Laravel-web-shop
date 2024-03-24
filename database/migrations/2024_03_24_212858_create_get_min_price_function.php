<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
        CREATE OR REPLACE FUNCTION get_min_price(user_id UUID, product_id UUID)
        RETURNS NUMERIC AS $$
        DECLARE
            user_price NUMERIC;
            product_price NUMERIC;
            contract_price NUMERIC;
        
        BEGIN
        SELECT price INTO user_price
        FROM user_contract_prices
        WHERE user_contract_prices.user_id = get_min_price.user_id AND user_contract_prices.product_id = get_min_price.product_id;

        SELECT price INTO product_price
        FROM products
        WHERE products.id = get_min_price.product_id;

        SELECT MIN(price) INTO contract_price
        FROM user_contract_prices
        WHERE user_contract_prices.product_id = get_min_price.product_id;

        RETURN LEAST(user_price, product_price, contract_price);
        END;
        $$ LANGUAGE plpgsql;
    ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS get_min_price(UUID, UUID)');
    }
};
