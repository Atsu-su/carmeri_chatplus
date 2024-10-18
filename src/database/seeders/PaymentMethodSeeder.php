<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! PaymentMethod::exists()) {
            PaymentMethod::create(['payment_method' => 'コンビニ支払い']);
            PaymentMethod::create(['payment_method' => 'カード支払い']);
        }
    }
}
